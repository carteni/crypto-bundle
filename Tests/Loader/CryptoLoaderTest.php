<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\Tests\Loader;

use Mes\Security\CryptoBundle\KeyGenerator\KeyGenerator;
use Mes\Security\CryptoBundle\Loader\CryptoLoader;

/**
 * Class CryptoLoaderTest.
 */
class CryptoLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CryptoLoader
     */
    private $loader;

    /**
     * @var KeyGenerator
     */
    private $keyGenerator;

    private $tempCryptoFile;

    public function testLoadKeyLoadsEncodedKey()
    {
        $encodedKey = $this->loader->loadKey();

        $key = $this->keyGenerator->generateFromAscii($encodedKey, $this->loader->loadSecret());

        $this->assertInstanceOf('Mes\\Security\\CryptoBundle\\Model\\KeyInterface', $key);
        $this->assertTrue(ctype_print($encodedKey), ' is printable');
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testLoadKeyThrowsExceptionBecauseKeyIsMissing()
    {
        $this->tempCryptoFile = __DIR__.'/../invalid_key.crypto';

        $handle = fopen($this->tempCryptoFile, 'w+');
        fwrite($handle, <<<'EOF'
secret=ThisIsASecret
EOF
        );
        fclose($handle);

        $loader = new CryptoLoader($this->tempCryptoFile);
        $loader->loadKey();
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testLoadKeyThrowsExceptionBecauseSecretIsMissing()
    {
        $this->tempCryptoFile = __DIR__.'/../invalid_key.crypto';

        $handle = fopen($this->tempCryptoFile, 'w+');
        fwrite($handle, <<<'EOF'
key=ThisIsAnEncodedKey
EOF
        );
        fclose($handle);

        $loader = new CryptoLoader($this->tempCryptoFile);
        $loader->loadSecret();
    }

    public function testLoadSecretLoadsSecret()
    {
        $secret = $this->loader->loadSecret();

        $this->assertSame('ThisIsASecret', $secret);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test__ConstructFailsBecauseResourceNotExists()
    {
        $fakeFile = '/var/www/fake';

        new CryptoLoader($fakeFile);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test__ConstructFailsBecauseResourceIsBadFormatted()
    {
        $this->tempCryptoFile = __DIR__.'/../invalid_key.crypto';

        $handle = fopen($this->tempCryptoFile, 'w+');
        fwrite($handle, <<<'EOF'
key:ThisIsAKey
secret:ThisIsASecret
EOF
        );
        fclose($handle);

        new CryptoLoader($this->tempCryptoFile);
    }

    protected function setUp()
    {
        $this->loader = new CryptoLoader(__DIR__.'/../key.crypto');
        $this->keyGenerator = new KeyGenerator();
    }

    protected function tearDown()
    {
        $this->loader = null;
        $this->keyGenerator = null;
        @unlink($this->tempCryptoFile);
    }
}
