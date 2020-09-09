# API Testing with PHP
This project based on linked in course: [Api Testing and Validation]( https://www.linkedin.com/learning/api-testing-and-validation) by Keith Casey.

It's about creating api automation test with behavior driven development using php and behat

## Tools instalation
- PHP 7
- Guzzle for http request library
- [Behat 3.* ](https://github.com/Behat/Behat) as BDD framework
- [Composer](https://getcomposer.org/download/) 

You need composer in order to install guzzle and behat library, run [composer command](https://getcomposer.org/download/) in your terminal
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');”
php -r "if (hash_file('sha384', 'composer-setup.php') === '8a6138e2a05a8c28539c9f0fb361159823655d7ad2deecb371b04a83966c61223adc522b0189079e3e9e277cd72b8897') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;”
php composer-setup.php
php -r "unlink('composer-setup.php');”
```

- Install guzzle
```
php composer.phar require guzzlehttp/guxxle 6.*
```
- install Behat
```
php composer.phar require behat/behat3.7.0
```

- Or you can set the config in json file then run this command in your directory
```
php composer.phar install
```

### Test Structure
All your test case will be under features folder. run this command to generate features, features/bootsrap, and  features/bootsrtap/FeatureContext.php
```
vendor/bin/behat —-init 
```

behat.yml is a file you put configuration. in this case, i used to save the credentials that neeed for test data

### How to Run Test 
```
vendor/bin/behat 
```
or 
```
vendor/bin/behat <filename.feature>
```