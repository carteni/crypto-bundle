Examples
========

Console commands
----------------

```sh
# Generates and prints a key
$ bin/console mes:crypto:generate-key
```

```sh
# Generates and saves a key
$ bin/console mes:crypto:generate-key --dir /home/vagrant/key.crypto
```

```sh
# Generates a secret
$ bin/console mes:crypto:generate-secret
```

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
	 * @Route("/example1/encrypt", name="example1_encrypt")
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Mes\Security\CryptoBundle\Exception\CryptoException
	 */
	public function example1EncAction(Request $request)
	{
		$str = "";

		try {
			$str .= "// Example 1 Encryption<br />";

			$sensitiveData = "Dinanzi a me non fuor cose create se non etterne, e io etterno duro. Lasciate ogni speranza, voi ch'intrate.";

			$str .= "<br />* Sensitive data: {$sensitiveData}<br />";

			/**
			 * @var KeyInterface $key
			 */
			$key = $this->get('mes_crypto.key_manager')->getKey();

			$str .= "Encoded Key: {$key->getEncoded()}<br />";

			$cipherText = $this->get('mes_crypto.encryption')->encryptWithKey($sensitiveData, $key);

			$str .= "* Ciphertext: {$cipherText}<br />";

			// Save ciphertext.
			$request->getSession()->set('cipherText', $cipherText);
		} catch (CryptoException $ex) {
			throw new CryptoException($ex->getMessage());
		}

		$str .= "<br/><a href='".$this->generateUrl("example1_decrypt")."'>Example 1 / Decryption</a><br />";

		$resp = new Response(sprintf("<!DOCTYPE html><html><head><title>Example 1</title></head><body>%s</body></html>", $str));

		return $resp;
	}

	/**
	 * @Route("/example1/decrypt", name="example1_decrypt")
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Mes\Security\CryptoBundle\Exception\CryptoException
	 */
	public function example1DecAction(Request $request)
	{
		$str = "// Example 1 Decryption<br />";

		$cipherText = $request->getSession()->get('cipherText');

		try {
			/**
			 * @var KeyInterface $key
			 */
			$key = $this->get('mes_crypto.key_manager')->getKey();

			$str .= "<br />* Sensitive decrypted data: " . $this->get('mes_crypto.encryption')->decryptWithKey($cipherText, $key);
		} catch (CryptoException $ex) {
			throw new CryptoException($ex->getMessage());
		}

		$str .= "<br /><br/><a href='" . $this->generateUrl("example1_encrypt") . "'>Back to Example 1 / Encryption</a><br />";

		$resp = new Response(sprintf("<!DOCTYPE html><html><head><title>Example 1</title></head><body>%s</body></html>", $str));

		return $resp;
	}
}
```

Example 2
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
	 * @Route("/example2/encrypt", name="example2_encrypt")
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Mes\Security\CryptoBundle\Exception\CryptoException
	 */
	public function example2EncAction(Request $request)
	{
		// Create user.
		$user = new \stdClass();
		$user->username = "John Doe";
		// hash('sha512', 'password');
		$user->password = "b109f3bbbc244eb82441917ed06d618b9008dd09b3befd1b5e07394c706a8bb980b1d7785e5976ec049b46df5f1326af5a2ea6d103fd07c95385ffab0cacbc86";

		/**
		 * Generates a key based on the user's password.
		 *
		 * @var KeyInterface $key
		 */
		$key = $this->get('mes_crypto.key_manager')->generate($user->password);

		// Save encoded key.
		$user->key = $key->getEncoded();

		$sensitiveData = "Ed ecco verso noi venir per nave un vecchio, bianco per antico pelo, gridando: \"Guai a voi, anime prave!.";

		$user->sensitiveData = $this->get('mes_crypto.encryption')->encryptWithKey($sensitiveData, $key);

		// Save user.
		$request->getSession()->set('user', $user);

		$str = "<br />Sensitive Data: " . $sensitiveData;
		$str .= "<br />Sensitive Encrypted Data: " . $user->sensitiveData;
		$str .= "<br /><br/><a href='" . $this->generateUrl("example2_decrypt") . "'>Example 2 / Decryption</a><br />";

		$resp = new Response(sprintf("<!DOCTYPE html><html><head><title>Example 2</title></head><body>%s</body></html>", "{$user->username} Created.".$str));

		return $resp;
	}

	/**
	 * @Route("/example2/decrypt", name="example2_decrypt")
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Mes\Security\CryptoBundle\Exception\CryptoException
	 */
	public function example2DecAction(Request $request)
	{
		$user = $request->getSession()->get('user');

		/**
		 * Generates a key from the encoded key and from the user's password.
		 *
		 * @var KeyInterface $key
		 */
		$key = $this->get('mes_crypto.key_manager')->generateFromAscii($user->key, $user->password);

		try {
			$sensitiveDecryptedData = $this->get('mes_crypto.encryption')->decryptWithKey($user->sensitiveData, $key);
		} catch(CryptoException $ex) {
			throw new CryptoException($ex->getMessage());
		}

		$str = "User: " . $user->username;
		$str .= "<br />Sensitive Data: " . $sensitiveDecryptedData;

		$str .= "<br /><br/><a href='" . $this->generateUrl("example2_encrypt") . "'>Example 2 / Encryption</a><br />";

		$resp = new Response(sprintf("<!DOCTYPE html><html><head><title>Example 2</title></head><body>%s</body></html>", $str));

		return $resp;
	}
}
```

