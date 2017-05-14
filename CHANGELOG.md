Changelog
=========

1.2.0 (2017-XX-XX)
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
