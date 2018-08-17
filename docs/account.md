# Account

- [Login](#login)
- [Logout](#logout)
- [Registration](#registration)
    - [Simple user](#simple-user)
    - [Business account](#business-account)
    - [Confirm email](#confirm-email)
- [Convert to business account](#convert-to-business-account)
- [Profile](#profile)
    - [Change avatar](#change-avatar)
    - [Settings](#profile-settings)
    - [Username](#username)
    - [User id](#user-id)
    - [Check ban](#ban-check)
    - [Reset password](#reset-password)    
    - [Change password](#change-password)
    - [Clear search history](#clear-search-history)
    - [Deactivate account](#deactivate-account)
    - [Sessions history](#sessions-history)
- [Invitation](#invitation)

## Login

```php
$result = $bot->auth->login('mypinterestlogin', 'mypinterestpassword');
```
Login method returns `true` on success and `false` if fails:

```php
$result = $bot->auth->login('mypinterestlogin', 'mypinterestpassword');

if (!$result) {
    echo $bot->getLastError();
    die();
}
```

By default bot uses auto-login. It uses cookies, saved from the last session. If auto-login fails, then bot will 
send login requests. 

To skip auto-login and force login requests, you can pass `false` as the third argument:

```php
$result = $bot->auth->login('mypinterestlogin', 'mypinterestpassword', false);
```

Or you may skip login if you want. It is only required for such operations as likes, follows and making pins.
You can get your current logged in status via *isLoggedIn* method:

```php
if ($bot->auth->isLoggedIn()) {
    // ...
}
```

## Logout 

To logout use *logout* method:

```php
$bot->auth->logout();
```

## Registration

### Simple user

To register a new user:

```php
$bot->auth->register('youremail@gmail.com', 'password', 'Name');
```

Use `Registration` form object with fluent interface for specifying additional parameters:
```php

use seregazhuk\PinterestBot\Api\Forms\Registration;

$registration = new Registration('youremail@gmail.com', 'password', 'name');
$registration
    ->setAge(30)
    ->setCountry('DE')
    ->setMaleGender(); // ->setFemaleGender()

$bot->auth->register($registration);
```

### Business account

Register a business account. The last parameter with website url is *optional*:

```php
$bot->auth->registerBusiness('youremail@gmail.com', 'password', 'BusinessName');

$bot->auth->registerBusiness('youremail@gmail.com', 'password', 'BusinessName', 'http://yoursite.com');
```

Variant with Registration form:

```php
use seregazhuk\PinterestBot\Api\Forms\Registration;

$registration = new Registration('youremail@gmail.com', 'password', 'name');
$registration
    ->setAge(30)
    ->setCountry('DE')
    ->setMaleGender()
    ->setSite('http://yoursite.com');

$bot->auth->registerBusiness($registration);
```

### Confirm email

After registration you will receive a confirmation email. You can pass a link from this email to `confirmEmail`
method:

```php
$bot->auth->confirmEmail($linkFromEmail);
```

## Convert to business account 

Convert your account to a business one. Requires log in. The last parameter with website url is *optional*:

```php
$bot->auth->convertToBusiness('businessName');

$bot->auth->convertToBusiness('businessName', 'http://yoursite.com');
```

## Profile
Change profile. To update profile you need to setup `Profile` form object. It has following methods:
 - `setLastName($lastName)`,
 - `setFirstName($firstName)`,
 - `setUserName($username)`,
 - `setAbout($bio)`,
 - `setLocation($location)`,
 - `setWebsiteUrl($url)`,
 - `setCountry($code)` (ISO2 code). list of countries can be retrieved with `$bot->user->getCountries()` method,
 - `excludeFromSearch($bool)` to exclude your account from search results,
 - `setLocale($locale)`, list of locales can be retrieved with `$bot->user->getLocales()` method,
 - `setAccountType($type)` (only for business account) list of available types can be retrieved with `$bot->user->getAccountTypes()` method,
 - `setImage($pathToImage)`:

```php
use seregazhuk\PinterestBot\Api\Forms\Profile

$profileForm = (new Profile())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setAbout('My bio')
            ->setCountry('UK');
$bot->user->profile($profileForm);
```

### Change avatar

You can change your profile avatar by using `setImage()` method and a path to your image:

```php
use seregazhuk\PinterestBot\Api\Forms\Profile

$profileForm = (new Profile())->setImage($pathToFile);
$bot->user->profile($profileForm);
```

### Profile settings

You can get your current profile settings calling *profile* method without any params:

```php
$profile = $bot->user->profile();
echo $profile['username']; // Prints your username
```

### Username

In result you can find your username, and all your account settings.

Get your current username:

```php
$username = $bot->user->username();
```

### User id

Get your current user id:
```php
$userId = $bot->user->id();
```

### Ban check

Check if your account is banned:
```php
if ($bot->user->isBanned() {
    // You have ban
}
```

### Change password

Change you password:
```phpVisual similar pins
$bot->password->change('oldPassword', 'newPassword');
```

### Reset password
You can send to your email a link to reset your password:

```php
$bot->password->sendResetLink('youremail@gmail.com');
```

Then your can grab a link from email and pass use it to reset password:

```php
$bot->password->reset(
    'https://post.pinterest.com/f/a/your-password-reset-params',
    'newPassword'
);
```

### Clear search history

Remove things you’ve recently searched for from search suggestions:
```php
$bot->user->clearSearchHistory();
```

### Deactivate account

Deactivate current account:
```php
$bot->user->deactivate();
```

### Sessions history

Get sessions history:

```php
$history = $bot->user->sessionsHistory();
```

## Invitation

To invite someone by email:

```php
$bot->user->invite($email);
```
