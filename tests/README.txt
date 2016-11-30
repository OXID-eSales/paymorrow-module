Paymorrow Tests Folder
======================

Unit tests
 - Requirements
    - PHPUnit 3.7.x
    - (optional) OXMD installed in shop root folder
    - Make sure *.sh files in the test folder have execute permissions
    - Enable the module in eShop back end
 - How to run Unit tests
    - Go to the test folder in Linux console, example location "/var/www/my_shop/modules/oxps/paymorrow/tests"
    - For Unit tests execute ./runtests.sh
    - For HTML coverage report execute ./runcoverage.sh
    - For OXMD metrics and certification cost report execute ./runcertification.sh

Acceptance tests
 - Requirements
    - PHPUnit 3.7.x, Xvfb, Java, Firefox 10 installed on Linux system
    - Make sure *.sh files in the test folder have execute permissions
 - What environment is needed
    - OXID eShop CE 4.9+ clean installation with Germany as main country, UTF-8 mode and default demo data
    - Of course copy the module files into eShop
    - Import into eShop database additional data from test folder, file "acceptance/demodata_paymorrow.sql"
        - NOTE: eShop admin user name will now be "admin@example.com", password - "admin@example.com"
    - Make sure content of test folder "acceptance/testData/" is copied to shop root directory
    - Shop root directory should be writable
    - Make sure Paymorrow module is activated, configured and payment methods mapped
 - Configuration
    - Copy Paymorrow settings file "paymorrow_config.sample.php" to "paymorrow_config.php"
    - Fill in Paymorrow setting values (keys starting with "PAYMORROW_SETTING_...") in the "paymorrow_config.php" file
    - Optionally You might want to change more values there and also in "test_config.php" file
 - How to start Xvfb and Selenium server
    - Go to the test folder in Linux console, example location "/var/www/my_shop/modules/oxps/paymorrow/tests"
    - Execute the following script ./startseleniumserver.sh
    - It starts Xvfb and Selenium server - this console windows now shows Selenium server log output
 - How to run Selenium tests
    - Go to the test folder in a new console window
    - For Selenium tests execute ./runselenium.sh
 - Troubleshooting
    - Follow console output for errors
        - If some Linux library is missing during Selenium server start or tests execution, then please install it
    - Double check if all set up and configuration steps were fulfilled
    - To make sure that Selenium server is running, try to access http://[YOUR_VM_IP]:4444/wd/hub
        - If Selenium server was already started and You want to start ir again, kill "java" and "Xvfb" processes
            - To find processes IDs use:
                ps -aux | grep Xvfb
                ps -aux | grep java
            - To kill processes use
                kill -9 [pid]
              Where [pid] stand for corresponding process ID.
    - To see if tests are sending data to Selenium check the console where Selenium server is started
    - When tests fail, check tests output and screenshots in eShop root, "selenium_screenshots/" folder
        - If tests fail on payment page with snipping wheel visible, try to increase paymorrow service delay in config
    - Try to disable the PAymorrow module and then starting Selenium tests
