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

$sLangName = "Deutsch";

$aLang = array(
    "charset"                                        => "UTF-8",

    'PAYMORROW_PAYMENT_METHOD_NAME_INVOICE'          => 'Rechnungskauf',
    'PAYMORROW_PAYMENT_METHOD_NAME_DIRECT_DEBIT'     => 'Lastschriftverfahren',

    'PAYMORROW_PAYMENT_NO_JAVASCRIPT'                => 'Um diese Zahlungsart zu nutzen, muss JavaScript im Browser aktiviert sein.',

    'PAYMORROW_GENERAL_ERROR'                        => 'Es ist ein Fehler aufgetreten. Bitte wiederholen Sie den Vorgang.',
    'PAYMORROW_ACCEPT_CONDITIONS_ERROR'              => 'Bitte akzeptieren Sie die Datenschutzbestimmungen der Paymorrow GmbH.',
    'PAYMORROW_SELECT_GENDER_ERROR'                  => 'Sie haben keine Anrede ausgewählt.',
    'PAYMORROW_DATE_OF_BIRTH_ERROR'                  => 'Sie haben kein Geburtsdatum angegeben.',
    'PAYMORROW_MOBILE_NUMBER_ERROR'                  => 'Sie haben keine Festnetz- oder Mobilnummer eingegeben.',

    // Custom
    'PAYMORROW_ORDER_DATA_COLLECTION_FAILED'         => 'Die Bestelldatenerfassung ist gescheitert',
    'PAYMORROW_ORDER_SAVING_TEMPORARY_ORDER_FAILED'  => 'Das Speichern der temporären Bestellung ist gescheitert.',

    // Email
    'EMAIL_ORDER_CUST_HTML_PAYMENTMETHOD'            => 'Zahlungsart:',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_BANK'           => 'Bank:',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_IBAN'           => 'IBAN:',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_BIC'            => 'BIC:',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_REFERENCE_LINE' => 'Verwendungszweck:',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_ORDER_ID'       => 'BE',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_CUSTOMER_NR'    => 'KD',
);
