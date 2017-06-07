Examples with Files
===================

Example 1
---------

```php
namespace AppBundle\Controller;

use Mes\Security\CryptoBundle\Exception\CryptoException;
use Mes\Security\CryptoBundle\Model\KeyInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/example1/encryptFile", name="example1_file_encrypt")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function example1EncFileAction(Request $request)
    {
        $str = "// Example 1: File Encryption<br /><br/>";

        try {

            /** @var KeyInterface $key */
            $key = $this->get('mes_crypto.key_manager')
                        ->getKey();

            // Create file to encrypt.
            $tmpfname = tempnam(sys_get_temp_dir(), 'CRYPTO_');

            $plainContent = "Dinanzi a me non fuor cose create se non etterne, e io etterno duro. Lasciate ogni speranza, voi ch'intrate.";

            $handle = fopen($tmpfname, "w");
            fwrite($handle, $plainContent);
            fclose($handle);

            $filename = md5(uniqid());

            $encryptedFilename = $this->getParameter('kernel.cache_dir')."/ENCRYPTED_$filename.crypto";

            // Encryption
            $this->get('mes_crypto.encryption')->encryptFileWithKey($tmpfname, $encryptedFilename, $key);

            unlink($tmpfname);

            $str .= "Plain content: $plainContent<br/><br/>";

            $str .= sprintf("Encrypted file [%s]: %s", $encryptedFilename, file_get_contents($encryptedFilename));

            // Decryption
            $decryptedFilename = $this->getParameter('kernel.cache_dir')."/DECRYPTED_$filename.crypto";

            $this->get('mes_crypto.encryption')->decryptFileWithKey($encryptedFilename, $decryptedFilename, $key);

            $str .= sprintf("<br/><br/>Decrypted file [%s]: %s", $decryptedFilename, file_get_contents($decryptedFilename));

        } catch (CryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }

        $resp = new Response(sprintf("<!DOCTYPE html><html><head><title>Example 1</title></head><body>%s</body></html>", $str));

        return $resp;
    }
}
```

Example 2 (Image Encryption)
----------------------------

```php
namespace AppBundle\Controller;

use Mes\Security\CryptoBundle\Exception\CryptoException;
use Mes\Security\CryptoBundle\Model\KeyInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/example2/encryptImage", name="example2_image_encrypt")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function example2EncFileAction(Request $request)
    {
        try {

            /** @var KeyInterface $key */
            $key = $this->get('mes_crypto.key_manager')->getKey();

            $imageToEncrypt = new File($this->getParameter('kernel.root_dir').'/Resources/Lighthouse.jpg');
            $imageToEncryptName = pathinfo($imageToEncrypt->getFilename(), PATHINFO_FILENAME);
            $imageToEncryptExt = $imageToEncrypt->getExtension();

            $encryptedImageFilename = $this->getParameter('kernel.cache_dir')."/ENCRYPTED_$imageToEncryptName.$imageToEncryptExt";

            // Image encryption
            $this->get('mes_crypto.encryption')->encryptFileWithKey(
                    $imageToEncrypt->getRealPath(),
                    $encryptedImageFilename,
                    $key);

            // Image decryption
            $tmpfname = tempnam($this->getParameter('kernel.cache_dir'), 'CRYPTO_');
            $this->get('mes_crypto.encryption')->decryptFileWithKey($encryptedImageFilename, $tmpfname, $key);

            ob_start();
                readfile($tmpfname);

            $imgData = base64_encode(ob_get_clean());
            $img = "<img src= 'data:image/jpeg;base64, $imgData' />";

            unlink($tmpfname);


        } catch (CryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }

        $html = <<<HTML
<!DOCTYPE html>
<html>
    <head><title>Image Encryption</title></head>
        <body>$img</body>
</html>
HTML;

        return new Response($html);
    }
}
```
