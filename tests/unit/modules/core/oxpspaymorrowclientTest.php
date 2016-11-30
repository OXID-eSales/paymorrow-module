<?php
/**
 * This file is part of the OXID module for Paymorrow payment.
 *
 * The OXID module for Paymorrow payment is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * The OXID eShop module for Paymorrow payment is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
 * Public License for more details.
 *
 * Linking this library statically or dynamically with other modules is making a
 * combined work based on this library. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 * As a special exception, the copyright holders of this library give you
 * permission to link this library with independent modules to produce an
 * executable, regardless of the license terms of these independent modules, and
 * to copy and distribute the resulting executable under terms of your choice,
 * provided that you also meet, for each linked independent module, the terms and
 * conditions of the license of that module. An independent module is a module
 * which is not derived from or based on this library. If you modify this library,
 * you may extend this exception to your version of the library, but you are not
 * obliged to do so. If you do not wish to do so, delete this exception statement
 * from your version.
 *
 * You should have received a copy of the GNU General Public License along with
 * the OXID module for Paymorrow payment. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class Unit_Module_Controllers_OxpsPaymorrowClientTest
 *
 * @see OxpsPaymorrowClient
 */
class Unit_Module_Controllers_OxpsPaymorrowClientTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowClient
     */
    protected $SUT;


    /**
     * Set initial objects state.
     *
     * @return null|void
     */
    public function setUp()
    {
        parent::setUp();

        // SUT mock
        $this->SUT = $this->getProxyClass( 'OxpsPaymorrowClient' );
    }


    public function testSignRequest_userPrivateKeyFromSettingsToSignRequestData()
    {
        // Module settings mock
        $oSettingsMock = $this->getMock( 'OxpsPaymorrowSettings', array('getPrivateKey') );
        $oSettingsMock->expects( $this->once() )->method( 'getPrivateKey' )->will(
            $this->returnValue(
                '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCqGKukO1De7zhZj6+H0qtjTkVxwTCpvKe4eCZ0FPqri0cb2JZfXJ/DgYSF6vUp
wmJG8wVQZKjeGcjDOL5UlsuusFncCzWBQ7RKNUSesmQRMSGkVb1/3j+skZ6UtW+5u09lHNsj6tQ5
1s1SPrCBkedbNf0Tp0GbMJDyR4e9T04ZZwIDAQABAoGAFijko56+qGyN8M0RVyaRAXz++xTqHBLh
3tx4VgMtrQ+WEgCjhoTwo23KMBAuJGSYnRmoBZM3lMfTKevIkAidPExvYCdm5dYq3XToLkkLv5L2
pIIVOFMDG+KESnAFV7l2c+cnzRMW0+b6f8mR1CJzZuxVLL6Q02fvLi55/mbSYxECQQDeAw6fiIQX
GukBI4eMZZt4nscy2o12KyYner3VpoeE+Np2q+Z3pvAMd/aNzQ/W9WaI+NRfcxUJrmfPwIGm63il
AkEAxCL5HQb2bQr4ByorcMWm/hEP2MZzROV73yF41hPsRC9m66KrheO9HPTJuo3/9s5p+sqGxOlF
L0NDt4SkosjgGwJAFklyR1uZ/wPJjj611cdBcztlPdqoxssQGnh85BzCj/u3WqBpE2vjvyyvyI5k
X6zk7S0ljKtt2jny2+00VsBerQJBAJGC1Mg5Oydo5NwD6BiROrPxGo2bpTbu/fhrT8ebHkTz2epl
U9VQQSQzY1oZMVX8i1m5WUTLPz2yLJIBQVdXqhMCQBGoiuSoSjafUhV7i1cEGpb88h5NBYZzWXGZ
37sJ5QsW+sJyoNde3xH8vdXhzU7eT82D6X/scw9RZz+/6rCJ4p0=
-----END RSA PRIVATE KEY-----'
            )
        );
        oxRegistry::set( 'OxpsPaymorrowSettings', $oSettingsMock );

        $sSignature = $this->SUT->signRequest( 'data' );

        $this->assertFalse( empty( $sSignature ) );
    }


    public function testVerifyResponse_userPublicCertificateFromSettingsToValidateResponseData()
    {
        // Module settings mock
        $oSettingsMock = $this->getMock( 'OxpsPaymorrowSettings', array('getPaymorrowKey') );
        $oSettingsMock->expects( $this->once() )->method( 'getPaymorrowKey' )->will(
            $this->returnValue(
                '-----BEGIN CERTIFICATE-----
MIIDpTCCAw6gAwIBAgIJAMwaU71bkD+IMA0GCSqGSIb3DQEBBQUAMIGUMQswCQYD
VQQGEwJERTEbMBkGA1UECBMSQmFkZW4tV3VlcnR0ZW1iZXJnMRIwEAYDVQQHEwlL
YXJsc3J1aGUxFzAVBgNVBAoTDnBheW1vcnJvdyBHbWJIMRgwFgYDVQQDFA8qLnBh
eW1vcnJvdy5uZXQxITAfBgkqhkiG9w0BCQEWEmluZm9AcGF5bW9ycm93LmNvbTAe
Fw0wOTAzMjAxNTMwMTFaFw0xOTAzMTgxNTMwMTFaMIGUMQswCQYDVQQGEwJERTEb
MBkGA1UECBMSQmFkZW4tV3VlcnR0ZW1iZXJnMRIwEAYDVQQHEwlLYXJsc3J1aGUx
FzAVBgNVBAoTDnBheW1vcnJvdyBHbWJIMRgwFgYDVQQDFA8qLnBheW1vcnJvdy5u
ZXQxITAfBgkqhkiG9w0BCQEWEmluZm9AcGF5bW9ycm93LmNvbTCBnzANBgkqhkiG
9w0BAQEFAAOBjQAwgYkCgYEAqvHjEaDggMsSGkCvPZBu+6W0fkKrY+rJE+aleKrF
FZv/B+/+ntMnQYsmJTGhD1z3ZKr/hjPAqZTnfzh2XCM9cbmjRUy9wgfo1txaaRVT
YQWxqQ5UAxyMzo2Mu+3eaedmXga/l01moNkU/n5MKfAOYMQ7E/inXMne+fgkbmVx
0QkCAwEAAaOB/DCB+TAdBgNVHQ4EFgQUTW3fhTiSwkCts/Jf3Nnw57b+mLkwgckG
A1UdIwSBwTCBvoAUTW3fhTiSwkCts/Jf3Nnw57b+mLmhgZqkgZcwgZQxCzAJBgNV
BAYTAkRFMRswGQYDVQQIExJCYWRlbi1XdWVydHRlbWJlcmcxEjAQBgNVBAcTCUth
cmxzcnVoZTEXMBUGA1UEChMOcGF5bW9ycm93IEdtYkgxGDAWBgNVBAMUDyoucGF5
bW9ycm93Lm5ldDEhMB8GCSqGSIb3DQEJARYSaW5mb0BwYXltb3Jyb3cuY29tggkA
zBpTvVuQP4gwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOBgQB830Fx0vn5
QRkFp0IJyXW13hlCmIuqJMyg5IP/3NSUtRws3BeLBBLB7fL6t4hDWPaF7n1oOwkp
wYleQwvrtUOAOscJyNBaAy9T1RTLlyFTR/6soKwvrBBx727DGHgSU0DFFX99szQ+
D17y5GVVQlLKAlvke3IdjGrA5EZZPs0dsg==
-----END CERTIFICATE-----'
            )
        );
        oxRegistry::set( 'OxpsPaymorrowSettings', $oSettingsMock );

        $this->SUT->verifyResponse( 'data', '0A1F2C34' );
    }
}
