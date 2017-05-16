<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle;

use Mes\Security\CryptoBundle\Model\KeyInterface;

/**
 * Interface EncryptionInterface.
 */
interface EncryptionInterface
{
    /**
     * Encrypts a plaintext string using a secret key.
     *
     * * Cautions:
     * * The ciphertext returned by this method is decryptable by anyone with knowledge of the key $key.
     * * It is the caller's responsibility to keep $key secret.
     * * Where $key should be stored is up to the caller and depends on the threat model the caller is designing their application under.
     * * If you are unsure where to store $key, consult with a professional cryptographer to get help designing your application.
     *
     * @param string       $plaintext String to encrypt
     * @param KeyInterface $key       Instance of KeyInterface containing the secret key for encryption
     *
     * @return string A ciphertext string representing $plaintext encrypted with the key $key. Knowledge of $key is required in order to decrypt the ciphertext and recover the plaintext
     */
    public function encryptWithKey($plaintext, KeyInterface $key);

    /**
     * Encrypts a plaintext string using a secret password.
     * This method is intentionally slow, using a lot of CPU resources for a fraction of a second.
     * It applies key stretching to the password in order to make password guessing attacks more computationally expensive.
     * If you need a faster way to encrypt multiple ciphertexts under the same secret, use encryptWithKey and generate a Key protected by secret.
     *
     * @param string $plaintext String to encrypt
     * @param string $password  String containing the secret password used for encryption
     *
     * @return string A ciphertext string representing $plaintext encrypted with a key derived from $password. Knowledge of $password is required in order to decrypt the ciphertext and recover the plaintext
     */
    public function encryptWithPassword($plaintext, $password);

    /**
     * Decrypts a ciphertext string using a secret key.
     * It is impossible in principle to distinguish between the case where you attempt to decrypt with the wrong key and the case where you attempt to decrypt a modified (corrupted) ciphertext.
     * It is up to the caller how to best deal with this ambiguity, as it depends on the application this bundle is being used in. If in doubt, consult with a professional cryptographer.
     *
     * @param string            $ciphertext ciphertext to be decrypted
     * @param KeyInterface|null $key        Instance of KeyInterface containing the secret key for decryption
     *
     * @return string If the decryption succeeds, returns a string containing the same value as the string that was passed to encryptWithKey() when $ciphertext was produced.
     *                Upon a successful return, the caller can be assured that $ciphertext could not have been produced except by someone with knowledge of $key
     */
    public function decryptWithKey($ciphertext, KeyInterface $key);

    /**
     * Decrypts a ciphertext string using a secret password.
     * This method is intentionally slow.
     * It applies key stretching to the password in order to make password guessing attacks more computationally expensive.
     * If you need a faster way to encrypt multiple ciphertexts under the same secret, use encryptWithKey and generate a Key protected by secret.
     *
     * @param string $ciphertext ciphertext to be decrypted
     * @param string $password   A string containing the secret password used for decryption
     *
     * @return string If the decryption succeeds, returns a string containing the same value as the string that was passed to encryptWithPassword() when $ciphertext was produced.
     *                Upon a successful return, the caller can be assured that $ciphertext could not have been produced except by someone with knowledge of $password
     */
    public function decryptWithPassword($ciphertext, $password);

    /**
     * Encrypts a file using a secret key.
     * Encrypts the contents of the input file, writing the result to the output file. If the output file already exists, it is overwritten.
     *
     * * Cautions:
     * * The ciphertext output by this method is decryptable by anyone with knowledge of the key $key.
     * * It is the caller's responsibility to keep $key secret.
     * * Where $key should be stored is up to the caller and depends on the threat model the caller is designing their application under.
     * * If you are unsure where to store $key, consult with a professional cryptographer to get help designing your application.
     *
     * @param string       $inputFilename  Path to a file containing the plaintext to encrypt
     * @param string       $outputFilename Path to save the ciphertext file
     * @param KeyInterface $key            Instance of KeyInterface containing the secret key for encryption
     */
    public function encryptFileWithKey($inputFilename, $outputFilename, KeyInterface $key);

