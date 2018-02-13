# Errors handling

## Last error

You can check for occurred errors after requests with method *getLastError()*. It returns
string that contains error from you last request to API:
 
```php
$error = $bot->getLastError();
echo $error;
```
