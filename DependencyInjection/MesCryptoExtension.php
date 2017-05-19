<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class MesCryptoExtension.
 */
class MesCryptoExtension extends ConfigurableExtension implements CompilerPassInterface
{
    /**
     * @var bool
     */
    private $loaderEnabled = false;

    /**
     * @var Expression
     */
    private $loadKey;

    /**
     * @var Expression
     */
    private $loadSecret;

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getNamespace()
    {
        return 'http://multimediaexperiencestudio.it/schema/dic/crypto';
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string|bool
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$this->loaderEnabled) {
            return;
        }

        foreach ($container->findTaggedServiceIds('mes_crypto.loader') as $id => $attributes) {
            foreach ($attributes as $attribute) {

                // Sets the loader resource.
                $container->findDefinition($id)
                          ->addMethodCall('setResource', array($attribute['resource']));

                // Load secret from loader.
                $container->getDefinition('mes_crypto.key_manager_wrapper')
                          ->setMethodCalls(array())
                          ->addMethodCall('setSecret', array($this->loadSecret));

                // Sets Defuse KeyProtectedByPassword.
                $container->findDefinition('mes_crypto.raw_key')
                          ->setClass('Defuse\Crypto\KeyProtectedByPassword')
                          ->setArguments(array($this->loadKey))
                          ->setFactory(array(
                              'Defuse\Crypto\KeyProtectedByPassword',
                              'loadFromAsciiSafeString',
                          ));

                // Sets the key
                $this->createKeyDefinition($container, $this->loadSecret);
            }

            $container->setAlias('mes_crypto.loader', $id);

            return;
        }
    }

    /**
     * Configures the passed container according to the merged configuration.
     *
     * @param array            $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($this->isConfigEnabled($container, $mergedConfig['loader'])) {
            $this->loaderEnabled = true;
        }

        $this->createKeyStorage($mergedConfig, $container);
        $this->createKeyGenerator($mergedConfig, $container);
        $this->generateKey($mergedConfig, $container);
        $this->createEncryption($mergedConfig, $container);
    }

    /**
     * @param $config
     * @param ContainerBuilder $container
     */
    private function createKeyStorage($config, ContainerBuilder $container)
    {
        // Key Storage
        if (null !== $config['key_storage']) {
            $container->setAlias(new Alias('mes_crypto.key_storage', false), $config['key_storage']);
        }
    }

    /**
     * @param $config
     * @param ContainerBuilder $container
     */
    private function createKeyGenerator($config, ContainerBuilder $container)
    {
        // Key Generator
        if (null !== $config['key_generator']) {
            $container->setAlias(new Alias('mes_crypto.key_generator', false), $config['key_generator']);
        }
    }

    /**
     * @param $config
     * @param ContainerBuilder $container
     */
    private function generateKey($config, ContainerBuilder $container)
    {
        $secret = $config['secret'];
        $ext_secret = $config['external_secret'];
        $key = $config['key']['key'];
        $path = $config['key']['path'];

        // Secret Expression.
        $this->loadSecret = new Expression('service("mes_crypto.loader").loadSecret()');

        // Key Expression.
        $this->loadKey = new Expression('service("mes_crypto.loader").loadKey()');

        // Conditions.
        $createRandomKey = (null === $key) && (null === $path);

        $keyIsExternal = (true === !$createRandomKey) && (null !== $path) && (null === $key);

        $secretExists = (null !== $secret) || (true === $ext_secret);
        $secretIsExternal = (true === $secretExists) && (true === $ext_secret) && (null === $secret);

        $rawKeyDefinition = new Definition();
        $defuseKey = $secretExists ? 'Defuse\Crypto\KeyProtectedByPassword' : 'Defuse\Crypto\Key';

        // Creates a Key from an encoded version.
        if (!$createRandomKey) {
            if ($keyIsExternal) {
                // Sets the .crypto file path to CryptoLoader.
                $this->setCryptoLoaderResource($container, $path);
            }

            // Reads encoded key from configuration file if key is not external.
            $rawKeyDefinition->setClass($defuseKey)
                             ->setFactory(array(
                                 $defuseKey,
                                 'loadFromAsciiSafeString',
                             ));
            $rawKeyDefinition->setArguments(array(
                $keyIsExternal ? $this->loadKey : $key,
            ));
        } else {
            $rawKeyDefinition->setClass($defuseKey)
                             ->setArguments($secretExists ? array($secretIsExternal ? $this->loadSecret : $secret) : array())
                             ->setFactory($secretExists ? array(
                                 'Defuse\Crypto\KeyProtectedByPassword',
                                 'createRandomPasswordProtectedKey',
                             ) : array(
                                 'Defuse\Crypto\Key',
                                 'createNewRandomKey',
                             ));
        }

        if (($createRandomKey || (!$createRandomKey && !$keyIsExternal)) && !$this->loaderEnabled) {
            $container->removeAlias('mes_crypto.loader');
        }

        $container->setDefinition('mes_crypto.raw_key', $rawKeyDefinition)
                  ->setPublic(false);

        // Key
        $this->createKeyDefinition($container, $secretExists ? ($secretIsExternal ? $this->loadSecret : $secret) : null);

        $keyManagerDefinition = $container->findDefinition('mes_crypto.key_manager_wrapper');

        // Save the generated key.
        $keyManagerDefinition->addMethodCall('setKey', array(new Reference('mes_crypto.key')));

        // Save the secret.
        if ($secretExists) {
            $keyManagerDefinition->addMethodCall('setSecret', array(
                $secretExists ? ($secretIsExternal ? $this->loadSecret : $secret) : null,
            ));
        }
    }

    private function createKeyDefinition(ContainerBuilder $container, $secret)
    {
        $keyDefinition = new Definition('Mes\Security\CryptoBundle\Model\Key', array(
            new Reference('mes_crypto.raw_key'),
            $secret,
        ));
        $keyDefinition->setFactory(array(
            'Mes\Security\CryptoBundle\Model\Key',
            'create',
        ))
                      ->setPublic(false);

        $container->setDefinition('mes_crypto.key', $keyDefinition);
    }

    /**
     * @param $config
     * @param ContainerBuilder $container
     */
    private function createEncryption($config, ContainerBuilder $container)
    {
        if (null !== $config['encryption']) {
            $container->setAlias(new Alias('mes_crypto.encryption'), $config['encryption']);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param $resource
     *
     * @return Definition
     */
    private function setCryptoLoaderResource(ContainerBuilder $container, $resource)
    {
        $container->findDefinition('mes_crypto.loader')
                  ->addMethodCall('setResource', array($resource));
    }
}
