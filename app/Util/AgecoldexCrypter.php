<?php


namespace App\Util;

/**
 * Tomado desde: https://github.com/iam-raihan/3DES-ECB-Cryptography-in-PHP/blob/master/Crypt_mcrypt.php
 * Class AgecoldexCrypter
 * @package App\Util
 */
class AgecoldexCrypter {
    private $hash;

    function __construct( $hash ) {
        $key        = md5($hash, true);
        $key        .= substr($key, 0, 8);
        $this->hash = $key;
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function Encrypt( $data ) {
        $encData = openssl_encrypt($data, 'DES-EDE3', $this->hash, OPENSSL_RAW_DATA);

        $encData = base64_encode($encData);

        return $encData;
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function Decrypt( $data ) {
        $data = base64_decode($data);

        $decData = openssl_decrypt($data, 'DES-EDE3', $this->hash, OPENSSL_RAW_DATA);

        return $decData;
    }

    /**
     * @param $password
     */
    public static function FastEncrypt( $password ) {
        return (new AgecoldexCrypter('web_novo_agesoft'))->Encrypt($password);
    }

    public static function FastDecrypt( $password ) {
        return (new AgecoldexCrypter('web_novo_agesoft'))->Decrypt($password);
    }
}