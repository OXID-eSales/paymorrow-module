<?php

/**
 * @package Paymorrow Gateway
 * @copyright (C) 2015 - Paymorrow GmbH
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

class CertificateGenerator
{
    public function generateCertificate($certData)
    {
        $configParams = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $privkey = openssl_pkey_new($configParams);

        //Now, using the private key, we can create the certificate. First we define the certificate parameters:

        //And then we can create the certificate:

        $csr = openssl_csr_new($certData, $privkey, $configParams);

        //Now we sign the certificate using the private key:

        $duration = 2 * 365;
        $sscert = openssl_csr_sign($csr, null, $privkey, $duration, $configParams);

        //Finally we can export the certificate and the private key:

        openssl_x509_export($sscert, $certout);
        $password = NULL;
        openssl_pkey_export($privkey, $pkout, $password, $configParams);
        //Note that a password is needed to export the private key. If a password is not needed, you must set $password
        //to NULL (don't set it to empty string as the private key password will be an empty string).

        return array(
            'privateKey' => $pkout,
            'certificate' => $certout
        );
    }
}

