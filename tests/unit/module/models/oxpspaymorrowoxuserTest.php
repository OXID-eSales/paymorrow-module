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
 * Class Unit_Module_Models_OxpsPaymorrowOxUserTest
 *
 * @see OxpsPaymorrowOxUser
 */
class Unit_Module_Models_OxpsPaymorrowOxUserTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowOxUser
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
        $this->SUT = $this->getMock( 'OxpsPaymorrowOxUser', array('__construct', 'load', 'save', 'getUserGroups') );
    }


    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASE IGNORE
     *
     * @param array $aParams
     *
     * @return OxpsPaymorrowOxUser|oxUser
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass( 'OxpsPaymorrowOxUser', $aParams );
    }


    public function test_getUserTelephone_shouldReturnEmpty()
    {
        $this->assertEmpty( $this->SUT->getUserTelephone() );
    }


    public function test_getUserTelephone_shouldReturnSetMobilePhone()
    {
        $sMobilePhone = '2929190 292';

        $this->SUT->oxuser__oxmobfon = new oxField( $sMobilePhone );

        $this->assertEquals( $sMobilePhone, $this->SUT->getUserTelephone() );
    }


    public function test_getUserTelephone_shouldReturnSetOxFonWhenMobilePhoneEmpty()
    {
        $sPhone = '593 101040 007';

        $this->SUT->oxuser__oxfon = new oxField( $sPhone );

        $this->assertEquals( $sPhone, $this->SUT->getUserTelephone() );
    }


    public function testGetUserPaymorrowDateOfBirth_ArgumentEmpty_returnSetBirthDateAsString()
    {
        $this->SUT->oxuser__oxbirthdate = new oxField( '1991-04-05' );

        $this->assertEquals( '1991-04-05', $this->SUT->getUserPaymorrowDateOfBirth() );
    }

    public function testGetUserPaymorrowDateOfBirth_argumentIsTrueAndBirthDateEmpty_returnArrayWithThreeZeroes()
    {
        $this->SUT->oxuser__oxbirthdate = new oxField( '' );

        $this->assertSame( array(0, 0, 0), $this->SUT->getUserPaymorrowDateOfBirth( true ) );
    }

    public function testGetUserPaymorrowDateOfBirth_argumentIsTrueAndBirthDateIsZeroes_returnArrayWithThreeZeroes()
    {
        $this->SUT->oxuser__oxbirthdate = new oxField( '0000-00-00' );

        $this->assertSame( array(0, 0, 0), $this->SUT->getUserPaymorrowDateOfBirth( true ) );
    }

    public function testGetUserPaymorrowDateOfBirth_argumentIsTrueAndBirthDateSet_returnArrayWithYearMonthDayAsIntegers()
    {
        $this->SUT->oxuser__oxbirthdate = new oxField( '1995-02-09' );

        $this->assertSame( array(1995, 2, 9), $this->SUT->getUserPaymorrowDateOfBirth( true ) );
    }


    public function testGetUserPaymorrowGender_salutationIsNotSupportedOne_returnEmptyString()
    {
        $this->SUT->oxuser__oxsal = new oxField( 'Prof. Dr. Herr' );

        $this->assertEquals( '', $this->SUT->getUserPaymorrowGender() );
    }

    public function testGetUserPaymorrowGender_salutationIsMrs_returnFemaleGender()
    {
        $this->SUT->oxuser__oxsal = new oxField( 'mrs' );

        $this->assertEquals( 'FEMALE', $this->SUT->getUserPaymorrowGender() );
    }

    public function testGetUserPaymorrowGender_salutationIsFrau_returnFemaleGender()
    {
        $this->SUT->oxuser__oxsal = new oxField( 'Frau' );

        $this->assertEquals( 'FEMALE', $this->SUT->getUserPaymorrowGender() );
    }

    public function testGetUserPaymorrowGender_salutationIsMr_returnMaleGender()
    {
        $this->SUT->oxuser__oxsal = new oxField( 'mr' );

        $this->assertEquals( 'MALE', $this->SUT->getUserPaymorrowGender() );
    }

    public function testGetUserPaymorrowGender_salutationIsHerr_returnMaleGender()
    {
        $this->SUT->oxuser__oxsal = new oxField( 'HERR' );

        $this->assertEquals( 'MALE', $this->SUT->getUserPaymorrowGender() );
    }

    public function testGetUserPaymorrowGender_argumentIsTrue_returnGenderInLowercase()
    {
        $this->SUT->oxuser__oxsal = new oxField( 'mr' );

        $this->assertEquals( 'male', $this->SUT->getUserPaymorrowGender( true ) );
    }


    public function testGetUserPaymorrowSalutation_returnSetSalutation()
    {
        $this->SUT->oxuser__oxsal = new oxField( 'mrs' );
        $this->assertEquals( 'mrs', $this->SUT->getUserPaymorrowSalutation() );
    }


    public function test_getUserPaymorrowRequestLanguage_shouldReturnAbbreviationOfSetLanguage()
    {
        oxRegistry::getLang()->setBaseLanguage( 33 );

        $this->assertEquals( 33, $this->SUT->getUserPaymorrowRequestLanguage() );
    }


    public function testGetUserPaymorrowIpAddress_ipv4GotFromServerUtils_returnTheValue()
    {
        $oServerUtilsMock = $this->getMock( 'oxUtilsServer', array('getRemoteAddress', 'getServerVar') );
        $oServerUtilsMock->expects( $this->once() )->method( 'getRemoteAddress' )->will(
            $this->returnValue( '222.222.111.22' )
        );
        $oServerUtilsMock->expects( $this->never() )->method( 'getServerVar' );
        oxRegistry::set( 'oxUtilsServer', $oServerUtilsMock );

        $this->assertEquals( '222.222.111.22', $this->SUT->getUserPaymorrowIpAddress() );
    }

    public function testGetUserPaymorrowIpAddress_ipv6GotFromServerUtils_returnRemoteAddrValue()
    {
        $oServerUtilsMock = $this->getMock( 'oxUtilsServer', array('getRemoteAddress', 'getServerVar') );
        $oServerUtilsMock->expects( $this->once() )->method( 'getRemoteAddress' )->will(
            $this->returnValue( 'fe80:1:2:3:a:bad:1dea:dad' )
        );
        $oServerUtilsMock->expects( $this->once() )->method( 'getServerVar' )
            ->with( $this->equalTo( 'REMOTE_ADDR' ) )
            ->will( $this->returnValue( '222.222.111.22' ) );
        oxRegistry::set( 'oxUtilsServer', $oServerUtilsMock );

        $this->assertEquals( '222.222.111.22', $this->SUT->getUserPaymorrowIpAddress() );
    }


    public function test_getUserPaymorrowEmail_shouldReturnSetUserMail()
    {
        $this->SUT->oxuser__oxusername = new oxField( 'paymorrow@info.com' );

        $this->assertEquals( 'paymorrow@info.com', $this->SUT->getUserPaymorrowEmail() );
    }


    public function test_getUserPaymorrowStreet_shouldReturnSetUserAddress()
    {
        $this->SUT->oxuser__oxstreet = new oxField( 'Address 33b - 10' );

        $this->assertEquals( 'Address 33b - 10', $this->SUT->getUserPaymorrowStreet() );

    }


    public function test_getUserPaymorrowStreetNumber_shouldReturnSetUserAddress()
    {
        $this->SUT->oxuser__oxstreetnr = new oxField( 'Street number: 10' );

        $this->assertEquals( 'Street number: 10', $this->SUT->getUserPaymorrowStreetNumber() );

    }


    public function test_getUserPaymorrowZipCode_shouldReturnSetUserAddress()
    {
        $this->SUT->oxuser__oxzip = new oxField( 'ZIP:22123' );

        $this->assertEquals( 'ZIP:22123', $this->SUT->getUserPaymorrowZipCode() );

    }


    public function test_getUserPaymorrowSessionId_shouldReturnSessionId()
    {
        oxRegistry::getSession()->setId( 'UNIT - test session id' );

        $this->assertEquals( 'UNIT - test session id', $this->SUT->getUserPaymorrowSessionId() );
    }


    public function test_getUserPaymorrowOrderIdFromBasket_shouldReturnExpectedOrderIdFromBasket()
    {
        $sExpectedOrderId = 'awfa92flakjwfawfawf';

        $this->SUT->getUserPaymorrowUserBasketFromSession()->setOrderId( $sExpectedOrderId );

        $this->assertEquals( $sExpectedOrderId, $this->SUT->getUserPaymorrowOrderIdFromBasket() );
    }


    public function test_getUserPaymorrowUserBasketFromSession_shouldReturnSetBasketFromSession()
    {
        oxRegistry::getSession()->setBasket( $this->getMock( 'oxBasket' ) );

        $this->assertTrue( $this->SUT->getUserPaymorrowUserBasketFromSession() instanceof oxBasket );
    }


    public function test_getUserPaymorrowCity_shouldReturnSetCity()
    {
        $this->SUT->oxuser__oxcity = new oxField( 'Vilnius' );


        $this->assertEquals( 'Vilnius', $this->SUT->getUserPaymorrowCity() );
    }


    public function test_getCustomerPaymorrowCustomerNumber_shouldReturnSetCustomerNumber()
    {
        $this->SUT->oxuser__oxcustnr = new oxField( 'sine' );

        $this->assertEquals( 'sine', $this->SUT->getCustomerPaymorrowCustomerNumber() );
    }


    public function test_getUserPaymorrowFirstName_shouldReturnSetFirstName()
    {
        $this->SUT->oxuser__oxfname = new oxField( 'myName' );

        $this->assertEquals( 'myName', $this->SUT->getUserPaymorrowFirstName() );
    }

    public function test_getUserPaymorrowLastName_shouldReturnSetLastName()
    {
        $this->SUT->oxuser__oxlname = new oxField( 'lastName' );

        $this->assertEquals( 'lastName', $this->SUT->getUserPaymorrowLastName() );
    }


    public function test_getUserPaymorrowCountry_shouldReturnCountryAbbreviationInISO_AlphaFormat()
    {
        $this->SUT->oxuser__oxcountryid = new oxField( '8f241f110955a7644.68859180' ); // British Indian Ocean Territory

        $this->assertEquals( 'IO', $this->SUT->getUserPaymorrowCountry() );

        $this->SUT->oxuser__oxcountryid = new oxField( '8f241f11095b94476.05195832' ); // Iran


        $this->assertEquals( 'IR', $this->SUT->getUserPaymorrowCountry() );
        $this->SUT->oxuser__oxcountryid = new oxField( '8f241f110960ec345.71805056' ); // Nicaragua

        $this->assertEquals( 'NI', $this->SUT->getUserPaymorrowCountry() );
    }


    public function testMapToProfileDataAndUpdateUser_noFieldsMapped_notUpdateUser()
    {
        $this->SUT->oxuser__oxfname = new oxField( 'J' );
        $this->SUT->expects( $this->never() )->method( 'save' );

        $this->assertNull( $this->SUT->mapToProfileDataAndUpdateUser( array('first_name' => 'John') ) );
        $this->assertSame( 'J', $this->SUT->oxuser__oxfname->value );
    }

    public function testMapToProfileDataAndUpdateUser_mappedFieldValueIsEmpty_notUpdateUser()
    {
        $this->SUT->oxuser__oxfname = new oxField( 'J' );
        $this->SUT->expects( $this->never() )->method( 'save' );

        $this->assertNull( $this->SUT->mapToProfileDataAndUpdateUser( array('pm_customer_firstName' => '') ) );
        $this->assertSame( 'J', $this->SUT->oxuser__oxfname->value );
    }

    /**
     * @dataProvider notAcceptedUserFieldsMap
     */
    public function testMapToProfileDataAndUpdateUser_fieldIsNotAccepted_notUpdateUser( $sInitValue,
                                                                                        $sDataFields,
                                                                                        $sUserField,
                                                                                        $sNewValue )
    {
        $this->SUT->$sUserField = new oxField( $sInitValue );
        $this->SUT->expects( $this->never() )->method( 'save' );

        $this->assertNull( $this->SUT->mapToProfileDataAndUpdateUser( array($sDataFields => $sNewValue) ) );
        $this->assertSame( $sInitValue, $this->SUT->$sUserField->value );
    }

    /**
     * @dataProvider validUserFieldsMap
     */
    public function testMapToProfileDataAndUpdateUser_mappedFieldValueIsValid_updateThisUserField( $sInitValue,
                                                                                                   $sDataFields,
                                                                                                   $sUserField,
                                                                                                   $sNewValue )
    {
        $this->SUT->$sUserField = new oxField( $sInitValue );
        $this->SUT->expects( $this->once() )->method( 'save' )->will( $this->returnValue( true ) );

        $this->assertTrue( $this->SUT->mapToProfileDataAndUpdateUser( array($sDataFields => $sNewValue) ) );
        $this->assertSame( $sNewValue, $this->SUT->$sUserField->value );
    }


    /**
     * Data provider for not accepted user profile fields map and values.
     *
     * @return array
     */
    public function notAcceptedUserFieldsMap()
    {
        return array(
            array('Herr.', 'pm_customer_title', 'oxuser__oxsal', 'MRS'),
            array('my@example.com', 'pm_customer_email', 'oxuser__oxusername', 'new@example.com'),
            array('DE', 'pm_customer_billingAddress_country', 'oxuser__oxcountryid', 'de'),
        );
    }

    /**
     * Data provider for accepted user profile fields map and values.
     *
     * @return array
     */
    public function validUserFieldsMap()
    {
        return array(
            array('J', 'pm_customer_firstName', 'oxuser__oxfname', 'John'),
            array('D', 'pm_customer_lastName', 'oxuser__oxlname', 'Doe'),
            array('', 'pm_customer_phoneNumber', 'oxuser__oxfon', '+49654546654'),
            array('0000-00-00', 'pm_customer_dateOfBirth', 'oxuser__oxbirthdate', '1999-09-09'),
            array(' ', 'pm_customer_billingAddress_street', 'oxuser__oxstreet', 'Street st.'),
            array('', 'pm_customer_billingAddress_houseNo', 'oxuser__oxstreetnr', '1'),
            array('123', 'pm_customer_billingAddress_postalCode', 'oxuser__oxzip', '12345'),
            array('X', 'pm_customer_billingAddress_city', 'oxuser__oxcity', 'Town'),
        );
    }


    public function testMapShippingDataAndUpdateAddress_noFieldsMapped_notUpdateAddress()
    {
        $oAddress = $this->getMock( 'oxAddress', array('__construct', 'load', 'save') );

        $oAddress->oxaddress__oxstreet = new oxField( '' );
        $oAddress->expects( $this->never() )->method( 'save' );

        $this->assertNull(
            $this->SUT->mapShippingDataAndUpdateAddress( array('street' => 'Street st.'), $oAddress )
        );
        $this->assertSame( '', $oAddress->oxaddress__oxstreet->value );
    }

    public function testMapShippingDataAndUpdateAddress_mappedFieldValueIsEmpty_notUpdateAddress()
    {
        $oAddress = $this->getMock( 'oxAddress', array('__construct', 'load', 'save') );

        $oAddress->oxaddress__oxstreet = new oxField( 'Street st.' );
        $oAddress->expects( $this->never() )->method( 'save' );

        $this->assertNull(
            $this->SUT->mapShippingDataAndUpdateAddress( array('pm_customer_shippingAddress_street' => ''), $oAddress )
        );
        $this->assertSame( 'Street st.', $oAddress->oxaddress__oxstreet->value );
    }

    public function testMapShippingDataAndUpdateAddress_fieldIsNotAccepted_notUpdateAddress()
    {
        $oAddress = $this->getMock( 'oxAddress', array('__construct', 'load', 'save') );

        $oAddress->oxaddress__oxcountryid = new oxField( 'DE' );
        $oAddress->expects( $this->never() )->method( 'save' );

        $this->assertNull(
            $this->SUT->mapShippingDataAndUpdateAddress(
                array('mp_customer_shippingAddress_country' => 'EE'), $oAddress
            )
        );
        $this->assertSame( 'DE', $oAddress->oxaddress__oxcountryid->value );
    }

    /**
     * @dataProvider validAddressFieldsMap
     */
    public function testMapShippingDataAndUpdateAddress_mappedFieldValueIsValid_updateThisAddressField( $sInitValue,
                                                                                                        $sDataFields,
                                                                                                        $sAddressField,
                                                                                                        $sNewValue )
    {
        $oAddress = $this->getMock( 'oxAddress', array('__construct', 'load', 'save') );

        $oAddress->$sAddressField = new oxField( $sInitValue );
        $oAddress->expects( $this->once() )->method( 'save' )->will( $this->returnValue( true ) );

        $this->assertTrue(
            $this->SUT->mapShippingDataAndUpdateAddress( array($sDataFields => $sNewValue), $oAddress )
        );
        $this->assertSame( $sNewValue, $oAddress->$sAddressField->value );
    }

    /**
     * Data provider for accepted user shipping address fields map and values.
     *
     * @return array
     */
    public function validAddressFieldsMap()
    {
        return array(
            array('', 'pm_customer_shippingAddress_street', 'oxaddress__oxstreet', 'Strasse st.'),
            array(' ', 'pm_customer_shippingAddress_houseNo', 'oxaddress__oxstreetnr', '12'),
            array('1', 'pm_customer_shippingAddress_postalCode', 'oxaddress__oxzip', '12345'),
            array('X', 'pm_customer_shippingAddress_city', 'oxaddress__oxcity', 'Burg'),
            array('', 'pm_customer_shippingAddress_firstName', 'oxaddress__oxfname', 'John'),
            array('', 'pm_customer_shippingAddress_lastName', 'oxaddress__oxlname', 'Smith'),
            array('', 'pm_customer_shippingAddress_company', 'oxaddress__oxcompany', 'X inc.'),
            array('', 'pm_customer_shippingAddress_additionalInfo', 'oxaddress__oxaddinfo', 'NFO'),
        );
    }


    public function testGetUserGroupNames_userHasNoGroupsAssigned_returnEmptyString()
    {
        $this->assertSame( '', $this->SUT->getUserGroupNames() );
    }

    public function testGetUserGroupNames_userGroupsAreNotActive_returnEmptyString()
    {
        // Group mock
        $oGroupMock = $this->getMock( 'oxBase', array('__construct', 'load') );

        $this->SUT->expects( $this->once() )->method( 'getUserGroups' )->will(
            $this->returnValue( array($oGroupMock) )
        );

        $this->assertSame( '', $this->SUT->getUserGroupNames() );
    }

    public function testGetUserGroupNames_userGroupsHaveNoNames_returnEmptyString()
    {
        // Group mock
        $oGroupMock                     = $this->getMock( 'oxBase', array('__construct', 'load') );
        $oGroupMock->oxgroups__oxactive = new oxField( '1' );
        $oGroupMock->oxgroups__oxtitle  = new oxField( '' );

        $this->SUT->expects( $this->once() )->method( 'getUserGroups' )->will(
            $this->returnValue( array($oGroupMock) )
        );

        $this->assertSame( '', $this->SUT->getUserGroupNames() );
    }

    public function testGetUserGroupNames_userHasOneValidGroup_returnTheGroupName()
    {
        // Group mock
        $oGroupMock                     = $this->getMock( 'oxBase', array('__construct', 'load') );
        $oGroupMock->oxgroups__oxactive = new oxField( '1' );
        $oGroupMock->oxgroups__oxtitle  = new oxField( 'My Group' );

        $this->SUT->expects( $this->once() )->method( 'getUserGroups' )->will(
            $this->returnValue( array($oGroupMock) )
        );

        $this->assertSame( 'My Group', $this->SUT->getUserGroupNames() );
    }

    public function testGetUserGroupNames_userHasManyValidGroups_returnCommaSeparatedGroupNames()
    {
        // Group mock
        $oGroupMock1                     = $this->getMock( 'oxBase', array('__construct', 'load') );
        $oGroupMock1->oxgroups__oxactive = new oxField( '1' );
        $oGroupMock1->oxgroups__oxtitle  = new oxField( 'My Group' );

        $oGroupMock2                     = $this->getMock( 'oxBase', array('__construct', 'load') );
        $oGroupMock2->oxgroups__oxactive = new oxField( '1' );
        $oGroupMock2->oxgroups__oxtitle  = new oxField( 'Admins' );

        $this->SUT->expects( $this->once() )->method( 'getUserGroups' )->will(
            $this->returnValue( array($oGroupMock1, $oGroupMock2) )
        );

        $this->assertSame( 'My Group, Admins', $this->SUT->getUserGroupNames() );
    }
}
