<?php
namespace Managers;
/**
 *  =======Example======
 *  $message   = 'hello there';
 *  $encrypted = Cryptor::encrypt( $message);
 *  $decrypted = Cryptor::decrypt( $encrypted);
 */


class Cryptor
{
    const METHOD = 'aes-256-ctr';
    const HASH_ALGO = 'sha256';
    const KEY = 'you key here';

    /**
     * Encrypts (but does not authenticate) a message
     *
     * @param string $message - plaintext message
     * @param boolean $encode - set to TRUE to return a base64-encoded
     * @return string (raw binary)
     */
    public static function unsafe_encrypt($message, $encode = false)
    {
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = openssl_random_pseudo_bytes($nonceSize);

        $ciphertext = openssl_encrypt(
            $message,
            self::METHOD,
            self::KEY,
            OPENSSL_RAW_DATA,
            $nonce
        );

        // Now let's pack the IV and the ciphertext together
        // Naively, we can just concatenate
        if ($encode) {
            return base64_encode($nonce.$ciphertext);
        }
        return $nonce.$ciphertext;
    }

    /**
     * Decrypts (but does not verify) a message
     *
     * @param string $message - ciphertext message
     * @param boolean $encoded - are we expecting an encoded string?
     * @return string
     */
    public static function unsafe_decrypt($message, $encoded = false)
    {
        if ($encoded) {
            $message = base64_decode($message, true);
            if ($message === false) {
                throw new Exception('Encryption failure');
            }
        }

        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = mb_substr($message, 0, $nonceSize, '8bit');
        $ciphertext = mb_substr($message, $nonceSize, null, '8bit');

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::METHOD,
            self::KEY,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $plaintext;
    }


    /**
     * Encrypts then MACs a message
     *
     * @param string $message - plaintext message
     * @param boolean $encode - set to TRUE to return a base64-encoded string
     * @return string (raw binary)
     */
    public static function encrypt($message, $encode = false)
    {
        $key = hex2bin(implode(unpack("H*", self::KEY)));
        list($encKey, $authKey) = self::splitKeys($key);

        // Pass to UnsafeCrypto::encrypt
        $ciphertext = self::unsafe_encrypt($message, $encKey);

        // Calculate a MAC of the IV and ciphertext
        $mac = hash_hmac(self::HASH_ALGO, $ciphertext, $authKey, true);

        if ($encode) {
            return base64_encode($mac.$ciphertext);
        }
        // Prepend MAC to the ciphertext and return to caller
        return base64_encode($mac.$ciphertext);
    }

    /**
     * Decrypts a message (after verifying integrity)
     *
     * @param string $message - ciphertext message
     * @param boolean $encoded - are we expecting an encoded string?
     * @return string (raw binary)
     */
    public static function decrypt($message, $encoded = false)
    {
        $message= base64_decode($message);
        $key = hex2bin(implode(unpack("H*", self::KEY)));
        list($encKey, $authKey) = self::splitKeys($key);
        if ($encoded) {
            $message = base64_decode($message, true);
            if ($message === false) {
                throw new Exception('Encryption failure');
            }
        }

        // Hash Size -- in case HASH_ALGO is changed
        $hs = mb_strlen(hash(self::HASH_ALGO, '', true), '8bit');
        $mac = mb_substr($message, 0, $hs, '8bit');

        $ciphertext = mb_substr($message, $hs, null, '8bit');

        $calculated = hash_hmac(
            self::HASH_ALGO,
            $ciphertext,
            $authKey,
            true
        );

        if (!self::hashEquals($mac, $calculated)) {
            throw new Exception('Encryption failure');
        }

        // Pass to UnsafeCrypto::decrypt
        $plaintext = self::unsafe_decrypt($ciphertext, $encKey);

        return $plaintext;
    }

    /**
     * Splits a key into two separate keys; one for encryption and the other for authenticaiton
     *
     * @param string $masterKey (raw binary)
     * @return array (two raw binary strings)
     */
    protected static function splitKeys($masterKey)
    {
        // You really want to implement HKDF here instead!
        return [
            hash_hmac(self::HASH_ALGO, 'ENCRYPTION', $masterKey, true),
            hash_hmac(self::HASH_ALGO, 'AUTHENTICATION', $masterKey, true)
        ];
    }

    /**
     * Compare two strings without leaking timing information
     *
     * @param string $a
     * @param string $b
     * @ref https://paragonie.com/b/WS1DLx6BnpsdaVQW
     * @return boolean
     */
    protected static function hashEquals($a, $b)
    {
        if (function_exists('hash_equals')) {
            return hash_equals($a, $b);
        }
        $nonce = openssl_random_pseudo_bytes(32);
        return hash_hmac(self::HASH_ALGO, $a, $nonce) === hash_hmac(self::HASH_ALGO, $b, $nonce);
    }
}