Example 3
---------

### Encryption of Session Data

**Warning:** If you choose to use the `EncryptedSessionProxy` solution, might receive
the **Invalid Header** error message.
This issue could be caused by a wrong configuration.
Make sure to have the same configuration as the key generated.
For example, if the key is generated with an authentication secret,
the configuration must define that authentication secret as well, or if the key
is generated without a secret, the configuration must define a key without
authentication secret as well.

```yaml
# app/config/config.yml
framework:
    session:
        handler_id:  app.session_handler
```

```yaml
# app/config/services.yml
services:
    app.session_handler:
        class: AppBundle\Session\EncryptedSessionProxy
        arguments: ["@session.handler.native_file", "@mes_crypto.encryption", "@mes_crypto.key_manager"]
```

```php
namespace AppBundle\Session;

use Mes\Security\CryptoBundle\EncryptionInterface;
use Mes\Security\CryptoBundle\Exception\CryptoException;
use Mes\Security\CryptoBundle\KeyManagerInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

class EncryptedSessionProxy extends SessionHandlerProxy
{
	/**
	 * @var EncryptionInterface
	 */
	private $encryption;

	/**
	 * @var KeyManagerInterface
	 */
	private $keyManager;

	public function __construct(\SessionHandlerInterface $handler, EncryptionInterface $encryption, KeyManagerInterface $keyManager)
	{
		$this->encryption = $encryption;

		$this->keyManager = $keyManager;

		parent::__construct($handler);
	}

	public function read($id)
	{
		$data = parent::read($id);

		if (!empty($data)) {
			try {
				$data = $this->decryptData($data);
			} catch(CryptoException $ex) {
				throw new \Exception($ex->getMessage());
			}
		}

		return $data;
	}

	public function write($id, $data)
	{
		if (!empty($data)) {
			try {
				$data = $this->encryptData($data);
			} catch(CryptoException $ex) {
				throw new \Exception($ex->getMessage());
			}
		}

		return parent::write($id, $data);
	}

	/**
	 * Returns decrypted original string
	 *
	 * @param $cipherText
	 *
	 * @return string
	 */
	private function decryptData($cipherText)
	{
		return $this->encryption->decryptWithKey($cipherText, $this->keyManager->getKey());
	}

	/**
	 * Returns an encrypted string
	 *
	 * @param $plainText
	 *
	 * @return string
	 */
	private function encryptData($plainText)
	{
		return $this->encryption->encryptWithKey($plainText, $this->keyManager->getKey());
	}
}
```

Example 4
---------

### Custom Password Encoder
Can I encrypt my (bcrypt) password hashes? **Yes**.

