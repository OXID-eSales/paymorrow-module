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

## Bugs and Issues

If you experience any bugs or issues, please report them in the section **Module Paymorrow** of https://bugs.oxid-esales.com.

## User Manuals

Please find full user manuals inside `documentation/` folder.
 - [UserManual_de.pdf](documentation/UserManual_de.pdf)
 - [UserManual_en.pdf](documentation/UserManual_en.pdf)
