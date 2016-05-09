# Pinterest Bot for PHP

<p align="center">
    <img src="image.jpg" alt="Pinterest PHP Bot">
</p>

<p align="center">
	<a href="https://travis-ci.org/seregazhuk/php-pinterest-bot"><img src="https://camo.githubusercontent.com/412fbc5888a7d03937daf554d662678477512375/68747470733a2f2f7472617669732d63692e6f72672f7365726567617a68756b2f7068702d70696e7465726573742d626f742e737667" alt="Build Status" data-canonical-src="https://travis-ci.org/seregazhuk/php-pinterest-bot.svg" style="max-width:100%;"></a>
	<a href="https://circleci.com/gh/seregazhuk/php-pinterest-bot"><img src="https://camo.githubusercontent.com/3577832f301d8615a2275d36aa3e274765ffb995/68747470733a2f2f636972636c6563692e636f6d2f67682f7365726567617a68756b2f7068702d70696e7465726573742d626f742e7376673f7374796c653d736869656c64" alt="Circle CI" data-canonical-src="https://circleci.com/gh/seregazhuk/php-pinterest-bot.svg?style=shield" style="max-width:100%;"></a>
	<a href="https://codeclimate.com/github/seregazhuk/php-pinterest-bot"><img src="https://camo.githubusercontent.com/d30f6b154177d3a589af46e154b31ec66ef05128/68747470733a2f2f636f6465636c696d6174652e636f6d2f6769746875622f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f6770612e737667" alt="Code Climate" data-canonical-src="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/gpa.svg" style="max-width:100%;"></a>
	<a href="https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/?branch=master"><img src="https://camo.githubusercontent.com/c05b7ed73a9c49224fb982888fc7ac643fbec5f6/68747470733a2f2f7363727574696e697a65722d63692e636f6d2f672f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f7175616c6974792d73636f72652e706e673f623d6d6173746572" alt="Scrutinizer Code Quality" data-canonical-src="https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/badges/quality-score.png?b=master" style="max-width:100%;"></a>
	<a href="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/coverage"><img src="https://camo.githubusercontent.com/704321dd17ccb2c5de0e954e1c29a8cefa2572be/68747470733a2f2f636f6465636c696d6174652e636f6d2f6769746875622f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f636f7665726167652e737667" alt="Test Coverage" data-canonical-src="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/coverage.svg" style="max-width:100%;"></a>
	<a href="https://packagist.org/packages/seregazhuk/pinterest-bot"><img src="https://camo.githubusercontent.com/8957462146926452326740b5c6255bbdb3abea67/68747470733a2f2f706f7365722e707567782e6f72672f7365726567617a68756b2f70696e7465726573742d626f742f646f776e6c6f616473" alt="Total Downloads" data-canonical-src="https://poser.pugx.org/seregazhuk/pinterest-bot/downloads" style="max-width:100%;"></a>
</p>

This PHP library will help you to work with your Pinterest account like with API calls, but without using any 
API account credentials. 

To have access to Pinterest API you need to go to developers.pinterest.com and register as a developer, 
then register your application, then wait for confirmation, and only then you will get an access token. With this 
library you are already to go. Just use only your account login and password, like you do it in your browser. 
But even your account is not required, if your don't use such operations as creating pins, writing comments or sending
messages!

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

*Note*: Some functions use pinterest navigation through results, for example,
get user followers or search queries. These functions return a generator object with api results as batches in 
every iteration. By default functions return all pinterest result batches, but you can 
pass batches num as a second argument. For example, 
```php 
$bot->pins->search('query', 2)
```
will return only 2 batches of search results.

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
    
You can specify a link for pin (source) as fourth argument. If not set, link is equal to image url.    
```php
$pinId = $bot->pins->create('http://exmaple.com/image.jpg', $boards[0]['id'], 'pin description', 'http://site.com');
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

## How can I thank you?
Why not star the github repo? I'd love the attention!

Thanks! 
