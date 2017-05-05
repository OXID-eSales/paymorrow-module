--
-- NOTE: A copy of demodata_PE_CE.sql
--
-- Additional database data to import before executing Paymorrow acceptance tests.
-- Paymorrow module installation SQL is included.
-- It contains pre-configured user profiles, voucher series
-- This script also enables Paymorrow module, configures it and maps payment methods.
-- @SHOP_ID must be set with previous script, if not, 1 will be used
--

--
--  Dynamically select shop id to fit all shop editions
--

SELECT IF(@SHOP_ID IS NULL, 1, @SHOP_ID) INTO @SHOP_ID;

-- CMS snippets installation --
-- NOTE: This is an example for one bas sub-shop.
--       If You have many sub-shops, You need to add these snippets to each sub-shop.
--       If You have many languages, You also need to set it for al other languages.

REPLACE INTO `oxcontents` (`OXID`, `OXLOADID`, `OXSHOPID`, `OXSNIPPET`, `OXTYPE`, `OXACTIVE`, `OXACTIVE_1`, `OXPOSITION`, `OXTITLE`, `OXCONTENT`, `OXTITLE_1`, `OXCONTENT_1`, `OXACTIVE_2`, `OXTITLE_2`, `OXCONTENT_2`, `OXACTIVE_3`, `OXTITLE_3`, `OXCONTENT_3`, `OXCATID`, `OXFOLDER`, `OXTERMVERSION`)
VALUES
  ('oxpspm_orderemail_invoice', 'oxpspmuserorderemailinvoice', 1, 1, 0, 1, 1, '',
   'Paymorrow Keine Kontodaten Hinweistext Rechnung Email',
   'Bitte verwenden Sie ausschließlich die Kontoverbindung, die Ihnen für diesen Kauf per E-Mail mitgeteilt wurde.',
   'Paymorrow no bank account info invoice email',
   'Please only use the bank account that was given to you for this purchase by e-mail.', 1, '', '', 1, '', '',
   '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
  ('oxpspm_orderemail_invoice_plain', 'oxpspmuserorderemailinvoiceplain', 1, 1, 0, 1, 1, '',
   'Paymorrow Keine Kontodaten Hinweistext Rechnung Email Plain',
   'Bitte verwenden Sie ausschließlich die Kontoverbindung, die Ihnen für diesen Kauf per E-Mail mitgeteilt wurde.',
   'Paymorrow no bank account info invoice email plain',
   'Please only use the bank account that was given to you for this purchase by e-mail.', 1, '', '', 1, '', '',
   '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
  ('oxpspm_orderemail_sdd', 'oxpspmuserorderemailsdd', 1, 1, 0, 1, 1, '',
   'Paymorrow Kontodaten Hinweistext Lastschrift Email',
   'Der Rechnungsbetrag wird automatisch von Ihrem Bankkonto eingezogen. Der Zeitpunkt des Einzugs wird Ihnen vorher per E-Mail mitgeteilt.',
   'Paymorrow bank account info direct debit email',
   'The invoice amount is automatically debited from your bank account. The date of the entry will be communicated by e-mail.',
   1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
  ('oxpspm_orderemail_sdd_plain', 'oxpspmuserorderemailsddplain', 1, 1, 0, 1, 1, '',
   'Paymorrow Kontodaten Hinweistext Lastschrift Email Plain',
   'Der Rechnungsbetrag wird automatisch von Ihrem Bankkonto eingezogen. Der Zeitpunkt des Einzugs wird Ihnen vorher per E-Mail mitgeteilt.',
   'Paymorrow bank account info direct debit email plain',
   'The invoice amount is automatically debited from your bank account. The date of the entry will be communicated by e-mail.',
   1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', '');


-- ----- --
-- USERS --
-- ----- --

--
-- Dumping data for table `oxuser`
--

INSERT IGNORE INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`, `OXCUSTNR`, `OXUSTID`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`, `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXUPDATEKEY`, `OXUPDATEEXP`, `OXPOINTS`, `OXTIMESTAMP`)
VALUES
  ('admin@example.com', 1, 'malladmin', @SHOP_ID, 'admin@example.com',
   '1f2938eb162de457528b4d53c91ebc6219742124bd2b28b2d77b76281e0c3eacc386f239b63be0ff479bb353deffac175e494d0a01e2f0e8d555f379ca9d2d14',
   'cf52ce79989924a140bacd2150c50954', 10, '', '', 'Admin', 'Admin', 'Bertoldstr.', '48', '', 'Freiburg im Breisgau',
   'a7c40f631fc920687.20179984', '', '79098', '+49(6545)46654', '', 'MR', 1000, '2014-09-08 09:47:23',
   '2014-09-08 09:47:23', '', '', '1977-04-11', '', '', 0, 0, '2014-09-08 08:43:49'),
  ('0affed7c43ee0b1743c12f9ea772caa9', 1, 'user', @SHOP_ID, 'valid.user@oxid-esales.com',
   '59e15bbcc4751ee3077ddd847567b3c1c0a577999c1a2a2fe0dc6c659f824bdda13827c814ca15310a3b4befb370946b15366f0da28fa6495a218136f862cbd9',
   'd7b18358dafc1260673ff8b656b717c8', 3, '', '', 'Valid', 'User', 'Bertoldstr.', '48', '', 'Freiburg im Breisgau',
   'a7c40f631fc920687.20179984', '', '79098', '+49(6545)46654', '', 'MR', 1000, '2014-09-08 09:47:23',
   '2014-09-08 09:47:23', '', '', '1977-04-11', '', '', 0, 0, '2014-09-08 08:43:49'),
  ('d5eb34c373bc2f4bff034a9e834fe16d', 1, 'user', @SHOP_ID, 'incomplete.user@oxid-esales.com',
   '842f36d020f589326de6256627d116bf589ab0a66ca36e1f6f52748f9a62d880c7f032811f793e1de11054b1d198eb127feb56f1131abe3bc1e6374989785472',
   '82c38b5bb00c5eaf6b4e87ffb2140f19', 4, '', '', 'Incomplete', 'User', 'Bertoldstraße', '48', '', 'Freiburg',
   'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2014-09-09 11:24:02', '2014-09-09 11:24:02', '', '',
   '0000-00-00', '', '', 0, 0, '2014-09-09 09:25:33'),
  ('1890bca2d23c74555c851454b3a8d936', 1, 'user', @SHOP_ID, 'multiaddress.user@oxid-esales.com',
   '6505db679e6d69c3c2de11c38faaa2e81ec125e60b1c6f8e6eeaf41f03da537adb8993f4bcc43cb96410d804092141ddb70f1ba92e95eab0c02e4e3f4dbfcd84',
   '492aa753d9c8d3dd2b4e09e02ac7e528', 5, '', '', 'Multiaddress', 'User', 'Bertoldstraße', '48', '', 'Freiburg',
   'a7c40f631fc920687.20179984', '', '79098', '+49654546654', '', 'MR', 1000, '2014-09-09 12:22:11',
   '2014-09-09 12:22:11', '', '', '1955-05-05', '', '', 0, 0, '2014-09-09 10:45:19');

-- -------- --
-- VOUCHERS --
-- -------- --

--
-- Dumping data for table `oxvouchers`
--

INSERT IGNORE INTO `oxvouchers` (`OXDATEUSED`, `OXORDERID`, `OXUSERID`, `OXRESERVED`, `OXVOUCHERNR`, `OXVOUCHERSERIEID`, `OXDISCOUNT`, `OXID`, `OXTIMESTAMP`)
VALUES
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '024c2d428827b247a78f7708ad144860', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '027e786a5f83245c1cd8dda93e99fc12', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '049c3bde0472be022366725e3b108769', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '05d8fd95c176af445cf8a1f95535c936', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '05fdfedaf78aeef570114483dfb000ba', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '086b28a71b83440f6ea338bb4d5866d3', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '0a6e6a0f47218055aee1a0a18f6342c8', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '0c20073f011925f402893247b9e03a53', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '0f75d3f851d8fef9d41debb32e10a4c2', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '118ffe7c83b91f8753ee2c6c5dbaf4b1', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '125c23e48e639fc049fd6cd027b09ecb', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '13ae3b1fbecaee74230354756f53f8d2', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '14025a16539bd7b1fb3d85b72bfa2d64', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '1707ec139e73aed97433b00dadf74c46', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '186ba12d457d57da36d187a64d2e0c07', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '191c34965da8f9b7b22620a0b83cefaf', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '19b43abe9b9189945203ea8121c70a13', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '1aacc9ece218e8bf614c41f9e9d19784', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '1c49a7dc2fa8693afa0afb31c1384a7b', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '1ce8e07f3965ab495d367dbb6645b0f4', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '1d54296d3b8e95d887c58119cec62d38', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '1f2e5fc46d7aa9585ca37697bb126d3a', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '2102afdbca6f6d96c1c17a18cfc6a1ad', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '212377a3c443352776021caf1dbda961', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '2278465ae4276f5a9123f2f5f11f87e5', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '23ca26f6afd98637d32ddd54124bada0', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '24289ba8af70848adc76a37468e0eda1', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '242b818c8394bcc4e1113929683dacbe', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '244b9c832091407befda1f04ae39ce41', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '2a038857e945e523a27833b1f43f91e1', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '2a2e43007290022297569ecc3729528e', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '2aa3ddf76eb69a42ef3bed7100d566bd', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '2f9c5dc9f4d5214056e4d89882841aea', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '3086b70da96a98c1470da6b32d0b635c', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '30f22d459de295237aea9bfb66fe8350', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '312f5fcbf95b3796d38817ac4f2d90d0', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '313f11466704e9cbadbaa0f171d5761c', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '330c492f9290e38e1429010e3594bb3d', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '35add830686c0302672321db28c1650f', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '380718de6dda3c23cad1dd821b3e8e00', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '3808e2f092841c8464624bd2a0779174', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '385e07135f90cc3f118b42a94917a8b6', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '38d93a6ffbe4e76174b537336838bb6b', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '3908a6c0f44c0781940d174bdb799203', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '39602c98c36b03aa52f1c2e46d683bd8', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '3bb84b6bae7106c4993e4021a7344f34', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '3ce735cce9b2fe937f95586dc22439a8', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '3d9c52c1244db9e42f1170877c7b06ca', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '3effc15dfd7a3059e9dae3e7aacd299f', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '3f5b0dc70c51ebc9ac48933e36de71b2', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '3f8830349b486a6850c66730acf678f7', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '41cdb0320fdabba26e2cafaba5f4bce8', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '42205cbbb826b27192614b914949d2e5', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '4282afe981cd0114f3041debe0f36efc', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '46d40903505c21decf9abf07e411dfbe', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '47181bbbd6ee2ab19b148a46cbcfb568', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '48bdcf202be41b2b070ebb65d26633de', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '4a667c1de0a767da4da824f12ab3197c', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '4a7034d8d14a58d6554c1160efaf1f6a', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '4b3df3857cfa868eff4b885cf65df7fd', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '4b74d45f040a28e15b9066f68c57e4b3', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '4c6cc3e6001770f3af04a770e55912a1', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '4ff5905a82ab292085b1cba6e251e804', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '539050dd2f0cdbc49096aae384cb6bf2', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '567122f0ad88cd7442c9ddeeb8257110', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '5732ddb5cd45890378d7e11e2eb630db', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '57969adf96f7615b4f901806adfc892f', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '5a62f67e251cf35449fc4690b5787246', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '5bfa2539dc503d53a00e25285f092cfd', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '5c615f85a619a79c7c122be37d2c924c', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '5de049dd4916214b97885d2132a0c02c', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '5f67dda079c3556888fc37611c79b4c1', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '62094f93299ef74a008dcc7458fc0618', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '62c4802c5d32e457c020c5e5c58a3ebf', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '62ec067fc56740f4f9a73d63e4e599b2', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '6350eb8dd933681a518c25865f54da89', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '63b8efb19c69afaf2a1a5f1bcb095b6e', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '63ee3ad17026b78bb9302189aeeb0f2d', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '658be383419d9cb0b19050e155fb34f5', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '670a10cff8b907b8a73cc868dbdbecf5', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '678f842503f32cb73d9e360a949e5abd', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '6b5dbf2b6f1228bfcade724d9425c1c4', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '6c0c38977d7cf7a0fc3de13e8071e270', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '6ddba6181548d48c8d8a346873046ae9', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '6e3dab82fa1299dfab59970f6291041e', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '6eaead6071200cb4a7a5172789f0be33', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '70c7fcdec1781c093cd9850bc7550095', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '717e6ef3df02a6d0383a966637025137', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '72b42cc1e480fbcf40d932c4d833a5ee', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '74b301dedae302b72cc63b59ec8f8504', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '7631cd4b63dcbbf067b86fb1aaaab281', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '78f2492a406d6c10d3fc189d38878186', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '7b0b3611dfd2cceeff70847f7d214fac', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '7c032c3f3ac23d340e081ec4ba20d139', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '7c44816243d7ae5003a530050cffd6a7', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '7f629a843802118ec7830b36c98a1b5a', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '7fb821162cbafc63f95a953ef5bf812b', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '802a4538b7f841eaa677ed0b353f0294', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '809a0412512b2d3b1426d7ecdbfd09b1', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '85b453985e1cecc7913fe7734da5ccb0', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '8639d2a0b7e0fdaa3110524c863c99a9', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '86ee3064df85baa6b853c2578355300e', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '88fe93b3f1bb8fa4ad08db1edee1aba8', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '8904056e65231c3070ea26059c3e12d0', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '894ca66e500b6ffc067341aa4b19f353', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '8a50f290353764b9aadf7b627a95983e', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '8b163dea6a13de4898924b199a2bded3', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '8d3d9f73c7764cb6079975baff1270c7', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '90cf558ef93fc1a343b5bfc1a1890939', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '90ff43df50a1560d1609802a960673f2', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '942abed42c184bbddf24087ccf0da919', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '951f4b5110db7587ce9935e27dde6f24', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '95ae6f71e95d03d5a4a362c916555bd3', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '9a453adfe1a024bc05fc01e7d13d88f1', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '9a5f1afd391f74dadb248c1c48fb2143', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, '9adb9ed5e529f5bf3361dbba00595df5', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '9b021f8557ca11974b038bfc0e1755ba', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '9c2627048c3b18474a69787070e4c6b9', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '9f57f7f1b376930ea5d402c4d10dec78', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, '9fa6fbbb998c658fb9e1f541d350abff', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'a12b69150107d0887a0a7590555672a9', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'a1b33d21730f258db146432dff0720df', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'a25113ac7ff8664d1dfccf780c445573', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'a2aea1ed3fd3df4fddc6d2bc2a00c8a3', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'a2e7188c35f0b4e7749c74e090b2a896', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'a38d92b694fe1d0d3541e95ff2159074', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'a662b58301541556f1398ecd49b4f408', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'a7a9e3fff3f9e4edfa3de8b1adeab248', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'a7fd78e7ac14b33834905156e2216047', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'a8510c6b699b966fd5e16097011afd75', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'a88ab7f0a90e2ed580ae2b8d13e2a68a', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'a9d6004769cb6edc4903666c00053a5d', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'ab0a44fb2ecddacdab605504c8b6a2b0', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'ad4dd7c887331a707042b19ad9ff59bd', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'adc6b212c954bd954400ec505910c258', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'ade997b232b8acd0de5b729beaf4dfac', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'ae6e09e13b377b805cbce708fe65adf3', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'b0f2064b8c3f5ff732769b4488d2afb9', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'b2f4d7466d719d127b0f84cc3030ab63', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'b4deac26754d441af355cb364e7b4df6', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'b5035aec61be059a252c006e6b86611f', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'b64896a1cba47a59a8e91025932bf119', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'b69c63fd421c884e9663eebb4cc5e237', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'ba8c6019167f7443966d233b751517c0', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'baa1ee2f2ccf134c27d1fa0029d9673f', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'bbde1854fb85440f5f2f64eea77a69e0', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'bde3fedd73a7ffd94df842d5223518e7', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'be4de7a959054694828162dbfa9f6bc0', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'bfc9a7c87401799702819f3ce74a5d9a', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'c058f61a72a10111894041cb354157f9', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'c16eedb63573fb811a13cb39cb927524', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'c77c164dfaa8ed20cb5b1e1c2182c07d', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'c94552f3e0a9f9ef1b1b59575fe550b7', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'c94fdb4486b7f586686418db94ba20d5', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'ce69299045c051d48f67e8bf594a16d1', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'cef08f47a4e96e2cc8561fd19ef1a44a', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'cf5bdf36e2d561b9909fd03787cef04e', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'd035529fb1972e5dddac1eda3e10786f', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'd1836b04a882debaa19567aa4083ab89', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'd19b11389bdce0f4c29ca899749aeff7', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'd2f4a09de39a250ea94c7b577d9fbed1', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'd3c1acba8fc9f101e0275ee5b6b36b2d', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'd4ccf25e159d0f936212c2c3797ced53', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'd623fd054a566dbe33cee40d4a495e50', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'd795fdb1d08e8433509f4b13e75b9709', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'd7d65b586c5785d0193a17c6e3088f1c', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'd8986a7663ca0655737399f80fb06ff1', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'd8bdba1fa958c0a8ffbabb0e49531b00', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'd9028867aad57e11d1a900442eb394c3', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'd908b410946d4d71a1b3090a6bc9b79b', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'd9e9015d7a2044024fc6046efdc4dc3c', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'db6609a8f7a72609a09770d0bf161592', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'dba9138d4c27ed60907487d624aa93f2', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'de3e8e30035405a977eb76a91c4c0b4e', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'df9f3a68b4e7ef86f98bd179ad649f1a', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'e00e907a8cafa38f789e220b648ed824', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'e31dfa2aeafb718bfdde3328123d2583', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'e42bf3350a691a2f09bf693c68313d70', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'e4fa5eabb5459d7017a87b9e60943679', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'e540d702bf9ea123438fb84bf3c9c153', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'e7ae9331d2527eb246fa0f7d3dd6b3fa', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'e7e9991950b00ec7647db45d1921553a', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'e9c95f24398281795b9810ba31a73b6d', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'ea130fb2a1481499a9a8d9014a77aa08', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'ea2758d318ff669b2037bf01514badb0', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'ec77a562ede4a101acead2c0dd568421', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'ee16db2a7150b18be8e98bd429a4b09e', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'eea2533499a3555826abb6bc24d94afa', '2014-09-09 09:50:58'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'effa7b094f32669b77100c5180325ead', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'f08d5dfba75f6463899b090f3049b9a4', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'f2a6326c95c0e7934c33d330da91b2ce', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'f64b384e1aee82794a1c41d74af03df1', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'f87e74add42bc4e420bf51b0296dbde8', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'f88d2a993b5d545b451e8678fb1ef992', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'fa42ee5d31d533bc444efedfa4746428', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'fa44e1060d2d0f66f0a7049f7eb86cba', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10EUR', '28928c72893813743fab3fb82478e18e', NULL, 'fb93c09be1a02435a1e569dd222f039a', '2014-09-09 09:51:34'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'fbf9e804a55e27becb8eab60dbb7fc3d', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'fe9cd0a8cc3f1f4a995534b3177c024d', '2014-09-09 09:50:57'),
('0000-00-00', '', '', 0, '10%', 'a683a4ba78f0c5b44a1c346963e52b8f', NULL, 'ff96fdc636fb6852628a90e100eb0ce7', '2014-09-09 09:50:57');

--
-- Dumping data for table `oxvoucherseries`
--

INSERT IGNORE INTO `oxvoucherseries` (`OXID`, `OXSHOPID`, `OXSERIENR`, `OXSERIEDESCRIPTION`, `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXBEGINDATE`, `OXENDDATE`, `OXALLOWSAMESERIES`, `OXALLOWOTHERSERIES`, `OXALLOWUSEANOTHER`, `OXMINIMUMVALUE`, `OXCALCULATEONCE`, `OXTIMESTAMP`)
VALUES
  ('28928c72893813743fab3fb82478e18e', @SHOP_ID, '10EUR', '10EUR', 10.00, 'absolute', '0000-00-00 00:00:00',
   '0000-00-00 00:00:00', 1, 1, 1, 10.00, 1, '2014-09-09 09:51:25'),
  ('a683a4ba78f0c5b44a1c346963e52b8f', @SHOP_ID, '10%', '10%', 10.00, 'percent', '0000-00-00 00:00:00',
   '0000-00-00 00:00:00', 1, 1, 1, 0.00, 1, '2014-09-09 09:50:42');

-- -------- --
-- PAYMENTS --
-- -------- --

-- Remove current payment methods --
DELETE FROM `oxpayments`
WHERE `OXID` IN ("oxiddebitnote", "oxidinvoice");

--
-- Dumping data for table `oxpayments`
--

INSERT INTO `oxpayments` (`OXID`, `OXACTIVE`, `OXDESC`, `OXADDSUM`, `OXADDSUMTYPE`, `OXADDSUMRULES`, `OXFROMBONI`, `OXFROMAMOUNT`, `OXTOAMOUNT`, `OXVALDESC`, `OXCHECKED`, `OXDESC_1`, `OXVALDESC_1`, `OXDESC_2`, `OXVALDESC_2`, `OXDESC_3`, `OXVALDESC_3`, `OXLONGDESC`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXSORT`, `OXTIMESTAMP`, `OXPSPAYMORROWACTIVE`, `OXPSPAYMORROWMAP`)
VALUES
  ('oxiddebitnote', 1, 'Bankeinzug/Lastschrift', 1.22, 'abs', 0, 0, 0, 1000000,
   'lsbankname__@@lsblz__@@lsktonr__@@lsktoinhaber__@@', 0, 'Direct Debit',
   'lsbankname__@@lsblz__@@lsktonr__@@lsktoinhaber__@@', '', '', '', '',
   'Die Belastung Ihres Kontos erfolgt mit dem Versand der Ware.',
   'Your bank account will be charged when the order is shipped.', '', '', 22, '2014-09-12 08:51:25', 1, 2),
  ('oxidinvoice', 1, 'Rechnung', 0, 'abs', 0, 800, 0, 1000000, '', 1, 'Invoice', '', '', '', '', '', '', '', '', '', 11,
   '2014-09-12 08:50:54', 1, 1);

--
-- Adding user to user groups
--

INSERT IGNORE INTO `oxobject2group` (`OXID`, `OXSHOPID`, `OXOBJECTID`, `OXGROUPSID`) VALUES
('0397c2bd51210680340aac0f353eddc9', @SHOP_ID, 'admin@example.com', 'oxidadmin'),
('c397c2bd51210680340aac0f353eddc9', @SHOP_ID, '0affed7c43ee0b1743c12f9ea772caa9', 'oxidnewcustomer'),
('_c397c2bd51210680340aac0f353eddc', @SHOP_ID, 'd5eb34c373bc2f4bff034a9e834fe16d', 'oxidnewcustomer'),
('__c397c2bd51210680340aac0f353edd', @SHOP_ID, '1890bca2d23c74555c851454b3a8d936', 'oxidnewcustomer');

--
-- Force Azure theme
--
UPDATE `oxconfig` SET `OXVARVALUE` = 0x4db70f6d1a WHERE `OXVARNAME` = 'sTheme';
