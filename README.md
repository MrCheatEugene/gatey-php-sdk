# gatey-php-sdk
PHP version of Gatey SDK.

# Install

Just include `GateySDK.php`

# Example usage

```php
<?php
include 'GateySDK.php';
$id=0; // project id
$gateysdk = new GateySDK($id, 'CLIENT KEY');

$gateysdk->capture_message("Capture me!","DEBUG");

set_exception_handler(array($gateysdk, 'catch'));
throw new Exception("This exception will be catched");

restore_exception_handler();
throw new Exception("And that won't");
```

# Usage

You can initialize the SDK with client key or the server key, like this for client:
```php
$id=0; // project id
$gateysdk = new GateySDK($id, 'CLIENT KEY');
```
and for server:
```php
$id=0; // project id
$gateysdk = new GateySDK($id, null,'SERVER KEY');
```

You can also specify API endpoint:
```php
$id=0; // project id
$gateysdk = new GateySDK($id, null,'SERVER KEY', 'https://gatey.endpoint/v1');
```

To capture a message, use `capture_message` method:
```php
$gateysdk->capture_message("Any message here","LEVEL");
```

If you want to use gatey to handle and catch any exceptions, use
```php
set_exception_handler(array($gateysdk, 'catch'));
```

and to restore original handler:
```php
restore_exception_handler();
```

# License

This library is distributed under The Unlicense license, which means that the code is in the public domain.
