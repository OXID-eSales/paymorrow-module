--
--  Dynamically select shop id to fit all shop editions
--  @SHOP_ID must be set with previous script, if not, 1 will be used
--

SELECT IF(@SHOP_ID IS NULL, 1, @SHOP_ID) INTO @SHOP_ID;

DELETE FROM `oxconfig` WHERE `OXVARNAME` IN ('paymorrowPublicKeyTest','paymorrowPaymorrowKeyTest','paymorrowKeysJson','paymorrowPrivateKeyTest','paymorrowMerchantIdTest');
INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`, `OXTIMESTAMP`) VALUES
('02415f2d4ce8405128c7c60d60d4ac87', @SHOP_ID, 'module:oxpspaymorrow', 'paymorrowPublicKeyTest', 'str', '', NOW()),
('09f5cb56f022e0361584f191b44730d2', @SHOP_ID, 'module:oxpspaymorrow', 'paymorrowPaymorrowKeyTest', 'str', '', NOW()),
('9be32a6647cc006c4d1176ef77f46128', @SHOP_ID, 'module:oxpspaymorrow', 'paymorrowKeysJson', 'str', '', NOW()),
('9c8722d5f35082368219add408a7ab5a', @SHOP_ID, 'module:oxpspaymorrow', 'paymorrowPrivateKeyTest', 'str', '', NOW()),
('be1c8a51877acd7978dccabc77c52159', @SHOP_ID, 'module:oxpspaymorrow', 'paymorrowMerchantIdTest', 'str', '', NOW());