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
 * Class Unit_Module_Controllers_OxpsPaymorrowResponseHandlerTest
 *
 * @see OxpsPaymorrowResponseHandler
 */
class Unit_Module_Controllers_OxpsPaymorrowResponseHandlerTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowResponseHandler
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
        $this->SUT = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('__construct', 'load', 'init',)
        );

    }


    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASe IGNORE
     *
     * @param array $aParams
     *
     * @return OxpsPaymorrowResponseHandler
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass( 'OxpsPaymorrowResponseHandler', $aParams );
    }


    public function testCall_methodHasNorOkNeitherErrorEnding_returnNull()
    {
        $this->assertNull( $this->SUT->handleSomeResponseWithUnknownStatus() );
    }

    public function testCall_methodEndsWithOk_callLoggerSetResponseDataAndReturnTrue()
    {
        $aData = array('pm_response_status' => 'OK');

        $oLoggerMock = $this->getMock( 'OxpsPaymorrowLogger', array('logWithType') );
        $oLoggerMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( $aData ), $this->equalTo( 'PM_handleUpdateOrderIdResponseOK' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLoggerMock );

        $this->assertTrue( $this->SUT->handleUpdateOrderIdResponseOK( $aData ) );
        $this->assertSame( $aData, $this->SUT->getResponse() );
    }

    public function testCall_methodEndsWithError_callLoggerAndReturnFalse()
    {
        $aData = array('pm_error' => 'bad data');

        $oLoggerMock = $this->getMock( 'OxpsPaymorrowLogger', array('logWithType') );
        $oLoggerMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( $aData ), $this->equalTo( 'PM_handleUpdateOrderIdResponseError' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLoggerMock );

        $this->assertFalse( $this->SUT->handleUpdateOrderIdResponseError( $aData ) );
    }


    public function test_handlePrepareOrderResponseError_shouldReturnFalse()
    {
        $oLoggerMock = $this->getMock( 'OxpsPaymorrowLogger', array('logWithType') );
        $oLoggerMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( array() ), $this->equalTo( 'PM_handlePrepareOrderResponseError' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLoggerMock );

        $this->assertFalse( $this->SUT->handlePrepareOrderResponseError( array() ) );
    }


    public function test_handleConfirmOrderResponseOK_shouldReturnTrue()
    {
        $oLoggerMock = $this->getMock( 'OxpsPaymorrowLogger', array('logWithType') );
        $oLoggerMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( array() ), $this->equalTo( 'PM_handleConfirmOrderResponseOK' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLoggerMock );

        $this->assertTrue( $this->SUT->handleConfirmOrderResponseOK( array() ) );
    }


    public function test_handleConfirmOrderResponseError_shouldReturnFalseAndSetSessionUnexpectedError()
    {
        $oLoggerMock = $this->getMock( 'OxpsPaymorrowLogger', array('logWithType') );
        $oLoggerMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( array() ), $this->equalTo( 'PM_handleConfirmOrderResponseError' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLoggerMock );

        $this->assertFalse( $this->SUT->handleConfirmOrderResponseError( array() ) );
    }


    public function test_handleConfirmOrderResponseError_shouldReturnFalseAndSetSessionExpectedErrorCode()
    {
        $aData = array(
            'pm_error_22_code' => 229292
        );

        $this->SUT->handleConfirmOrderResponseError( $aData );

        $this->assertEquals( 229292, $this->SUT->getErrorCode() );
    }


    public function testHandlePrepareOrderResponseOK_returnTrue()
    {
        // Logger mock
        $oLoggerMock = $this->getMock( 'OxpsPaymorrowLogger', array('logWithType') );
        $oLoggerMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( array('data' => 7) ), $this->equalTo( 'PM_handlePrepareOrderResponseOK' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLoggerMock );

        // Module mock
        $oModuleMock = $this->getMock( 'OxpsPaymorrowModule', array('__construct', 'updateProfile') );
        $oModuleMock->expects( $this->once() )->method( 'updateProfile' )->with( $this->equalTo( array('data' => 7) ) );
        oxRegistry::set( 'OxpsPaymorrowModule', $oModuleMock );

        $this->assertTrue( $this->SUT->handlePrepareOrderResponseOK( array('data' => 7) ) );
    }


    public function test_getErrorCodeFromResponseData_shouldReturnDefaultUnexpectedError()
    {
        $this->assertEquals( 999, $this->SUT->getErrorCodeFromResponseData( array() ) );
    }


    /**
     * @dataProvider dataProviderErrorCode
     */
    public function test_getErrorCodeFromResponseData_shouldReturnExpectedError( $aData )
    {
        $this->assertEquals( $aData['result'], $this->SUT->getErrorCodeFromResponseData( $aData[0] ) );
    }


    public function dataProviderErrorCode()
    {
        return array(
            array(array('result' => 245, array('pm_error_22_code' => 245))),
            array(array('result' => 1313, array('error_22_code' => 1313))),
            array(array('result' => 12531, array('error_2152_code' => 12531))),
            array(array('result' => 1337, array('error_22_code' => 1337))),
            array(array('result' => 999, array('pm_errorawf23_2222_code' => 245))),
            array(array('result' => 45463, array('error_2222_code' => 45463))),
            array(array('result' => 999, array('pm_errorawf23_2222_code' => 3))),
            array(array('result' => 33, array('pm_error_2222_code' => '33aewg'))),
            array(array('result' => 112, array('pm_error_2242222222_code' => 112))),
            array(array('result' => 1, array('pm_error_2242222222_code' => 1))),
            array(array('result' => 13, array('pm_error_1_code' => 13))),
            array(array('result' => 999, array('pm_erde' => 112))),
            array(array('result' => 999, array('pm_er2222_code' => 112))),
            array(array('result' => 0, array('pm_error_0_code' => 0))),
            array(array('result' => 125345235235, array('pm_error_125345235235_code' => 125345235235))),
            array(array('result' => 11, array('pm_error_11_code' => 11))),
            array(array('result' => 13332, array('pm_error_22_code' => 13332))),
            array(array('result' => 999, array('rror_22422_code' => 112))),
            array(array('result' => 999, array('pm_error_22422222e' => 112))),
            array(array('result' => 21, array('pm_error_21_code' => 21))),
            array(array('result' => 111111112, array('pm_error_22454848422_code' => 111111112))),
            array(array('result' => 999, array('pm_error_2242afawfawfawfawf222222_code' => 112))),
        );
    }


    public function testWasDeclined_responseNotSet_returnFalse()
    {
        $this->assertFalse( $this->SUT->wasDeclined() );
    }

    public function testWasDeclined_orderStatusSetAsValidatedOrAccepted_returnFalse()
    {
        $this->SUT->setResponse( array('order_status' => 'VALIDATED') );

        $this->assertFalse( $this->SUT->wasDeclined() );
    }

    public function testWasDeclined_orderStatusSetAsDeclined_returnTrue()
    {
        $this->SUT->setResponse( array('order_status' => 'DECLINED') );

        $this->assertTrue( $this->SUT->wasDeclined() );
    }


    public function testWasAccepted_errorInResponse_returnFalse()
    {
        $this->SUT->setErrorCode( 100 );

        $this->assertFalse( $this->SUT->wasAccepted() );
    }

    public function testWasAccepted_responseNotSet_returnFalse()
    {
        $this->assertFalse( $this->SUT->wasAccepted() );
    }

    public function testWasAccepted_orderStatusIsNotEqualAccepted_returnFalse()
    {
        $this->SUT->setResponse( array('pm_order_status' => 'DECLINED') );

        $this->assertFalse( $this->SUT->wasAccepted() );
    }

    public function testWasAccepted_orderStatusIsEqualAccepted_returnTrue()
    {
        $this->SUT->setResponse( array('pm_order_status' => 'ACCEPTED') );

        $this->assertTrue( $this->SUT->wasAccepted() );
    }


    public function testGetDeclinationDataFromResponse_responseNotSet_returnEmptyArray()
    {
        $this->assertSame( array(), $this->SUT->getDeclinationDataFromResponse() );
    }

    public function testGetDeclinationDataFromResponse_noPaymentFieldsInRequest_returnEmptyArray()
    {
        $this->SUT->setResponse( array('order_status' => 'DECLINED') );

        $this->assertSame( array(), $this->SUT->getDeclinationDataFromResponse() );
    }

    public function testGetDeclinationDataFromResponse_paymentFieldsFoundInRequest_returnArrayWithTheFieldsAndValues()
    {
        $this->SUT->setResponse(
            array(
                'order_status'              => 'DECLINED',
                'paymentMethod_name'        => 'INVOICE',
                'paymentMethod_status'      => 'DECLINED',
                'paymentMethod_declineType' => 'DONT_COMUNICATE',
            )
        );

        $this->assertSame(
            array(
                'paymentMethod_name'        => 'INVOICE',
                'paymentMethod_status'      => 'DECLINED',
                'paymentMethod_declineType' => 'DONT_COMUNICATE',
            ),
            $this->SUT->getDeclinationDataFromResponse()
        );
    }


    public function testGetErrorDataFromResponse_responseNotSet_returnEmptyArray()
    {
        $this->assertSame( array(), $this->SUT->getErrorDataFromResponse() );
    }

    public function testGetErrorDataFromResponse_responseHasNoError_returnEmptyArray()
    {
        $this->SUT->setResponse(
            array(
                'order_status'              => 'DECLINED',
                'paymentMethod_name'        => 'INVOICE',
                'paymentMethod_status'      => 'DECLINED',
                'paymentMethod_declineType' => 'DONT_COMUNICATE',
            )
        );

        $this->assertSame( array(), $this->SUT->getErrorDataFromResponse() );
    }

    public function testGetErrorDataFromResponse_responseContainError_returnArrayWithErrorKeysAndValues()
    {
        $this->SUT->setResponse(
            array(
                'order_status'              => 'DECLINED',
                'paymentMethod_name'        => 'INVOICE',
                'paymentMethod_status'      => 'DECLINED',
                'paymentMethod_declineType' => 'DONT_COMUNICATE',
                'error_1_code'              => '100',
                'error_1_msg'               => 'Error',
                'error_2_code'              => '800',
            )
        );

        $this->assertSame(
            array(
                'error_1_code' => '100',
                'error_1_msg'  => 'Error',
                'error_2_code' => '800',
            ),
            $this->SUT->getErrorDataFromResponse()
        );
    }


    public function testParseGetConfigurationResponse_inputArrayHasNoExpectedKeys_returnEmptyArray()
    {
        $this->assertSame( array(), $this->SUT->parseGetConfigurationResponse( array('api_endpoint_live' => 'URL') ) );
    }

    public function testParseGetConfigurationResponse_inputArrayHasNoValueForKey_returnAssocArrayWithTheKeyAndEmptyStringAsValue()
    {
        $this->assertSame(
            array('api_endpoint_live' => ''),
            $this->SUT->parseGetConfigurationResponse(
                array(
                    'pm_configuration_1_key'   => 'api_endpoint_live',
                    'pm_configuration_2_value' => 'URL',
                )
            )
        );
    }

    public function testParseGetConfigurationResponse_inputArrayValid_returnAssocArrayWithKeysMappedToCorrespondingValues()
    {
        $this->assertSame(
            array(
                'key1' => 'value1',
                'keyB' => 'valueB',
            ),
            $this->SUT->parseGetConfigurationResponse(
                array(
                    'pm_configuration_1_key'   => 'key1',
                    'pm_configuration_2_key'   => 'keyB',
                    'pm_configuration_2_value' => 'valueB',
                    'pm_configuration_1_value' => 'value1',
                )
            )
        );
    }


    public function testHasErrors_noErrorCodeSet_returnFalse()
    {
        $this->assertFalse( $this->SUT->hasErrors() );
    }

    public function testHasErrors_noErrorCodeSet_returnTrue()
    {
        $this->SUT->setErrorCode( 999 );

        $this->assertTrue( $this->SUT->hasErrors() );
    }
}
