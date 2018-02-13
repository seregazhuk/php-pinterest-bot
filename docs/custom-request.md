# Custom request settings

- [Curl options](#curl-options)
- [Client info](#client-info)
- [Current url](#current-url)
- [Visit page](#visit-page)

## Curl options

It is possible to add some additional Curl options for bot requests. For example, you can
set proxy and User Agent like this:

```php
$bot->getHttpClient()->setOptions([
    CURLOPT_PROXY => 'xx.xx.xxx.xx:xxxx',
    CURLOPT_PROXYTYPE => CURLPROXY_HTTP // Or CURLPROXY_SOCKS5,
    CURLOPT_USERAGENT => 'Your_User_Agent',
]);
```

## Client info

With every request Pinterest returns an array with your current client info, with such info as 
OS, browser, IP and others:

```php
$info = $bot->getClientInfo();
```

By default it uses client info from the last request. To reload client context pass `true` argument:

```php
// Force to reload client info
$info = $bot->getClientInfo(true);
```

## Current url

You can get an url of the last visited page:
```php
$url = $bot->getHttpClient()->getCurrentUrl();
```

## Visit page

Visit (click) a link. For example, when Pinterest sends you email with some link, and you want bot to visit it:
```
$bot->user->visitPage($url);
```
