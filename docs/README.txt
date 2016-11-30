==Title==
Paymorrow Payments

==Author==
OXID Professional Services

==Prefix==
oxps

==Shop Version==
5.0.x/4.7.x - 5.2.x/4.9.x

==Version==
1.0.1

==Link==
https://paymorrow.de/

==Mail==
info@oxid-esales.com

==Description==
Paymorrow Payments
>> Paymorrow-Homepage      : https://paymorrow.de/
>> Paymorrow-Händlerportal : https://paymorrow.net/perthPortal/
Paymorrow-Plugin (OxpsOxid2Paymorrow)

==Installation==
Activate the module in administration area.
It will automatically install docs/install.sql and update database views.
Configure Paymorrow module in Admin Backend ->  Extensions -> Modules -> Paymorrow Payments -> Settings [tab] -> API Configuration
 - Enter Paymorrow username for preferred mode (Live and/or Test)
 - Save the changes
 - Then Live/Test certificates registration button appear
 - Use buttons to open dialog, where You enter initialization code and generated certificate
Map your Payment methods Admin Backend -> Shop Settings -> Payments methods -> *any payment method available* -> Paymorrow [tab]
 - Activate a method as Paymorrow and press save
 - Choose payment method type and configure additional field
 - If there are no errors in the form save it

If You have many sub-shops and more languages than English and German,
please see comments in docs/install.sql to install all CMS snippets manually.

If Your shop version is 4.7.6/5.0.6 or older, You need to merge an admin template for module settings to extend:
 - Merge an example template from the module changed_full/application/views/admin/tpl/module_config.tpl
   with eShop template located in application/views/admin/tpl/module_config.tpl
 - Clear the cache by removing tmp/ folder content (all except .htaccess file)
Basically it is about adding a block named "admin_module_config_form" to the template.

==Extend==
 * order
  -- render
  -- _getNextStep
 * oxbasket
 * oxbasketitem
 * oxorder
  -- finalizeOrder
 * oxpayment
  -- isValidPayment
 * oxpaymentgateway
  -- executePayment
 * oxuser
 * oxuserpayment

==Modules==

==Modified original templates==

==Uninstall==
Disable the module in administration area, execute docs/uninstall.sql and delete module folder.
If You have many sub-shops, please see comments in docs/uninstall.sql to remove all CMS snippets manually.
