<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\Tests\DependencyInjection;

use Mes\Security\CryptoBundle\DependencyInjection\MesCryptoExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * Class MesCryptoExtensionTest.
 */
class MesCryptoExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder */
    private $configuration;

    protected function setup()
    {
        $this->configuration = new ContainerBuilder();
        $this->configuration->setParameter('kernel.root_dir', dirname(dirname(__DIR__)));
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }

    public function testContainerWithDefaultValues()
    {
        $loader = new MesCryptoExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);

        $this->assertHasDefinition('mes_crypto.raw_key');
        $this->assertSame('Defuse\Crypto\Key', $this->configuration->findDefinition('mes_crypto.raw_key')
                                                                   ->getClass(), 'Defuse\Crypto\Key class is correct');

        $this->assertNotHasDefinition('mes_crypto.loader');
    }

    public function testContainerWithRandomKeyAndSecret()
    {
        $loader = new MesCryptoExtension();
        $config = $this->getConfigWithRandomKeyAndSecret();
        $loader->load(array($config), $this->configuration);

        $this->assertHasDefinition('mes_crypto.raw_key');
        $this->assertSame('Defuse\Crypto\KeyProtectedByPassword', $this->configuration->findDefinition('mes_crypto.raw_key')
                                                                                      ->getClass(), 'KeyProtectedByPassword class is correct');

        $this->assertNotHasDefinition('mes_crypto.loader');
    }

    public function testContainerWithInternalKeyAndSecret()
    {
        $loader = new MesCryptoExtension();
        $config = $this->getConfigWithInternalKeyAndSecret();
        $loader->load(array($config), $this->configuration);

        $this->assertHasDefinition('mes_crypto.raw_key');
        $this->assertSame('Defuse\Crypto\KeyProtectedByPassword', $this->configuration->findDefinition('mes_crypto.raw_key')
                                                                                      ->getClass(), 'Defuse\Crypto\KeyProtectedByPassword class is correct');
        $this->assertNotHasDefinition('mes_crypto.loader');
    }

    public function testContainerWithExternalKeyAndSecret()
    {
        $loader = new MesCryptoExtension();
        $config = $this->getConfigWithExternalKey();
        $loader->load(array($config), $this->configuration);

        $this->assertHasDefinition('mes_crypto.raw_key');
        $this->assertSame('Defuse\Crypto\KeyProtectedByPassword', $this->configuration->findDefinition('mes_crypto.raw_key')
                                                                                      ->getClass(), 'Defuse\Crypto\KeyProtectedByPassword class is correct');
        $this->assertHasDefinition('mes_crypto.loader');
    }

    public function testContainerWithFullConfigWithExternalKey()
    {
        $loader = new MesCryptoExtension();
        $config = $this->getFullConfigWithExternalKey();
        $loader->load(array($config), $this->configuration);

        $this->assertHasDefinition('mes_crypto.raw_key');
        $this->assertSame('Defuse\Crypto\KeyProtectedByPassword', $this->configuration->findDefinition('mes_crypto.raw_key')
                                                                                      ->getClass(), 'Defuse\Crypto\KeyProtectedByPassword class is correct');
        $this->assertHasDefinition('mes_crypto.loader');

        $this->assertSame('custom_key_storage_service', (string) $this->configuration->getAlias('mes_crypto.key_storage'), 'custom_key_storage_service is correct alias');
        $this->assertSame('custom_key_generator_service', (string) $this->configuration->getAlias('mes_crypto.key_generator'), 'custom_key_generator_service is correct alias');
        $this->assertSame('custom_encryption_service', (string) $this->configuration->getAlias('mes_crypto.encryption'), 'custom_encryption_service is correct alias');
    }

    /**
     * @return array
     */
    private function getEmptyConfig()
    {
        return array();
    }

    /**
     * @return mixed
     */
    private function getConfigWithRandomKeyAndSecret()
    {
        $yaml = <<<'EOF'
secret: "ThisIsASecret"
EOF;

        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @return mixed
     */
    private function getConfigWithInternalKeyAndSecret()
    {
        $yaml = <<<'EOF'
key: "ThisIsEncodedKey"
secret: "ThisIsASecret"
EOF;

        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @return mixed
     */
    private function getConfigWithExternalKey()
    {
        $cryptoFile = dirname(__DIR__).'/key.crypto';
        $yaml = <<<EOF
key: $cryptoFile
external_secret: true
EOF;

        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @return mixed
     */
    private function getFullConfigWithExternalKey()
    {
        $cryptoFile = dirname(__DIR__).'/key.crypto';
        $yaml = <<<EOF
key: $cryptoFile
external_secret: true
key_storage: custom_key_storage_service
key_generator: custom_key_generator_service
encryption: custom_encryption_service
EOF;

        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @return mixed
     */
    private function getConfigWithExternalLoader()
    {
        $yaml = <<<'EOF'
loader:
    enabled; true
EOF;

        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @param string $id
     */
    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    /**
     * @param string $id
     */
    private function assertNotHasDefinition($id)
    {
        $this->assertFalse(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }
}
