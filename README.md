# Pinterest Bot for PHP

<p align="center">
    <img src="logo.jpg" alt="Pinterest PHP Bot">
</p>

##
<a href="https://travis-ci.org/seregazhuk/php-pinterest-bot"><img src="https://camo.githubusercontent.com/412fbc5888a7d03937daf554d662678477512375/68747470733a2f2f7472617669732d63692e6f72672f7365726567617a68756b2f7068702d70696e7465726573742d626f742e737667" alt="Build Status" data-canonical-src="https://travis-ci.org/seregazhuk/php-pinterest-bot.svg" style="max-width:100%;"></a>
<a href="https://codeclimate.com/github/seregazhuk/php-pinterest-bot"><img src="https://camo.githubusercontent.com/d30f6b154177d3a589af46e154b31ec66ef05128/68747470733a2f2f636f6465636c696d6174652e636f6d2f6769746875622f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f6770612e737667" alt="Code Climate" data-canonical-src="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/gpa.svg" style="max-width:100%;"></a>
<a href="https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/?branch=master"><img src="https://camo.githubusercontent.com/c05b7ed73a9c49224fb982888fc7ac643fbec5f6/68747470733a2f2f7363727574696e697a65722d63692e636f6d2f672f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f7175616c6974792d73636f72652e706e673f623d6d6173746572" alt="Scrutinizer Code Quality" data-canonical-src="https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/badges/quality-score.png?b=master" style="max-width:100%;"></a>
<a href="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/coverage"><img src="https://camo.githubusercontent.com/704321dd17ccb2c5de0e954e1c29a8cefa2572be/68747470733a2f2f636f6465636c696d6174652e636f6d2f6769746875622f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f636f7665726167652e737667" alt="Test Coverage" data-canonical-src="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/coverage.svg" style="max-width:100%;"></a>
<a href="https://packagist.org/packages/seregazhuk/pinterest-bot"><img class="spinned latest_stable_version_img" src="https://poser.pugx.org/seregazhuk/pinterest-bot/v/stable" style="display: inline;"></a>
<a href="https://packagist.org/packages/seregazhuk/pinterest-bot"><img src="https://camo.githubusercontent.com/8957462146926452326740b5c6255bbdb3abea67/68747470733a2f2f706f7365722e707567782e6f72672f7365726567617a68756b2f70696e7465726573742d626f742f646f776e6c6f616473" alt="Total Downloads" data-canonical-src="https://poser.pugx.org/seregazhuk/pinterest-bot/downloads" style="max-width:100%;"></a>

