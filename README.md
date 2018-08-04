# Pinterest Bot for PHP

<p align="center">
    <img src="logo.jpg" alt="Pinterest PHP Bot">
</p>

##
<a href="https://travis-ci.org/seregazhuk/php-pinterest-bot"><img src="https://camo.githubusercontent.com/412fbc5888a7d03937daf554d662678477512375/68747470733a2f2f7472617669732d63692e6f72672f7365726567617a68756b2f7068702d70696e7465726573742d626f742e737667" alt="Build Status" data-canonical-src="https://travis-ci.org/seregazhuk/php-pinterest-bot.svg" style="max-width:100%;"></a>
<a href="https://codeclimate.com/github/seregazhuk/php-pinterest-bot"><img src="https://camo.githubusercontent.com/d30f6b154177d3a589af46e154b31ec66ef05128/68747470733a2f2f636f6465636c696d6174652e636f6d2f6769746875622f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f6770612e737667" alt="Code Climate" data-canonical-src="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/gpa.svg" style="max-width:100%;"></a>
<a href="https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/?branch=master"><img src="https://camo.githubusercontent.com/c05b7ed73a9c49224fb982888fc7ac643fbec5f6/68747470733a2f2f7363727574696e697a65722d63692e636f6d2f672f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f7175616c6974792d73636f72652e706e673f623d6d6173746572" alt="Scrutinizer Code Quality" data-canonical-src="https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/badges/quality-score.png?b=master" style="max-width:100%;"></a>
<a href="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/coverage"><img src="https://camo.githubusercontent.com/704321dd17ccb2c5de0e954e1c29a8cefa2572be/68747470733a2f2f636f6465636c696d6174652e636f6d2f6769746875622f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f636f7665726167652e737667" alt="Test Coverage" data-canonical-src="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/coverage.svg" style="max-width:100%;"></a>
<a href="https://styleci.io/repos/39557985"><img src="https://styleci.io/repos/39557985/shield?branch=master" alt="StyleCI"></a>
<a href="https://packagist.org/packages/seregazhuk/pinterest-bot"><img class="spinned latest_stable_version_img" src="https://poser.pugx.org/seregazhuk/pinterest-bot/v/stable" style="display: inline;"></a>
<a href="https://packagist.org/packages/seregazhuk/pinterest-bot"><img src="https://camo.githubusercontent.com/8957462146926452326740b5c6255bbdb3abea67/68747470733a2f2f706f7365722e707567782e6f72672f7365726567617a68756b2f70696e7465726573742d626f742f646f776e6c6f616473" alt="Total Downloads" data-canonical-src="https://poser.pugx.org/seregazhuk/pinterest-bot/downloads" style="max-width:100%;"></a>

A PHP library to help you work with your Pinterest account without API credentials. 

The Pinterest API is painful: receiving an access token involves registering a developer account, 
registering an application, then waiting for confirmation. Not to mention, the
public API itself is poorly implemented and has a limited set of features. 

This library offers the full functionality available on Pinterest's website, with **no need to register an
application to receive an access token**. All that's needed is your account login information (but even this is not required
if you don't plan on creating pins, writing comments or sending messages)!

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Examples](#examples)
- [Account](docs/account.md)
- [Boards](docs/boards.md)
- [Pins](docs/pins.md)
- [Pinners](docs/pinners.md)
- [Interests and topics](docs/interests-and-topics.md)
- [Search](docs/search.md)
- [Inbox](docs/inbox.md)
- [Keywords](docs/keywords.md)
- [Error handling](docs/errors-handling.md)
- [Use proxy](docs/proxy.md)
- [Custom request settings](docs/custom-request.md)
- [Cookies](docs/cookes.md)
- [Pagination](docs/pagination.md)


## Installation

### Dependencies
Library requires CURL extension and PHP 7.0 or above.

The recommended way to install this library is via [Composer](https://getcomposer.org). 
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

```
composer require seregazhuk/pinterest-bot
```

## Quick Start

```php 
// You may need to amend this path to locate Composer's autoloader
require('vendor/autoload.php'); 

use seregazhuk\PinterestBot\Factories\PinterestBot;

$bot = PinterestBot::create();

// Login
$bot->auth->login('mypinterestlogin', 'mypinterestpassword');

// Get lists of your boards
$boards = $bot->boards->forUser('yourUserName');

// Create a pin
$bot->pins->create('http://exmaple.com/image.jpg', $boards[0]['id'], 'Pin description');
```

*Note*: Some methods (e.g. get user followers/following, pins
likes/dislikes, search and other feed queries) use Pinterest navigation through results (with bookmarks). This means that for every batch of results, a 
call is made to Pinterest and a [Pagination](#pagination) object with Pinterest API results is returned.

**To avoid being banned by Pinterest**, do not aggressively pin or write comments (e.g. creating hundreds of pins in a single minute).
Do this by inserting timeouts (`$bot->wait($seconds)`) with calls.

## Examples
These articles provide examples of common tasks that can be performed with the bot:

 - [Automate pinning](http://seregazhuk.github.io/2017/03/25/build-pinterest-bot-with-php-auto-pin/) ([source code](examples/auto_pins.php))
 - [Multiple Accounts and Proxy](http://seregazhuk.github.io/2017/03/28/build-printerest-bot-with-php-multiple-accounts/) ([source code](examples/multiple_accounts_and_proxy.php))
 - [Comments, Likes And Repins](http://seregazhuk.github.io/2017/03/30/build-pinterest-bot-with-php-comments-and-repins/) ([source code](examples/comments_likes_repins.php))
 - [Followers](http://seregazhuk.github.io/2017/04/01/build-pinterest-bit-with-php-followers/) ([source code](examples/followers.php))
 - [Parsing Pins](http://seregazhuk.github.io/2017/04/04/build-pinterest-bot-with-php-parsing-pins/) ([source code](examples/pins_parser.php))


## How can I thank you?
Why not star this GitHub repo? I'd love the attention!
Or, you can donate to my project on PayPal:

[![Support me with some coffee](https://img.shields.io/badge/donate-paypal-orange.svg)](https://www.paypal.me/seregazhuk)

Thanks! 
