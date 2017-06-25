# Paymorrow Payments

Paymorrow Payments
- Paymorrow-Homepage      : https://paymorrow.de/
- Paymorrow-Händlerportal : https://paymorrow.net/perthPortal/

Paymorrow-Plugin (OxpsOxid2Paymorrow)

## Installation

### Module installation via composer

In order to install the module via composer, run one of the following commands in commandline of your shop base directory 
(where the shop's composer.json file resides).
* **composer require oxid-esales/paymorrow-module:^2.0** to install the released version compatible with OXID eShop RC2
* **composer require oxid-esales/paymorrow-module:dev-master** to install the latest unreleased version from github

### Activate Module

Activate the module in administration area.

It will automatically install `docs/install.sql` and update database views.

### Configure Module

Configure Paymorrow module in `Admin Backend ->  Extensions -> Modules -> Paymorrow Payments -> Settings [tab] -> API Configuration`:
 - Enter Paymorrow username for preferred mode (Live and/or Test)
 - Save the changes
 - Then Live/Test certificates registration button appear
 - Use buttons to open dialog, where You enter initialization code and generated certificate

### Configure Payment Methods

Map Your Payment methods `Admin Backend -> Shop Settings -> Payments methods -> [any payment method available] -> Paymorrow [tab]`:
 - Activate a method as Paymorrow and press save
 - Choose payment method type and configure additional field
 - If there are no errors in the form save it

If You have many sub-shops and more languages than English and German,
please see comments in `docs/install.sql` to install all CMS snippets manually.

### For Old eShop Version

If Your shop version is 4.7.6/5.0.6 or older, You need to merge an admin template for module settings to extend:
 - Merge an example template from the module changed_full/application/views/admin/tpl/module_config.tpl
   with eShop template located in application/views/admin/tpl/module_config.tpl
 - Clear the cache by removing tmp/ folder content (all except .htaccess file)

Basically it is about adding a block named "admin_module_config_form" to the template.

## User Manuals

Please find full user manuals inside `documentation/` folder.
 - [UserManual_de.pdf](documentation/UserManual_de.pdf)
 - [UserManual_en.pdf](documentation/UserManual_en.pdf)
