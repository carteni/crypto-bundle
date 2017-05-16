<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\Tests;

use Defuse\Crypto\Exception\IOException;
use Mes\Security\CryptoBundle\Encryption;
use Mes\Security\CryptoBundle\EncryptionInterface;
use Mes\Security\CryptoBundle\KeyGenerator\KeyGenerator;
use Mes\Security\CryptoBundle\KeyGenerator\KeyGeneratorInterface;
use Mes\Security\CryptoBundle\Model\KeyInterface;

/**
 * Class EncryptionResourceTest.
 */
class EncryptionResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EncryptionInterface
     */
    private $encryption;

    /**
     * @var KeyGeneratorInterface
     */
    private $generator;

    private $plainContentResourceFile;
    private $plainContentResourceHandle;

    private $cipherContentResourceFile;
    private $cipherContentResourceHandle;

    /* ===========================================
     *
     * EncryptionInterface::EncryptResourceWithKey
     *
     * ===========================================
     */

    /**
     * @return array
     */
    public function testEncryptResourceWithKeyEncryptsAnHandle()
    {
        $this->createPlainContentFile();

        /** @var KeyInterface $key */
        $key = $this->generator->generate('ThisIsASuperSecret');

        $this->plainContentResourceHandle = $this->openHandle($this->plainContentResourceFile, 'rb');
        $this->cipherContentResourceHandle = $this->openHandle($this->cipherContentResourceFile, 'wb');

        $this->encryption->encryptResourceWithKey($this->plainContentResourceHandle, $this->cipherContentResourceHandle, $key);

        $this->assertFileExists($this->cipherContentResourceFile, sprintf('File %s not exists', $this->cipherContentResourceFile));
        $this->assertGreaterThan(0, (new \SplFileInfo($this->cipherContentResourceFile))->getSize());

        $this->closeHandle($this->plainContentResourceHandle);
        $this->closeHandle($this->cipherContentResourceHandle);
        unlink($this->plainContentResourceFile);

        return array(
            'key' => $key->getEncoded(),
            'secret' => $key->getSecret(),
        );
    }

    /**
     * @depends testEncryptResourceWithKeyEncryptsAnHandle
     *
     * @param $args
     */
    public function testDecryptResourceWithKeyDecryptsAnHandle($args)
    {
        $this->createPlainContentFile(false);

        /** @var KeyInterface $key */
        $key = $this->generator->generateFromAscii($args['key'], $args['secret']);

        $this->cipherContentResourceHandle = fopen($this->cipherContentResourceFile, 'rb');
        $this->plainContentResourceHandle = fopen($this->plainContentResourceFile, 'wb');

        $this->encryption->decryptResourceWithKey($this->cipherContentResourceHandle, $this->plainContentResourceHandle, $key);

        $lines = file($this->plainContentResourceFile);

        $this->assertCount(5, $lines);
        $this->assertSame('Line 1', trim($lines[0]));
        $this->assertSame('Line 2', trim($lines[1]));
        $this->assertSame('Line 3', trim($lines[2]));
        $this->assertSame('Line 4', trim($lines[3]));
        $this->assertSame('Line 5', trim($lines[4]));
        $this->assertGreaterThan(0, (new \SplFileInfo($this->cipherContentResourceFile))->getSize());

        $this->closeHandle($this->plainContentResourceHandle);
        $this->closeHandle($this->cipherContentResourceHandle);
    }

    /**
     * @expectedException \Defuse\Crypto\Exception\IOException
     */
    public function testEncryptResourceThrowsException()
    {
        $this->createPlainContentFile();
        $this->plainContentResourceHandle = fopen($this->plainContentResourceFile, 'rb');

        try {
            $this->encryption->encryptResourceWithKey($this->plainContentResourceHandle, $this->cipherContentResourceFile, $this->generator->generate());
        } catch (IOException $e) {
            $this->closeHandle($this->plainContentResourceHandle);
            throw $e;
        }
    }

    public function testEncryptResourceWithPasswordEncryptsAnHandle()
    {
        $this->createPlainContentFile();

        $this->plainContentResourceHandle = $this->openHandle($this->plainContentResourceFile, 'rb');
        $this->cipherContentResourceHandle = $this->openHandle($this->cipherContentResourceFile, 'wb');

        $this->encryption->encryptResourceWithPassword($this->plainContentResourceHandle, $this->cipherContentResourceHandle, 'SuperPa$$word');

        $this->assertFileExists($this->cipherContentResourceFile, sprintf('File %s not exists', $this->cipherContentResourceFile));
        $this->assertGreaterThan(0, (new \SplFileInfo($this->cipherContentResourceFile))->getSize());

        $this->closeHandle($this->plainContentResourceHandle);
        $this->closeHandle($this->cipherContentResourceHandle);
        unlink($this->plainContentResourceFile);
    }

    /**
     * @depends testEncryptResourceWithKeyEncryptsAnHandle
     *
     * @param $args
     */
    public function testDecryptResourceWithPasswordDecryptsAnHandle($args)
    {
        $this->createPlainContentFile(false);

        $this->cipherContentResourceHandle = fopen($this->cipherContentResourceFile, 'rb');
        $this->plainContentResourceHandle = fopen($this->plainContentResourceFile, 'wb');

        $this->encryption->decryptResourceWithPassword($this->cipherContentResourceHandle, $this->plainContentResourceHandle, 'SuperPa$$word');

        $lines = file($this->plainContentResourceFile);

        $this->assertCount(5, $lines);
        $this->assertSame('Line 1', trim($lines[0]));
        $this->assertSame('Line 2', trim($lines[1]));
        $this->assertSame('Line 3', trim($lines[2]));
        $this->assertSame('Line 4', trim($lines[3]));
        $this->assertSame('Line 5', trim($lines[4]));
        $this->assertGreaterThan(0, (new \SplFileInfo($this->cipherContentResourceFile))->getSize());

        $this->closeHandle($this->plainContentResourceHandle);
        $this->closeHandle($this->cipherContentResourceHandle);

        unlink($this->plainContentResourceFile);
        unlink($this->cipherContentResourceFile);
    }

    protected function setUp()
    {
        $this->encryption = new Encryption();
        $this->generator = new KeyGenerator();
        $this->cipherContentResourceFile = __DIR__.'/cipherContentResourceFile.crypto';
    }

    protected function tearDown()
    {
        $this->encryption = null;
        $this->generator = null;
    }

    private function createPlainContentFile($pushContent = true)
    {
        $this->plainContentResourceFile = __DIR__.'/plainContentResourceFile.crypto';
        if (!file_exists($this->plainContentResourceFile)) {
            if ($pushContent) {
                file_put_contents($this->plainContentResourceFile, <<<'EOT'
Line 1
Line 2
Line 3
Line 4
Line 5
EOT
                );
            } else {
                touch($this->plainContentResourceFile);
            }
        }
    }

    /**
     * @param $filename
     * @param $mode
     *
     * @return resource
     */
    private function openHandle($filename, $mode)
    {
        return fopen($filename, $mode);
    }

    /**
     * @param resource $handle
     */
    private function closeHandle($handle)
    {
        fclose($handle);
    }
}
