Changelog
=========

2.1.0 (2017-XX-XX)
------------------
* Added mes_crypto.loader service to load encoded key and secret by a custom CryptoLoader
* KeyGeneratorCommand and SecretGeneratorCommand defined as services

2.0.0 (2017-05-23)
------------------
* Added Encryption::encryptResourceWithKey
* Added Encryption::encryptResourceWithPassword
* Added Encryption::decryptResourceWithKey
* Added Encryption::decryptResourceWithPassword
* Added EncryptionWrapper::encryptResourceWithKey
* Added EncryptionWrapper::encryptResourceWithPassword
* Added EncryptionWrapper::decryptResourceWithKey
* Added EncryptionWrapper::decryptResourceWithPassword
* Removed deprecations

1.3.0 (2017-05-16)
------------------
* Added Encryption::encryptWithPassword
* Added Encryption::decryptWithPassword
* Added Encryption::encryptFileWithPassword
* Added Encryption::decryptFileWithPassword
* Added EncryptionWrapper::encryptWithPassword
* Added EncryptionWrapper::decryptWithPassword
* EncryptionWrapper::encryptFileWithPassword
* EncryptionWrapper::decryptFileWithPassword

This methods are intentionally slow, using a lot of CPU resources for a fraction of a second.
They apply key stretching to the password in order to make password guessing attacks more computationally expensive.
If you need a faster way to encrypt multiple ciphertexts under the same secret, see encryptWithKey, decryptWithKey,
encryptFileWithKey, decryptFileWithKey and use a secret to generate the Key.

* Tests improved

1.2.0 (2017-14-05)
------------------
* Deprecated Encryption::encrypt. Added Encryption::encryptWithKey
* Deprecated Encryption::decrypt. Added Encryption::decryptWithKey
* Deprecated Encryption::encryptFile. Added Encryption::encryptFileWithKey
* Deprecated Encryption::decryptFile. Added Encryption::decryptFileWithKey
* Deprecated EncryptionWrapper::encrypt. Added EncryptionWrapper::encryptWithKey
* Deprecated EncryptionWrapper::decrypt. Added EncryptionWrapper::decryptWithKey
* Deprecated EncryptionWrapper::encryptFile. EncryptionWrapper::encryptFileWithKey
* Deprecated EncryptionWrapper::decryptFile. EncryptionWrapper::decryptFileWithKey

1.1.0 (2017-05-12)
------------------
* Added ability to encrypt files:
    - `EncryptionInterface::encryptFile`
    - `EncryptionInterface::decryptFile`

1.0.1 (2017-03-21)
------------------
* Fixed: `KeyGeneratorCommand` shows process debug information (`-vvv`) only if `--dir` option is defined
* `.crypto` file is created in read-only mode

1.0.0 (2017-03-16)
------------------
* Initial release 1.0.0
