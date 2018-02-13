# Cookies

- [Get cookies list](#get-cookies-list)
- [Get cookie by value](#get-cookie-by-name)
- [Change cookies storage path](#change-cookies-storage-path)
- [Remove cookies](#remove-cookies)

## Get cookies list

Current bot cookies are available through getHttpClient and cookie/cookies methods.
All cookies:

```php
$cookies = $bot->getHttpClient()->cookies();
```

## Get cookie by name

Cookie value by name:
```php
$someCookieValue = $bot->getHttpClient()->cookie('cookieName');
```

## Change cookies storage path

By default cookie files are stored in your system temp directory. You can set custom path to store cookies. 
**Notice!** This path must have write permissions:

```php
$bot->getHttpClient()->setCookiesPath($yourCustomPathForCookies);

$currentPath = $bot->getHttpClient()->getCookiesPath();
```

## Remove cookies

Remove your cookies:
```php
$bot->getHttpClient()->removeCookies();
```
