# Paymorrow Tests

## Requirements

Both unit and acceptance tests require OXID Testing Library installed.
See https://github.com/OXID-eSales/testing_library

To run acceptance tests Selenium server is required.
If OXID VM is used (https://github.com/OXID-eSales/oxvm_eshop), in `personal.yml` add the following:

```
selenium:
  install: true
```

If VM already exist, after the `personal.yml` changes run:

```
vagrant provision
```

### Configuration

Here is an example of Testing Library configuration file `oxideshop/test_config.yml`

```
# This file is auto-generated during the composer install
mandatory_parameters:
    shop_path: /var/www/oxideshop/source
    shop_tests_path: /var/www/oxideshop/tests
    partial_module_paths: oxps/paymorrow
optional_parameters:
    shop_url: null
    shop_serial: ''
    enable_varnish: false
    is_subshop: false
    install_shop: false
    remote_server_dir: null
    shop_setup_path: null
    restore_shop_after_tests_suite: false
    test_database_name: null
    restore_after_acceptance_tests: false
    restore_after_unit_tests: false
    tmp_path: /tmp/oxid_test_library/
    database_restoration_class: DatabaseRestorer
    activate_all_modules: false
    run_tests_for_shop: false
    run_tests_for_modules: true
    screen_shots_path: null
    screen_shots_url: null
    browser_name: firefox
    selenium_server_ip: 127.0.0.1
    selenium_server_port: '4444'
    additional_test_paths: null
```

## Unit Tests

To execute unit tests run the following:

```
cd /var/www/oxideshop/
vendor/bin/runtests
```

## Acceptance tests

### Requirements for acceptance tests

Besides general requirements (see section on top), the following is needed to run Acceptance tests:

 - Installed and working OXID eShop with standard Demo Data
 - The eShop environment should be able to reach Paymorrow Test API
 - Paymorrow Test Credentials are required and configuration performed (see next section)
 - Depending on testing system, Test Library config might require adjustments

### Acceptance tests configuration

Copy `oxideshop/source/modules/oxps/paymorrow/tests/paymorrow_config.php.dist` to 
`oxideshop/source/modules/oxps/paymorrow/tests/paymorrow_config.php` 
and replace placeholders like "<Paymorrow ...>" with valid Paymorrow API test credentials.


### Acceptance tests execution

To execute acceptance tests run the following:

```
cd /var/www/oxideshop/
vendor/bin/runtests-selenium
```

### Manual test data reset

For manual test data reset, please execute SQL commands from `oxideshop/source/modules/oxps/paymorrow/tests/acceptance/deletetTestSql.sql`
