--
--  Take configs from shop 1 to subshop
--  @SHOP_ID must be set with previous script, if not, 1 will be used
--

SELECT IF(@SHOP_ID IS NULL, 1, @SHOP_ID) INTO @SHOP_ID;

delete from oxconfig where oxshopid = 2 and (oxvarname like "%Module%" or oxmodule = 'module:oxpspaymorrow');
replace into oxconfig (oxid, oxshopid, oxmodule, oxvarname, oxvartype, oxvarvalue, oxtimestamp) select concat(2, oxid), 2, oxmodule, oxvarname, oxvartype, oxvarvalue, now() from oxconfig where oxshopid=1 and (oxvarname like "%Module%" or oxmodule = 'module:oxpspaymorrow');
