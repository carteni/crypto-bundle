<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\Tests\KeyGenerator;

use Mes\Security\CryptoBundle\KeyGenerator\KeyGenerator;

/**
 * Class KeyGeneratorTest.
 */
class KeyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KeyGenerator;
     */
    private $keyGenerator;

    protected function setUp()
    {
        $this->keyGenerator = new KeyGenerator();
    }

    protected function tearDown()
    {
        $this->keyGenerator = null;
    }

    public function testGenerateGeneratesKey()
    {
        $key = $this->keyGenerator->generate();

        $this->assertInstanceOf('Mes\Security\CryptoBundle\Model\KeyInterface', $key);
        $this->assertInstanceOf('\Defuse\Crypto\Key', $key->getRawKey());
    }

    public function testGenerateGeneratesKeyWithSecret()
    {
        $key = $this->keyGenerator->generate('ThisIsASecretPassword');

        $this->assertInstanceOf('Mes\Security\CryptoBundle\Model\KeyInterface', $key);
        $this->assertInstanceOf('\Defuse\Crypto\KeyProtectedByPassword', $key->getRawKey());
        $this->assertSame('ThisIsASecretPassword', $key->getSecret());
    }

    public function testGenerateFromAsciiGeneratesKeyFromEncodedKey()
    {
        $key_encoded = 'def10000def50200bc2f46c72339246cd4a5fbdff83f13402a68480c2aff1d2345ed9c4b20b2e866651b1710eb62fa043cb2c1188db7a1241e33cba2ff53f574ffed9031d9d34edf56ef740b0825c632fc1e6f8dc09c513024fa09ac610729dd27f84f57bb1246887a66b71892701cfd8b78a98b09d23335531bab2c43105ee4fa45a183a658dc3d415655c45e086666710365bd765a19a06d57f36bb70b58271dd79354940cd44512c0f8663cdb9f9aff27a140558b4a63f43da0474b19cb6127d8623a84cb72e225786ef0b9c0f4e3c9763489d46bac4407f4cf549f68c5d38a922db15728040a7b36b12bc965e0cfa9ce78075afc8c56c964c9c7dff3a6bc';
        $secret = 'a0c7505086eb37713db511063c6d4253c174a8b8';
        $keyFromAscii = $this->keyGenerator->generateFromAscii($key_encoded, $secret);

        $this->assertInstanceOf('Mes\Security\CryptoBundle\Model\KeyInterface', $keyFromAscii);
        $this->assertInstanceOf('\Defuse\Crypto\KeyProtectedByPassword', $keyFromAscii->getRawKey());
        $this->assertSame($secret, $keyFromAscii->getSecret());
        $this->assertSame($key_encoded, $keyFromAscii->getEncoded());
    }

    /**
     * @expectedException \Defuse\Crypto\Exception\BadFormatException
     */
    public function testGeneratesFromAsciiThrowsExceptionBecauseEncodedKeyIsCorrupted()
    {
        $key_encoded = 'Zef10000def50200bc2f46c72339246cd4a5fbdff83f13402a68480c2aff1d2345ed9c4b20b2e866651b1710eb62fa043cb2c1188db7a1241e33cba2ff53f574ffed9031d9d34edf56ef740b0825c632fc1e6f8dc09c513024fa09ac610729dd27f84f57bb1246887a66b71892701cfd8b78a98b09d23335531bab2c43105ee4fa45a183a658dc3d415655c45e086666710365bd765a19a06d57f36bb70b58271dd79354940cd44512c0f8663cdb9f9aff27a140558b4a63f43da0474b19cb6127d8623a84cb72e225786ef0b9c0f4e3c9763489d46bac4407f4cf549f68c5d38a922db15728040a7b36b12bc965e0cfa9ce78075afc8c56c964c9c7dff3a6bc';
        $secret = 'a0c7505086eb37713db511063c6d4253c174a8b8';
        $this->keyGenerator->generateFromAscii($key_encoded, $secret);
    }
}
