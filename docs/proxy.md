# Proxy usage

- [No authentication](#no-authentication)
- [With authentication](#with-authentication)
- [Socks](#socks)
- [Cancel](#cancel-proxy)
- [Check if uses](#check-if-uses)

## No authentication

To set up proxy settings use *useProxy* method:
```php
$bot->getHttpClient()->useProxy('192.168.1.1', '12345');
```

## With authentication

By default it uses *http* proxy without authentication. If your 
proxy requires authentication, pass auth string as the third parameter:

```php
$bot->getHttpClient()->useProxy('192.168.1.1', '12345', 'username:password');
```

## Socks

Use *socks* proxy:

```php
$bot->getHttpClient()->useSocksProxy('192.168.1.1', '12345');

// With authentication
$bot->getHttpClient()->useSocksProxy('192.168.1.1', '12345', 'username:password');
```

## Cancel proxy

If you need to stop sending requests via proxy:
```php
$bot->getHttpClient()->dontUseProxy();
```

## Check if uses

Check if bot uses proxy:
```php
if($bot->getHttpClient()->usesProxy()) {
    // ...
}
```