[![Support me with some coffee](https://img.shields.io/badge/donate-paypal-orange.svg)](https://www.paypal.me/seregazhuk)


This PHP library will help you to work with your Pinterest account without using any API account credentials. 

To have an access to Pinterest API you need to go to <a target="_blank" href="https://developers.pinterest.com">developers.pinterest.com</a>
and register as a developer, then register your application, then wait for confirmation, and only then you 
will get an access token. Furthermore, its public API is very poor and has a very limited set of features. With this
library you have the entire set of functions, which available on Pinterest website. And there is no need to register an
application to receive an access token. Just use your account login and password, like you do it in your browser. But even your account is not required,
if you don't use such operations as creating pins, writing comments or sending messages!

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Examples](#examples)
- [Account](#account)
- [Boards](#boards)
- [Pins](#pins)
- [Pinners](#pinners)
- [Interests](#interests)
- [Topics](#topics)
- [Search](#search)
- [Inbox](#inbox)
    - [News](#news)
    - [Notifications](#notifications)
    - [Conversations](#conversations)
    - [Write a message](#write-a-message)
    - [Send email](#send-email)
    - [Contact requests](#contact-requests)
- [Keywords](#keywords)
- [Errors handling](#errors-handling)
- [Use proxy](#use-proxy)
- [Custom request settings](#custom-request-settings)
- [Cookies](#cookies)
- [Pagination](#pagination)


## Installation

### Dependencies
Library requires CURL extension and PHP 5.5.9 or above.

Install via [Composer](http://getcomposer.org):
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

// Wait 5 seconds
$bot->wait(5);
```

*Note*: Some methods use pinterest navigation through results (with bookmarks), for example, get user followers/following, pins
likes/dislikes, search and other feed queries. This means that for every batch of results there will be a 
request to Pinterest. These methods return a [Pagination](#pagination) object with Pinterest api results.

**How to avoid banned from Pinterest**: don't bee too aggressive making pins or writing comments.
Try to put some timeouts with `$bot->wait($seconds)` calls, so you will behave like a real person and not a bot,
creating hundreds of pins in a minute.

## Examples
Here you can find articles with some examples of the most common tasks that can be done with the bot:

 - [Automate pinning](http://seregazhuk.github.io/2017/03/25/build-pinterest-bot-with-php-auto-pin/) ([source code](examples/auto_pins.php))
 - [Multiple Accounts and Proxy](http://seregazhuk.github.io/2017/03/28/build-printerest-bot-with-php-multiple-accounts/) ([source code](examples/multiple_accounts_and_proxy.php))
 - [Comments, Likes And Repins](http://seregazhuk.github.io/2017/03/30/build-pinterest-bot-with-php-comments-and-repins/) ([source code](examples/comments_likes_repins.php))
 - [Followers](http://seregazhuk.github.io/2017/04/01/build-pinterest-bit-with-php-followers/) ([source code](examples/followers.php))
 - [Parsing Pins](http://seregazhuk.github.io/2017/04/04/build-pinterest-bot-with-php-parsing-pins/) ([source code](examples/pins_parser.php))

## Account

### Login

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

To logout use *logout* method:

```php
$bot->auth->logout();
```

### Registration

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

After registration you will receive a confirmation email. You can pass a link from this email to `confirmEmail`
method:

```php
$bot->auth->confirmEmail($linkFromEmail);
```

Convert your account to a business one. Requires log in. The last parameter with website url is *optional*:

```php
$bot->auth->convertToBusiness('businessName');

$bot->auth->convertToBusiness('businessName', 'http://yoursite.com');
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

### Profile
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

You can change your profile avatar by using `setImage()` method and a path to your image:

```php
use seregazhuk\PinterestBot\Api\Forms\Profile

$profileForm = (new Profile())->setImage($pathToFile);
$bot->user->profile($profileForm);
```

You can get your current profile settings calling *profile* method without any params:

```php
$profile = $bot->user->profile();
echo $profile['username']; // Prints your username
```

In result you can find your username, and all your account settings.

Get your current username:

```php
$username = $bot->user->username();
```

Get your current user id:
```php
$userId = $bot->user->id();
```

Check if your account is banned:
```php
if ($bot->user->isBanned() {
    // You have ban
}
```

Change you password:
```php
$bot->password->change('oldPassword', 'newPassword');
```

Remove things you’ve recently searched for from search suggestions:
```php
$bot->user->clearSearchHistory();
```

Deactivate current account:
```php
$bot->user->deactivate();
```

Get sessions history:

```php
$history = $bot->user->sessionsHistory();
```

### Invitation

To invite someone by email:

```php
$bot->user->invite($email);
```

## Boards

Get all user's boards:
```php
$boards = $bot->boards->forUser($username);
```

Get all current logged-in user's boards.
```php
$boards = $bot->boards->forMe();
```

Get full board info by boardName and userName. Here you can get board id, for further functions
(for example, pin creating or following boards):

```php
$info = $bot->boards->info($username, $board);
```

Create a new board:

```php
// Create a public board
$bot->boards->create('Name', 'Description');

// Create a private board
$bot->boards->createPrivate('Name', 'Description');
```

Update a board by id:
```php
$bot->boards->update($boardId, ['name' => 'New title', 'description' => 'New description']);
```

You can pass more options in update: 'privacy' - is *public* by default and 'category' - is *other* by default:
```php
$bot->boards->update($boardId, [
    'name'        => 'New title',
    'description' => 'New description',
    'privacy'     => 'secret',
    'category'    => 'sports',
]);
```

Delete a board by id:
```php
$bot->boards->delete($boardId);
```

Follow/unfollow board by id:
```php
$bot->boards->follow($boardId);
$bot->boards->unfollow($boardId);
```

Get all pins for board by id (returns [Pagination](#pagination) object):
```php
foreach ($bot->boards->pins($boardId) as $pin) {
    // ...
}
```

Get board followers. Uses pinterest api pagination (returns [Pagination](#pagination) object):
```php
foreach($bot->boards->followers($boardId) as $follower) {
	// ...
}
```

When you repin, Pinterest suggests you some board titles for it. You can get these
suggestions for pin by its id:
```
$suggestions = $bot->boards->titleSuggestionsFor($pinId);
```

Send board with message or by email:
```php
// Send board with message
$bot->boards->sendWithMessage($boardId, 'Message', $userId); // To a user
$bot->boards->sendWithMessage($boardId, 'Message', [$userId1, $userId2]); // To many yusers

// Send board by email
$bot->boards->sendWithEmail($boardId, 'Message', 'friend@example.com'); // One email
$bot->boards->sendWithEmail($boardId, 'Message', ['friend1@example.com', 'friend2@example.com']); // many
```

### Invites

Get your boards invites:
```php
$invites = $bot->boards->invites();
```

Invite someone to your board:
```php
// to a user by email
$bot->boards->sendInvite($boardId, 'someone@example.com');

// to a user by user id
$bot->boards->sendInvite($boardId, $userId);

// to users by email
$bot->boards->sendInvite($boardId, ['someone@example.com', 'somefriend@example.com']);
// to users by user id
$bot->boards->sendInvite($boardId, [$user1Id, $user2Id]);
```

Accept an invite to a board:
```php
$bot->boards->acceptInvite($boardId);
```

Ignore an invite to a board:
```php
$bot->boards->ignoreInvite($boardId);
```

Delete invite. Removes from the board collaborators, requires an id of the user, you want to remove from the board:
```php
$bot->boards->deleteInvite($boardId, $userId);
// also you can ban a user specifying third argument as true
$bot->boards->deleteInvite($boardId, $userId, true);
```

## Pins

Notice! Try not to be very aggressive when pinning or commenting pins, or Pinterest will gonna ban you.

Get pin info by its id:
```php
$info = $bot->pins->info(1234567890);
```

Create new pin. Accepts image url, board id, where to post image, description and preview url:

```php
$pinInfo = $bot->pins->create('http://exmaple.com/image.jpg', $boardId, 'Pin description');
print_r($pinfInfo['id']);
```

You can pass a path to your local image. It will be uploaded to Pinterest:

```php
$pinInfo = $bot->pins->create('image.jpg', $boardId, 'Pin description');
```
    
You can specify a link for pin (source) as fourth argument. If not set, link is equal to image url:
```php
$pinInfo = $bot->pins->create(
    'http://exmaple.com/image.jpg', 
    $boardId, 
    'Pin description',
    'http://site.com',
);
```
    
Repin a pin by its id. You need a pin id and a board id where you want to put this pin. The third parameter
is a pin description and it is optional.

```php
$pinInfo = $bot->pins->repin($pinId, $boardId, 'my repin');
``` 

Edit pin by id. You can change pin's description, link or board:

```php
// Change description and link
$bot->pins->edit($pinId, 'new description', 'new link');

// Change board
$bot->pins->edit($pinId, 'new description', 'new link', $newBoardId);
``` 

Move pin to a new board:
```php
// Change board
$bot->pins->moveToBoard($pinId, $newBoardId);
```

Delete pin by id:
```php
$bot->pins->delete($pinId);
```   

Like/dislike pin by id:
```php
$bot->pins->like($pinId);
$bot->pins->unLike($pinId);
```

Copy/move pins to board. To copy/move one pin, pass it's id as the first argument. Pass an array of ids
to copy/move many pins:
```php
$bot->pins->copy($pinId, $boardId);
$bot->pins->move($pinId, $boardId);
```

Save image from pin to the disk. Saves original image of the pin to the specified path:
```php
$imagePath = $bot->pins->saveOriginalImage($pinId, $pathForPics);
```

Delete pins from board. To delete one pin, pass it's id as the first argument. Pass an array of ids 
to delete many pins:
```php
$bot->pins->deleteFromBoard($pinId, $boardId);
```

Write a comment:
```php
$result = $bot->comments->create($pinId, 'your comment'); 
// Result contains info about written comment. For example,
// comment_id if you want to delete it.
```

Delete a comment:
```php
$bot->comments->delete($pinId, $commentId);
```

Get pins from a specific url. For example: https://pinterest.com/source/flickr.com/ will return 
recent pins from flickr.com (returns [Pagination](#pagination) object):
```php
foreach ($bot->pins->fromSource('flickr.com') as $pin) {
    // ...
}
```

Get user pins feed (returns [Pagination](#pagination) object):
```php
foreach ($bot->pins->feed() as $pin) {
    // ...
}

// Only first 20 pins from feed
foreach ($bot->pins->feed(20) as $pin) {
    // ...
}
```

Get activity of a pin (returns [Pagination](#pagination) object):

```php
foreach ($bot->pins->activity($pinId) as $data) {
    // ...
}
```

If you don't want to get all activity records, you can pass a limit as the second parameter.
Get 5 last activity records:

```php
$activities = $bot->pins->activity($pinId, 5);
// print_r($activities->toArray());

foreach ($activities as $activity) {
    // ...
}
```

Get related pins for current pin (returns [Pagination](#pagination) object):
```php
foreach ($bot->pins->related($pinId) as $pin) {
	// ...
}
```

Get last 10 related pins for current pin:
```php
$relatedPins = $bot->pins->related($pinId, 10);

// print_r($relatedPins->toArray());

foreach ($relatedPins as $pin) {
    // ...
}
```

Get trending pins for a specific topic from http://pinterest.com/discover page. Uses topic id, that can be received
from `$bot->topics->explore()` method (returns [Pagination](#pagination) object):

```php

$trendingTopics = $bot->topics->explore();
$firstTopicId = $trendingTopics[0]['id'];

$pins = $bot->pins->explore($firstTopicId)->toArray();
```

Get visual similar pins:
```php
$result = $bot->pins->visualSimilar($pinId);
```

Send pin with message or by email:
```php
// Send pin with message
$bot->pins->sendWithMessage($pinId, 'message', $userId); // To a user
$bot->pins->sendWithMessage($pinId, 'message', [$userId1, $userId2]); // To many users

// Send pin by email
$bot->pins->sendWithEmail($pinId, 'message', 'friend@example.com'); // One email
$bot->pins->sendWithEmail($pinId, 'message', ['friend1@example.com', 'friend2@example.com']); // Many
```

Get your pin analytics, like numbers of clicks, views and repins (only for business account);
```php
$analytics = $bot->pins->analytics($pinId);
```

### TryIt

Get the pinners who have tied this pin (returns [Pagination](#pagination) object):
```php
$pinners = $bot->pins->tried($pinId);
// print_r($pinners->toArray());

foreach ($pinners as $pinner) {
    // ...
}
```

Try a pin. The third parameter with path to image file is optional. Returns an array with data of the created record:
```php
$tryRecord = $bot->pins->tryIt($pinId, 'comment', 'pathToImage');
```

Delete your try. You can use an `id` field from data received when you created a tryIt record:
```php
$tryRecord = $bot->pins->tryIt($pinId, 'comment', 'pathToImage');

// ...

$bot->pins->deleteTryIt($tryRecord['id']);
```

Edit your try. You can use an `id` field from data received when you created a tryIt record. You also need a pin id for
your try:

```php
$tryRecord = $bot->pins->tryIt($pinId, 'comment', 'pathToImage');

// ...

$bot->pins->editTryIt($tryRecord['pin']['id'], $tryRecord['id'], 'new comment', 'optionalPathToImage');
```

## Pinners

Follow/unfollow user. You can use both id or username.
**Notice:** When using username, bot will make one additional request to resolve user'id for his name:

```php
$bot->pinners->follow($userId);
$bot->pinners->unfollow($userId);

$bot->pinners->follow($username);
$bot->pinners->unfollow($username);
```

Get user info by username:

```php
$userData = $bot->pinners->info($username);
```

Get user following info. By default returns following users. Returns [Pagination](#pagination) object:
```php
foreach ($bot->pinners->following('username') as $following) {
    // ...
}
```

You can specify type of entities to be returned: *people*, *interests* or *boards*. For example:
```php
foreach ($bot->pinners->following('username', 'people') as $user) {
    // Loop through people
}

foreach($bot->pinners->following('username', 'boards') as $board) {
    // Loop through boards
}

foreach($bot->pinners->following('username', 'interests') as $interest) {
    // Loop through interests
}
```

Also you can use special methods-helpers to achieve the same results:

```php
foreach ($bot->pinners->followingPeople('username') as $user) {
    // Loop through people
}

foreach ($bot->pinners->followingBoards('username') as $board) {
    // Loop through boards
}

foreach($bot->pinners->followingInterests('username') as $interest) {
    // Loop through interests
}
```

Get user followers (returns [Pagination](#pagination) object). Accepts optional parameter `username`,
whose subscribers need to receive.

```php
foreach ($bot->pinners->followers('username') as $follower) {
    // ...
}
```

Without arguments returns current users' followers:
```php
// returns my followers
foreach($bot->pinners->followers() as $follower)
{
	// ...
}
```

Get the newest pins of a pinner (returns [Pagination](#pagination) object):

```php
foreach ($bot->pinners->pins('username') as $pin) {
    // ...
}
```

Get the last 20 pins of a pinner:

```php
foreach ($bot->pinners->pins('username', 20) as $pin) {
    // ...
}
```

Get pins that user likes (returns [Pagination](#pagination) object):

```php
foreach ($bot->pinners->likes('username') as $like) {
    // ...
}
```

Block a user:

```php
// By name
$bot->pinners->block('username');

// By id. For example, after calling info() method
$pinnerInfo = $bot->pinners->info('username');
$bot->pinners->block($pinnerInfo['id']);
```

## Interests

Get a list of main categories. Required bot to be logged in:

```php
$categories = $bot->interests->main();
```

Get category info by name (can be taken from *main()*):

```php
$info = $bot->interests->info("gifts");
```

Get related topics for interest:

```php
$topics = $bot->interests->getRelatedTopics('videos');
```

Get pins for specific interest (returns [Pagination](#pagination) object):

```php
foreach ($bot->interests->pins('videos') as $pin) {
    // ...
}
```

## Topics

Each interest has a list of related topics.

Follow/unfollow a topic by name:

```php
$bot->topics->follow('content-marketing');
$bot->topics->unFollow('content-marketing');
```

Get a topic info:

```php
$info = $bot->topics->info('content-marketing');
```

Get pins for a specific topic (returns [Pagination](#pagination) object):

```php
foreach ($bot->topics->pins('content-marketing') as $pin) {
    // ...
}
```

Get related topics for topic (similar as related topics for interest):

```php
$topics = $bot->topics->getRelatedTopics('content-marketing');
```

Get trending topics from http://pinterest.com/discover page. Then you can use an id of each topic
to get trending pins for this topic with `$bot->pins->explore()` method:

```php

$trendingTopics = $bot->topics->explore();
$firstTopicId = $trendingTopics[0]['id'];

$pins = $bot->pins->explore($firstTopicId)->toArray();
```

## Search

Search functions use Pinterest pagination in fetching results and return [Pagination](#pagination) object:

```php
$pins = $bot->pins->search('query')->toArray();
print_r($pins);

// Or iterate with requests
foreach ($bot->pins->search('query') as $pin) {
    // ...
}

// Search only in my pins
$pins = $bot->pins->searchInMyPins('query')->toArray();

// Search in people
foreach($bot->pinners->search('query') as $pinner) {
    // ...
}

// Search in boards
foreach($bot->boards->search('query') as $board) {
    // ...
}
```

## Inbox

### News

Get your current user's news (returns [Pagination](#pagination) object):
```php
// Get result as array
$news = $bot->inbox->news()->toArray();

// Iterate with requests
foreach ($bot->inbox->news() as $new) {
    // ...
}
```

### Notifications

Get user's notifications (returns [Pagination](#pagination) object):
```php
// Get result as array
$notifications = $bot->inbox->notifications()->toArray();

// Iterate with requests
foreach ($bot->inbox->notifications() as $notification) {
    // ...
}
```

### Conversations

Get array of last conversations:
```php
$conversations = $bot->inbox->conversations();
print_r($conversations);
```

### Write a message
Write a message to a user by id. You may specify one user by id, or pass an array of user ids:

```php
$bot->inbox->sendMessage($userId, 'message text');
```

Attach pin by id to message:
```php
$pinId = 123456789;
$bot->inbox->sendMessage($userId, 'message text', $pinId);
```

### Send email
Email param may be string or array of emails:
```php
$bot->inbox->sendEmail('mail@domain.com', 'message text');
```

Attach pin to email:
```php
$bot->inbox->sendEmail('mail@domain.com', 'message text', $pindId);
```

### Contact requests
When someone at first sends you an invitation to a board, you receive a contact request.
Get a list of contact requests:

```php
$requests = $bot->inbox->contactRequests();
```

To accept or to ignore a request you need to specify a request ID. This ID can be received from the array
returned in `$bot->inbox->contactRequests()` method.

Accept a request:
```php
$bot->inbox->acceptContactRequest($requestId);
```

Ignore a request:
```php
$bot->inbox->ignoreContactRequest($requestId);
```

## Keywords
Get recommended keywords for the query:

```php
$keywords = $bot->keywords->recommendedFor('dress');
print_r($keywords);

/*
Array
(
    [0] => Array
        (
            [term] => for teens
            [position] => 1
            [display] => For Teens
        )

    [1] => Array
        (
            [term] => wedding
            [position] => 0
            [display] => Wedding
        )
	// ...
)
*/
```

"position" determine the order to create the complete word. For example:
 - "for teens", position = 1 -> the complete keyword is : "dress for teens"
 - "wedding", position = 0 -> the complete keywords is: "wedding dress"
 
So, position = 0 means the additional keyword should be put before the search keyword 
when making concatenation, and position = 1 is for the reverse case.

## Errors handling
You can check for occurred errors after requests with method *getLastError()*. It returns
string that contains error from you last request to API:
 
```php
$error = $bot->getLastError();
echo $error;
```

## Use proxy

To set up proxy settings use *useProxy* method:
```php
$bot->getHttpClient()->useProxy('192.168.1.1', '12345');
```

By default it uses *http* proxy without authentication. If your 
proxy requires authentication, pass auth string as the third parameter:

```php
$bot->getHttpClient()->useProxy('192.168.1.1', '12345', 'username:password');
```

Use *socks* proxy:

```php
$bot->getHttpClient()->useSocksProxy('192.168.1.1', '12345');

// With authentication
$bot->getHttpClient()->useSocksProxy('192.168.1.1', '12345', 'username:password');
```

If you need to stop sending requests via proxy:
```php
$bot->getHttpClient()->dontUseProxy();
```

Check if bot uses proxy:
```php
if($bot->getHttpClient()->usesProxy()) {
    // ...
}
```

## Custom request settings

It is possible to add some additional Curl options for bot requests. For example, you can
set proxy and User Agent like this:

```php
$bot->getHttpClient()->setOptions([
    CURLOPT_PROXY => 'xx.xx.xxx.xx:xxxx',
    CURLOPT_PROXYTYPE => CURLPROXY_HTTP // Or CURLPROXY_SOCKS5,
    CURLOPT_USERAGENT => 'Your_User_Agent',
]);
```

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

You can get an url of the last visited page:
```php
$url = $bot->getHttpClient()->getCurrentUrl();
```

## Cookies

Current bot cookies are available through getHttpClient and cookie/cookies methods.
All cookies:

```php
$cookies = $bot->getHttpClient()->cookies();
```

Cookie value by name:
```php
$someCookieValue = $bot->getHttpClient()->cookie('cookieName');
```

By default cookie files are stored in your system temp directory. You can set custom path to store cookies. 
**Notice!** This path must have write permissions:

```php
$bot->getHttpClient()->setCookiesPath($yourCustomPathForCookies);

$currentPath = $bot->getHttpClient()->getCookiesPath();
```

Remove your cookies:
```php
$bot->getHttpClient()->removeCookies();
```

Visit (click) a link. For example, when Pinterest sends you email with some link, and you want bot to visit it:
```
$bot->user->visitPage($url);
```

## Pagination

Most of methods use Pinterest pagination. For example, when you run `$bot->pins->search('query')`, Pinterest returns
only 20 results for request, you cannot get all the pins at once with only one request. So these methods return
`Pagination` object. You can iterate over it to get results:

```php
$pagination = $bot->pins->search('query');

foreach ($pagination as $pin) {
    // ...
}
 ```

Or you can grab all results at once as an array, but it will require some time, to loop through all Pinterest pages to get these results:
```php
$pagination = $bot->pins->search('query');

$results = $pagination->toArray();
// Or
$results = $bot->pins->search('query')->toArray();
```

By default methods return the first 50 results. For example, `$bot->pins->search('query')` will return only first 50
pins. But you can specify another limit num as a second argument. Or pass 0
for no limit. For example,
```php
foreach ($bot->pins->search('query', 20) as $pin) {
    // ...
}
```

Will return only 20 pins of the search results.

Limit and offset in results:
```php
// Skip first 50 results
$results = $bot->pins
    ->search('query')
    ->skip(50)
    ->get();

// Skip first 50 results, and then take 20
$results = $bot->pins
    ->search('query')
    ->take(20)
    ->skip(50)
    ->get();
```

To get all results pass `0` in `take()` method.

## How can I thank you?
Why not star the GitHub repo? I'd love the attention!
And you can donate project on PayPal.

[![Support me with some coffee](https://img.shields.io/badge/donate-paypal-orange.svg)](https://www.paypal.me/seregazhuk)

Thanks! 
