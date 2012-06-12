# Redmine PHP SDK #

Provides a re-usable PHP library for interacting with the Redmine system's API.

* Author: Dave Reid http://www.davereid.net/
* Website: http://davereid.github.com/redmine-php-sdk/
* License: GPLv2/MIT
* [![Build Status](https://secure.travis-ci.org/davereid/redmine-php-sdk.png?branch=master)](http://travis-ci.org/davereid/redmine-php-sdk)

## Requirements ##

* PHP 5.3 or higher
* [cURL](http://us.php.net/manual/en/book.curl.php) extension
* [JSON](http://us.php.net/manual/en/book.json.php) extension
* [PHPUnit](http://www.phpunit.de/) (for unit testing)

## Usage ##

```
<?php
$server = 'http://demo.redmine.com/';
$api_key = '00000000000000000000000000000000'; // Valid Redmine API key
$connection = new RedmineConnection($server, $api_key);
$issue = RedmineIssue::load($connection, 1); // Load issue #1.
$issue->subject = 'New subject for issue #1.';
$issue->save();
?>
```

## License ##

The Redmine PHP SDK is dual licensed under the MIT and GPLv2 licenses.

## Unit Tests ##

To run the unit tests included with the SDK, you must have PHPUnit installed.
From the Redmine SDK directory, run `phpunit tests` to run all tests.
