DELETE FROM `oxconfig` WHERE `OXVARNAME` in ('blConfirmAGB', 'iMinOrderPrice');
DELETE FROM `oxpayments` where `OXDESC` like '%test%';
