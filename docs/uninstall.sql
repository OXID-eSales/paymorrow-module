-- Paymorrow module uninstall SQL file --
-- Execute these SQL queries on Your database to completely remove Paymorrow fields and CMS contents --
-- NOTE: After executing it You will loose all Paymorrow related payment data! --
ALTER TABLE `oxpayments`
  DROP `OXPSPAYMORROWACTIVE`,
  DROP `OXPSPAYMORROWMAP`;

ALTER TABLE `oxuserpayments`
  DROP `OXPSPAYMORROWBANKNAME`,
  DROP `OXPSPAYMORROWIBAN`,
  DROP `OXPSPAYMORROWBIC`,
  DROP `OXPSPAYMORROWORDERID`;

-- CMS snippets deletion --
-- NOTE: This is an example for one bas sub-shop.
--       If You have many sub-shops, You need to remove snippets from each sub-shop.

DELETE FROM `oxcontents` WHERE `oxcontents`.`OXID` = 'oxpspm_orderemail_sdd_plain';
DELETE FROM `oxcontents` WHERE `oxcontents`.`OXID` = 'oxpspm_orderemail_sdd';
DELETE FROM `oxcontents` WHERE `oxcontents`.`OXID` = 'oxpspm_orderemail_invoice_plain';
DELETE FROM `oxcontents` WHERE `oxcontents`.`OXID` = 'oxpspm_orderemail_invoice';

-- For older shop version --
DELETE FROM `oxcontents` WHERE `oxcontents`.`OXID` = 'oxpspm0_orderemail_sdd_plain';
DELETE FROM `oxcontents` WHERE `oxcontents`.`OXID` = 'oxpspm0_orderemail_sdd';
DELETE FROM `oxcontents` WHERE `oxcontents`.`OXID` = 'oxpspm0_orderemail_invoice_plain';
DELETE FROM `oxcontents` WHERE `oxcontents`.`OXID` = 'oxpspm0_orderemail_invoice';
