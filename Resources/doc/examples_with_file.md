Examples with Files
===================

Example 1
---------

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
     * @Route("/example1/encryptRespurce", name="example1_resource_encrypt")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function example1EncResourceAction(Request $request)
    {

    }
}
```