```yaml
# app/config/security.yml
security:
    encoders:
        AppBundle\Entity\MyUser:
            id: app.security.encoder.encrypt_encoder
```

```yaml
# app/config/services.yml
services:
    app.security.encoder.encrypt_encoder:
        class: AppBundle\Security\Encoder\BCryptPasswordEncryptEncoder
        arguments: ['@mes_crypto.key_manager', '@mes_crypto.encryption', 13]
        public: false
```

```php
namespace AppBundle\Security\Encoder;

use Mes\Security\CryptoBundle\EncryptionInterface;
use Mes\Security\CryptoBundle\KeyManagerInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

/**
 * Class BCryptPasswordEncryptedEncoder
 *
 * @package AppBundle\Security\Encoder
 */
class BCryptPasswordEncryptEncoder extends BCryptPasswordEncoder
{
	/**
	 * @var KeyManagerInterface
	 */
	private $keyManager;

	/**
	 * @var EncryptionInterface
	 */
	private $encrypt;

	public function __construct(KeyManagerInterface $keyManager, EncryptionInterface $encrypt, $cost = 13)
	{
		$this->keyManager = $keyManager;

		$this->encrypt = $encrypt;

		parent::__construct($cost);
	}

	public function encodePassword($raw, $salt)
	{
		$digest = parent::encodePassword($raw, $salt);

		return $this->encrypt->encryptWithKey($digest, $this->keyManager->getKey());
	}

	public function isPasswordValid($encoded, $raw, $salt)
	{
		$encoded = $this->encrypt->decryptWithKey($encoded, $this->keyManager->getKey());

		return parent::isPasswordValid($encoded, $raw, $salt);
	}
}
```

```php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class MyUser
 *
 * @package AppBundle\Entity
 */
class MyUser implements UserInterface
{

	private $username;
	private $password;
	private $roles;

	public function __construct($username, $password, $roles)
	{
		$this->username = $username;
		$this->password = $password;
		$this->roles = $roles;
	}

	/**
	 * @return mixed
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param mixed $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @return mixed
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param mixed $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @return mixed
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	/**
	 * @param mixed $roles
	 */
	public function setRoles($roles)
	{
		$this->roles = $roles;
	}

	/**
	 * Returns the salt that was originally used to encode the password.
	 *
	 * This can return null if the password was not encoded using a salt.
	 *
	 * @return string|null The salt
	 */
	public function getSalt()
	{
		return null;
	}

	/**
	 * Removes sensitive data from the user.
	 *
	 * This is important if, at any given point, sensitive information like
	 * the plain-text password is stored on this object.
	 */
	public function eraseCredentials()
	{

	}
}
```

```php
namespace AppBundle\Controller;

use AppBundle\Entity\MyUser;
use AppBundle\Security\Encoder\BCryptPasswordEncryptEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Class CryptoController
 *
 * @package AppBundle\Controller
 *
 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Route("/crypto")
 */
class CryptoController extends Controller
{
/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Route(value="/example3/encrypt", name="example3CustomPasswordEncoderEncrypt")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function example3EncAction(Request $request)
	{
		$plainPassword = "ImpossiblePassword";

		/**
		 * @var BCryptPasswordEncryptEncoder $encoder
		 */
		$encoder = $this->get('security.encoder_factory')
						->getEncoder(MyUser::class);

		/**
		 * @var MyUser $user
		 */
		$user = new MyUser('John Doe', null, array('ROLE_ADMIN'));
		try {
			$user->setPassword($encoder->encodePassword($plainPassword, $user->getSalt()));
		} catch (BadCredentialsException $e) {
			return new Response($e->getMessageKey());
		}

		// Authenticated token.
		$token = new UsernamePasswordToken($user, $user->getPassword(), 'secured_area', $user->getRoles());

		$request->getSession()
				->set('_security_secured_area', serialize($token));

		var_dump($token);

		$str = "<br /><br/><a href='" . $this->generateUrl("example3CustomPasswordEncoderDecrypt") . "'>Example 3 / Decrypt</a><br />";

		$resp = new Response(sprintf("<!DOCTYPE html><html><head><title>Example 3 / Custom Password Encoder</title></head><body>%s</body></html>", $str));

		return $resp;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Route(value="/example3/decrypt", name="example3CustomPasswordEncoderDecrypt")
	 *
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function example3DecAction(Request $request)
	{
		$presentedPassword = "ImpossiblePassword";

		$token = $request->getSession()
						 ->get('_security_secured_area');

		/**
		 * @var UsernamePasswordToken $token
		 */
		$token = unserialize($token);

		/**
		 * @var MyUser $user
		 */
		$user = $token->getUser();

		/**
		 * @var BCryptPasswordEncryptEncoder $encoder
		 */
		$encoder = $this->get('security.encoder_factory')
						->getEncoder(get_class($user));

		$passwordIsValid = $encoder->isPasswordValid($user->getPassword(), $presentedPassword, $user->getSalt());

		var_dump("Password is valid: " . ($passwordIsValid ? 'OK' : 'Error'));

		var_dump($token);

		$str = "<br /><br/><a href='" . $this->generateUrl("example3CustomPasswordEncoderEncrypt") . "'>Example 3 / Encrypt</a><br />";

		$resp = new Response(sprintf("<!DOCTYPE html><html><head><title>Example 3 / Custom Password Encoder</title></head><body>%s</body></html>", $str));

		return $resp;
	}
}
```