    /**
     * Encrypts a file with a password.
     * Encrypts the contents of the input file, writing the result to the output file. If the output file already exists, it is overwritten.
     * This method is intentionally slow, using a lot of CPU resources for a fraction of a second.
     * It applies key stretching to the password in order to make password guessing attacks more computationally expensive.
     * If you need a faster way to encrypt multiple ciphertexts under the same password, use encryptFileWithKey and generate a Key protected by secret.
     *
     * @param string $inputFilename  Path to a file containing the plaintext to encrypt
     * @param string $outputFilename Path to save the ciphertext file
     * @param string $password       The password used for decryption
     */
    public function encryptFileWithPassword($inputFilename, $outputFilename, $password);

    /**
     * Decrypts a file using a secret key.
     * Decrypts the contents of the input file, writing the result to the output file. If the output file already exists, it is overwritten.
     * The input ciphertext is processed in two passes.
     * The first pass verifies the integrity and the second pass performs the actual decryption of the file and writing to the output file.
     * This is done in a streaming manner so that only a small part of the file is ever loaded into memory at a time.
     * It is impossible in principle to distinguish between the case where you attempt to decrypt with the wrong key and the case where you attempt to decrypt a modified (corrupted) ciphertext.
     * It is up to the caller how to best deal with this ambiguity, as it depends on the application this Bundle is being used in. If in doubt, consult with a professional cryptographer.
     *
     * * Cautions:
     * * Be aware that when an Exception is thrown, some partial plaintext data may have been written to the output.
     * * Any plaintext data that is output is guaranteed to be a prefix of the original plaintext (i.e. at worst it was truncated).
     * * This can only happen if an attacker modifies the input between the first pass (integrity check) and the second pass (decryption) over the file.
     *
     * @param string       $inputFilename  Path to a file containing the ciphertext to decrypt
     * @param string       $outputFilename Path to save the decrypted plaintext file
     * @param KeyInterface $key            Instance of KeyInterface containing the secret key for encryption
     */
    public function decryptFileWithKey($inputFilename, $outputFilename, KeyInterface $key);

    /**
     * Decrypts a file with a password.
     * Decrypts the contents of the input file, writing the result to the output file. If the output file already exists, it is overwritten.
     * This method is intentionally slow, using a lot of CPU resources for a fraction of a second.
     * It applies key stretching to the password in order to make password guessing attacks more computationally expensive.
     * If you need a faster way to encrypt multiple ciphertexts under the same password, use encryptFileWithKey and generate a Key protected by secret.
     * The input ciphertext is processed in two passes.
     * The first pass verifies the integrity and the second pass performs the actual decryption of the file and writing to the output file.
     * This is done in a streaming manner so that only a small part of the file is ever loaded into memory at a time.
     *
     * * Cautions:
     * * Be aware that when an Exception is thrown, some partial plaintext data may have been written to the output.
     * * Any plaintext data that is output is guaranteed to be a prefix of the original plaintext (i.e. at worst it was truncated).
     * * This can only happen if an attacker modifies the input between the first pass (integrity check) and the second pass (decryption) over the file.
     * * It is impossible in principle to distinguish between the case where you attempt to decrypt with the wrong password and the case where you attempt to decrypt a modified (corrupted) ciphertext.
     * * It is up to the caller how to best deal with this ambiguity, as it depends on the application this Bundle is being used in. If in doubt, consult with a professional cryptographer.
     *
     * @param string $inputFilename  Path to a file containing the ciphertext to decrypt
     * @param string $outputFilename Path to save the decrypted plaintext file
     * @param string $password       The password used for decryption
     */
    public function decryptFileWithPassword($inputFilename, $outputFilename, $password);
}
