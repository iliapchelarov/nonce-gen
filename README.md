# nonce-gen
<p align="center">
    <a href="https://github.com/inpsyde" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/571549" height="100px">
    </a>
    <h1 align="center">Demo Project Nonce Generation</h1>
    <a rel="author" href="https://github.com/iliapchelarov">Author: Ilia Pchelarov </a>
    <br>
</p>

Nonce generation & verification demo.

DESCRIPTION
-----------
The project aims at implementing nonce provisioning as used in WP Nonce, with [Composer](http://getcomposer.org/) and OOP practices.
_Specification:_
> A nonce is a "number used once" to help protect URLs and forms from certain types of misuse, malicious or otherwise. 
> WordPress nonces aren't numbers, but are a hash made up of numbers and letters. Nor are they used only once, but have a limited "lifetime" after which they expire. 
> During that time period the same nonce will be generated for a given user in a given context. The nonce for that action will remain the same for that user until that nonce life cycle has completed.

_Resources:_
*[Codex: WordPress Nonces](https://codex.wordpress.org/WordPress_Nonces)
*[How do WordPress nonces really work](https://www.bynicolas.com/code/wordpress-nonce/)
*[WordPress Nonces Vulnerabilities](https://codeseekah.com/2016/01/21/wordpress-nonces-vulnerabilities/)
*[Cryptographic nonce](https://en.wikipedia.org/wiki/Cryptographic_nonce)

For demo purposes only two basic functions are implemented:
*[wp_create_nonce](https://codex.wordpress.org/Function_Reference/wp_create_nonce)
*[wp_verify_nonce](https://codex.wordpress.org/Function_Reference/wp_verify_nonce)

The package is designed so that it permits configuration and plugin of different nonce generation mechanisms.

DIRECTORY STRUCTURE
-------------------

      config/             contains application configurations
      src/                contains model classes
      runtime/            contains files generated during runtime e.g. logs
      tests/              contains tests for the application
      vendor/             contains dependent 3rd-party packages installed via composer

REQUIREMENTS
------------

The minimum requirement by this project is that your host supports PHP 5.4.0.


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:
~~~
git clone https://github.com/iliapchelarov/nonce-gen.git 
composer update
~~~

CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=test',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

For testing purposes edit the file `config/test_db.php` to use SQLite:
```php
    $db['dsn'] = 'sqlite:@app/sqlite';
```

### Aliases & Autoloading

In the above code `@app` is an Yii defined alias for the application directory i.e ./
For aliases to work, you need to bootstrap the Yii framework first by including Yii.php in the autoloading process.
See example in the './yii' boot script:

```php
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.


TESTING
-------

Tests are located in `tests` directory. They are developed with [PHPUnit Testing Framework](https://phpunit.de/).

Tests can be executed by running

```
./vendor/bin/phpunit -c tests/phpunit.xml tests
```
or by using the utility script
```
./test
```

The command above will execute all unit tests located in the directory `tests`. Unit tests are testing the system components and simple integration scenarios.
They are basic system health-check, but not a replacement for user interaction (functional) - and acceptance tests. 

Enjoy!
------