Example 5 (Custom services)
---------------------------

```yaml
# app/config/services.yml
services:
    app.key_generator:
        class: AppBundle\CustomKeyGenerator
        public: false

    app.key_encryption:
        class: AppBundle\CustomEncryption
        public: false

    app.key_storage:
        class: AppBundle\CustomKeyStorage
        public: false
```

```yaml
# app/config/config.yml
mes_crypto:
    key: /home/vagrant/key.crypto
    secret: '%kernel.secret%'
    key_generator: app.key_generator
    key_storage: app.key_storage
    encryption: app.key_encryption
```

```php
namespace AppBundle;

use Mes\Security\CryptoBundle\KeyGenerator\AbstractKeyGenerator;

class CustomKeyGenerator extends AbstractKeyGenerator
{
	/**
	 * {@inheritdoc}
	 */
	public function generate($secret = null)
	{
		return new CustomKey($secret);
	}

	/**
	 * {@inheritdoc}
	 */
	public function generateFromAscii($key_encoded, $secret = null)
	{
		return new CustomKey($secret);
	}
}
```

```php
namespace AppBundle;

use Mes\Security\CryptoBundle\KeyStorage\AbstractKeyStorage;
use Mes\Security\CryptoBundle\Model\KeyInterface;

/**
 * Class CustomKeyStorage
 *
 * @package AppBundle
 */
class CustomKeyStorage extends AbstractKeyStorage
{
	/**
	 * @var KeyInterface
	 */
	private $key;

	/**
	 * {@inheritdoc}
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setKey(KeyInterface $key)
	{
		$this->key = $key;
	}
}
```

```php
namespace AppBundle;

use Mes\Security\CryptoBundle\AbstractEncryption;
use Mes\Security\CryptoBundle\Model\KeyInterface;

/**
 * Class CustomEncryption
 *
 * @package AppBundle
 */
class CustomEncryption extends AbstractEncryption
{

	/**
	 * {@inheritdoc}
	 */
	public function encryptWithKey($plaintext, KeyInterface $key)
	{
		return "encryptedData";
	}

	/**
	 * {@inheritdoc}
	 */
	public function decryptWithKey($ciphertext, KeyInterface $key)
	{
		return "decryptedData";
	}
}
```

```php
namespace AppBundle;

use Mes\Security\CryptoBundle\Model\AbstractKey;

/**
 * Class CustomKey
 *
 * @package AppBundle
 */
class CustomKey extends AbstractKey
{
	private $secret;

	/**
	 * {@inheritdoc}
	 */
	public function getEncoded()
	{
		return "encodedKey";
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setSecret($secret)
	{
		$this->secret = $secret;
	}
}
```
