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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class MesCryptoExtension.
 */
class MesCryptoExtension extends ConfigurableExtension
{
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
     * Configures the passed container according to the merged configuration.
     *
     * @param array            $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

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
        // Secret Factory Reference.
        $loadSecret = $this->getSecretFactoryReference();
        // Key Factory Reference.
        $loadKey = $this->getKeyFactoryReference();

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
                $keyIsExternal ? $loadKey : $key,
            ));
        } else {
            $rawKeyDefinition->setClass($defuseKey)
                             ->setArguments($secretExists ? array($secretIsExternal ? $loadSecret : $secret) : array())
                             ->setFactory($secretExists ? array(
                                 'Defuse\Crypto\KeyProtectedByPassword',
                                 'createRandomPasswordProtectedKey',
                             ) : array(
                                 'Defuse\Crypto\Key',
                                 'createNewRandomKey',
                             ));
        }

        if ($createRandomKey || (!$createRandomKey && !$keyIsExternal)) {
            $container->removeDefinition('mes_crypto.crypto_loader');
        }

        $container->setDefinition('mes_crypto.raw_key', $rawKeyDefinition)
                  ->setPublic(false);

        // Key
        $keyDefinition = new Definition('Mes\Security\CryptoBundle\Model\Key', array(
            new Reference('mes_crypto.raw_key'),
            $secretExists ? ($secretIsExternal ? $loadSecret : $secret) : null,
        ));
        $keyDefinition->setFactory(array(
            'Mes\Security\CryptoBundle\Model\Key',
            'create',
        ));
        $container->setDefinition('mes_crypto.key', $keyDefinition)
                  ->setPublic(false);

        $keyManagerDefinition = $container->getDefinition('mes_crypto.key_manager_wrapper');

        // Save the generated key.
        $keyManagerDefinition->addMethodCall('setKey', array(new Reference('mes_crypto.key')));

        // Save the secret.
        if ($secretExists) {
            $keyManagerDefinition->addMethodCall('setSecret', array(
                $secretExists ? ($secretIsExternal ? $loadSecret : $secret) : null,
            ));
        }
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
        $container->getDefinition('mes_crypto.crypto_loader')
                  ->replaceArgument(0, $resource);
    }

    /**
     * @return Reference
     */
    private function getSecretFactoryReference()
    {
        // Returns secret factory Reference.
        return new Reference('mes_crypto.secret_factory');
    }

    /**
     * @return Reference
     */
    private function getKeyFactoryReference()
    {
        // Returns key factory Reference.
        return new Reference('mes_crypto.key_factory');
    }
}
