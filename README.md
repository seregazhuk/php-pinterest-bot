# Pinterest Bot for PHP

[![Build Status](https://travis-ci.org/seregazhuk/php-pinterest-bot.svg)](https://travis-ci.org/seregazhuk/php-pinterest-bot)
[![Circle CI](https://circleci.com/gh/seregazhuk/php-pinterest-bot.svg?style=shield)](https://circleci.com/gh/seregazhuk/php-pinterest-bot)
[![Code Climate](https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/gpa.svg)](https://codeclimate.com/github/seregazhuk/php-pinterest-bot)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/?branch=master)
[![Test Coverage](https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/coverage.svg)](https://codeclimate.com/github/seregazhuk/php-pinterest-bot/coverage)
[![Total Downloads](https://poser.pugx.org/seregazhuk/pinterest-bot/downloads)](https://packagist.org/packages/seregazhuk/pinterest-bot)

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

API requires CURL extension and PHP 5.5 or above.

## Installation
Via composer:
```
php composer.phar require "seregazhuk/pinterest-bot:*"
```

## Quick Start

```php 
use seregazhuk\PinterestBot\PinterestBot;

$bot = new PinterestBot('mypinterestlogin', 'mypinterestpassword');
$bot->login();
```

Next, get your list of boards:

```php
$boards = $bot->boards->my();
```

## Pins

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
$bot->pins->comment($pinId, 'your comment');
```

Delete a comment.
```php
$bot->pins->deleteComment($pinId, $commentId);
```

## Pinners

Get your account name
```php
$bot->pinners->myAccountName(); 
```	
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
## Boards
Follow/unfollow board by ID
```php
$bot->boards->follow($boardId);
$bot->boards->unfollow($boardId);
```

## Interests
Follow/unfollow interest by ID
```php
$bot->interests->follow($interestId);
$bot->interests->unfollow($interestId);
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

foreach($bot->pinners->search('query') as $boardsBatch);
{
	// ...
}
```
