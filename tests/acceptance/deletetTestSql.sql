--
-- Delete test data.
-- Use in cases when eShop is not restored automatically after tests run.
--
DELETE FROM `oxcontents` WHERE `OXID` IN ('oxpspm_orderemail_invoice', 'oxpspm_orderemail_invoice_plain', 'oxpspm_orderemail_sdd', 'oxpspm_orderemail_sdd_plain');
DELETE FROM `oxuser` WHERE `OXID` IN ('admin@example.com', '0affed7c43ee0b1743c12f9ea772caa9', 'd5eb34c373bc2f4bff034a9e834fe16d', '1890bca2d23c74555c851454b3a8d936');
DELETE FROM `oxvouchers` WHERE `OXVOUCHERNR` IN ('10EUR', '10%');
DELETE FROM `oxvoucherseries` WHERE `OXID` IN ('28928c72893813743fab3fb82478e18e', 'a683a4ba78f0c5b44a1c346963e52b8f');
DELETE FROM `oxobject2group` WHERE `OXID` IN ('0397c2bd51210680340aac0f353eddc9', 'c397c2bd51210680340aac0f353eddc9', '_c397c2bd51210680340aac0f353eddc', '__c397c2bd51210680340aac0f353edd');
