# Pinterest Bot for PHP

[![Build Status](https://travis-ci.org/seregazhuk/php-pinterest-bot.svg)](https://travis-ci.org/seregazhuk/php-pinterest-bot)
[![Circle CI](https://circleci.com/gh/seregazhuk/php-pinterest-bot.svg?style=shield)](https://circleci.com/gh/seregazhuk/php-pinterest-bot)
[![Code Climate](https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/gpa.svg)](https://codeclimate.com/github/seregazhuk/php-pinterest-bot)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/?branch=master)
[![Test Coverage](https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/coverage.svg)](https://codeclimate.com/github/seregazhuk/php-pinterest-bot/coverage)
[![Total Downloads](https://poser.pugx.org/seregazhuk/pinterest-bot/downloads)](https://packagist.org/packages/seregazhuk/pinterest-bot)
[![StyleCI](https://styleci.io/repos/39557985/shield)](https://styleci.io/repos/39557985)

This PHP class will help you to work with your Pinterest account. You don't
need to register application in Pintererst to get access token for api. Use
only your account login and password.

Some functions use pinterest navigation through results, for example,
get user followers. Function returns generator object with api results as batches in 
every iteration. By default functions return all pinterest result batches, but you can 
pass batches num as second argument. For example, 
```php 
$bot->pins->search('query', 2)
```
will return only 2 batches of search results.

## Dependencies

Library requires CURL extension and PHP 5.6 or above.

## Installation
Via composer:
```
composer require "seregazhuk/pinterest-bot:*"
```

## Quick Start

```php 
// You may need to amend this path to locate composer's autoloader
require('vendor/autoload.php'); 

use seregazhuk\PinterestBot\Factories\PinterestBot;

$bot = PinterestBot::create();

// login
$bot->login('mypinterestlogin', 'mypinterestpassword');

// get lists of your boards 
$boards = $bot->boards->forUser('yourUserName');
$bot->pins->create('http://exmaple.com/image.jpg', $boards[0]['id'], 'pin description');
```

Or you may skip login, if you want. It is only required for such operations as likes, follows and making pins.

## Boards

Get all user's boards 
```php
$boards = $bot->boards->forUser($username);
```

Get full board info by boardName and userName. Here you can get board id, for further functions
(for examaple, pin creating or following boards).
```php
$info = $bot->boards->info($username, $board);
```

Create a new board. As third parameter you can pass privacy. It is *public* by default, or *secret* if private.
```php
// create a public board
$bot->boards->create('name', 'description');

// create a private board
$bot->boards->create('name', 'description', 'secret');
```

Delete board by id
```php
$bot->boards->delete($boardId);
```

Follow/unfollow board by ID
```php
$bot->boards->follow($boardId);
$bot->boards->unfollow($boardId);
```

Get all pins for board by ID
```php
foreach($bot->boards->pins($boardId) as $pinsBatch)
{
    // ...
}
```

Get board followers. Uses pinterest api pagination.
```php
foreach($bot->boards->followers($boardId) as $followersBatch)
{
	// ...
}
```

## Pins

Notice! Try not to be very aggressive when pinning or commetning pins, or pinterest will gonna ban you.

Get pin info by its id.
```php
$info = $bot->pins->info(1234567890);
```

Create new pin. Accepts image url, board id, where to post image, description and preview url.

```php
$pinId = $bot->pins->create('http://exmaple.com/image.jpg', $boards[0]['id'], 'pin description');
```
    
Repin other pin by its id.
```php
$bot->pins->repin($pinId, $boards[0]['id'], 'my repin');
``` 
Delete pin by id.
```php
$bot->pins->delete($pinId);
```   
Like/dislike pin by id.
```php
$bot->pins->like($pinId);
$bot->pins->unLike($pinId);
```
Write a comment.
```php
$result = $bot->pins->comment($pinId, 'your comment'); 
// Result contains info about written comment. For example,
// comment_id if you want to delete it.
```

Delete a comment.
```php
$bot->pins->deleteComment($pinId, $commentId);
```

## Pinners

Follow/unfollow user by ID
```php
$bot->pinners->follow($userId);
$bot->pinners->unfollow($userId);
```	
Get user info by username
```php
$userData = $bot->pinners->info($username);
```	
Get user following. Uses pinterest api pagination.
```php
foreach($bot->pinners->following('username') as $followingBatch)
{
	// ...
}
```
Get user followers. Uses pinterest api pagination.
```php
foreach($bot->pinners->followers('username') as $followersBatch)
{
	// ...
}
```

## Interests
Follow/unfollow interest by ID
```php
$bot->interests->follow($interestId);
$bot->interests->unfollow($interestId);
```
##Conversations

### Write a message
Write a message to user by id. You may specify one user by id, or pass an array of user ids. 

```php
$bot->conversations->sendMessage($userId, 'message text');
```

Add pin by id to message
```php
$pinId = 123456789;
$bot->conversations->sendMessage($userId, 'message text', $pinId);
```

### Send email
Email param may be string or array of emails.
```php
$bot->conversations->sendEmail('mail@domain.com', 'message text');
```

Attach pin to email
```php
$bot->conversations->sendEmail('mail@domain.com', 'message text', $pindId);
```

Get array of last conversations
```php
$conversations = $bot->conversations->last();
```

## Search

Search functions use pinterest pagination in fetching results and return generator.
```php
foreach($bot->pins->search('query') as $pinsBatch)
{
	// ...
}

foreach($bot->pinners->search('query') as $pinnersBatch)
{
	// ...
}

foreach($bot->boards->search('query') as $boardsBatch);
{
	// ...
}
```

## User Settings
Change profile. Available settings are: *last_name*, *first_name*, *username*, *about*, *location* and *website_url*:
```php
$bot->user->profile(['first_name'=>'My_name']);
```

## News
Get latest user's news:
```php
$news = $bot->news->latest();
```


## Errors handling
You can check for occurred errors after requests with method *getLastError()*:
```php
$error = $bot->getLastError();
print_r($error);
```

## Custom settings
You can set UserAgent string for bot like this:
```php
$userAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0';
$bot = PinterestBot::create($userAgent);
```
