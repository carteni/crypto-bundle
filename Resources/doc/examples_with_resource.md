Examples with Resources
=======================

Example 1
---------

```php
namespace AppBundle\Controller;

use Mes\Security\CryptoBundle\Exception\CryptoException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class CryptoController
 *
 * @package AppBundle\Controller
 *
 * @Route("/crypto")
 */
class CryptoController extends Controller
{
	/**
	 * @Route("/example1/encryptResourceWithPassword", name="example1_resource_pwd_encrypt")
	 *
	 * @return Response
	 * @throws CryptoException
	 */
 	public function example1EncResourceAction()
 	{
 		// Encryption.
 		$password = 'MySuperSecretPa$$word';

		// Create srcFile.txt file in this directory with following line:
		// ---> This is a cipher text <---

 		$srcName 	= __DIR__. '/srcFile.txt';
 		$destName 	= __DIR__ . '/destFile.dest';
 		$srcHandle 	= fopen($srcName, 'r');
 		$destHandle	= fopen($destName, 'w');

 		$this->get('mes_crypto.encryption')->encryptResourceWithPassword($srcHandle, $destHandle, $password);

 		fclose($srcHandle);
 		fclose($destHandle);

 		// Decryption.
 		$src2Handle  = fopen($destName, 'r');
 		$dest2Handle = fopen(__DIR__ . '/dest2.dest', 'w');

 		$this->get('mes_crypto.encryption')->decryptResourceWithPassword($src2Handle, $dest2Handle, $password);
 		fclose($src2Handle);
 		fclose($dest2Handle);

 		return new Response(hash_equals(md5_file($srcName), md5_file(__DIR__ . '/dest2.dest')) ? 'Equals' : 'Not Equals');
 	}
}
```

Example 2
---------

```php
namespace AppBundle\Controller;

use Mes\Security\CryptoBundle\Exception\CryptoException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class CryptoController
 *
 * @package AppBundle\Controller
 *
 * @Route("/crypto")
 */
class CryptoController extends Controller
{
	/**
	 * @Route("/example2/encryptResourceWithKey", name="example2_resource_key_encrypt")
	 *
	 * @return Response
	 * @throws CryptoException
	 */
	public function example2EncResourceAction()
	{
		$key = $this->get('mes_crypto.key_manager')->getKey();

		$srcHandle = fopen('php://temp', 'r+');
		fputs($srcHandle, '--> This is a cipher text <--');
		rewind($srcHandle);

		$destHandle = fopen('php://temp', 'w+');

		$this->get('mes_crypto.encryption')->encryptResourceWithKey($srcHandle, $destHandle, $key);
		rewind($srcHandle);
		rewind($destHandle);

		// Decryption.
		$dest2Handle = fopen('php://temp', 'w+');

		$this->get('mes_crypto.encryption')->decryptResourceWithKey($destHandle, $dest2Handle, $key);
		rewind($dest2Handle);

		return new Response(hash_equals(md5(stream_get_contents($srcHandle)), md5(stream_get_contents($dest2Handle)))
				? 'Equals'
				: 'Not Equals');
	}
}
```
