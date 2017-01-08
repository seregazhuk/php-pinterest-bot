# Pinterest Bot for PHP

<p align="center">
    <img src="logo.jpg" alt="Pinterest PHP Bot">
</p>

<p align="center">
	<a href="https://travis-ci.org/seregazhuk/php-pinterest-bot"><img src="https://camo.githubusercontent.com/412fbc5888a7d03937daf554d662678477512375/68747470733a2f2f7472617669732d63692e6f72672f7365726567617a68756b2f7068702d70696e7465726573742d626f742e737667" alt="Build Status" data-canonical-src="https://travis-ci.org/seregazhuk/php-pinterest-bot.svg" style="max-width:100%;"></a>
	<a href="https://circleci.com/gh/seregazhuk/php-pinterest-bot"><img src="https://camo.githubusercontent.com/3577832f301d8615a2275d36aa3e274765ffb995/68747470733a2f2f636972636c6563692e636f6d2f67682f7365726567617a68756b2f7068702d70696e7465726573742d626f742e7376673f7374796c653d736869656c64" alt="Circle CI" data-canonical-src="https://circleci.com/gh/seregazhuk/php-pinterest-bot.svg?style=shield" style="max-width:100%;"></a>
	<a href="https://codeclimate.com/github/seregazhuk/php-pinterest-bot"><img src="https://camo.githubusercontent.com/d30f6b154177d3a589af46e154b31ec66ef05128/68747470733a2f2f636f6465636c696d6174652e636f6d2f6769746875622f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f6770612e737667" alt="Code Climate" data-canonical-src="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/gpa.svg" style="max-width:100%;"></a>
	<a href="https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/?branch=master"><img src="https://camo.githubusercontent.com/c05b7ed73a9c49224fb982888fc7ac643fbec5f6/68747470733a2f2f7363727574696e697a65722d63692e636f6d2f672f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f7175616c6974792d73636f72652e706e673f623d6d6173746572" alt="Scrutinizer Code Quality" data-canonical-src="https://scrutinizer-ci.com/g/seregazhuk/php-pinterest-bot/badges/quality-score.png?b=master" style="max-width:100%;"></a>
	<a href="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/coverage"><img src="https://camo.githubusercontent.com/704321dd17ccb2c5de0e954e1c29a8cefa2572be/68747470733a2f2f636f6465636c696d6174652e636f6d2f6769746875622f7365726567617a68756b2f7068702d70696e7465726573742d626f742f6261646765732f636f7665726167652e737667" alt="Test Coverage" data-canonical-src="https://codeclimate.com/github/seregazhuk/php-pinterest-bot/badges/coverage.svg" style="max-width:100%;"></a>
	<a href="https://packagist.org/packages/seregazhuk/pinterest-bot"><img class="spinned latest_stable_version_img" src="https://poser.pugx.org/seregazhuk/pinterest-bot/v/stable" style="display: inline;"></a>
	<a href="https://packagist.org/packages/seregazhuk/pinterest-bot"><img src="https://camo.githubusercontent.com/8957462146926452326740b5c6255bbdb3abea67/68747470733a2f2f706f7365722e707567782e6f72672f7365726567617a68756b2f70696e7465726573742d626f742f646f776e6c6f616473" alt="Total Downloads" data-canonical-src="https://poser.pugx.org/seregazhuk/pinterest-bot/downloads" style="max-width:100%;"></a>
</p>

This PHP library will help you to work with your Pinterest account without using any API account credentials. 

To have an access to Pinterest API you need to go to <a target="_blank" href="http://developers.pinterest.com">developers.pinterest.com</a> 
and register as a developer, then register your application, then wait for confirmation, and only then you 
will get an access token. With this library, you are ready to go. Just use only your 
account login and password, like you do it in your browser. But even your account is not required, 
if you don't use such operations as creating pins, writing comments or sending messages!

- [Dependencies](#dependencies)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Account](#account)
- [Boards](#boards)
- [Pins](#pins)
- [Pinners](#pinners)
- [Interests](#interests)
- [Topics](#topics)
- [Search](#search)
- [Inbox](#inbox)
- [Keywords](#keywords)
- [Errors handling](#errors-handling)
- [Use proxy](#use-proxy)
- [Custom request settings](#custom-request-settings)
- [Cookies](#cookies)

## Dependencies
Library requires CURL extension and PHP 5.5.9 or above.

## Installation
Via composer:
```
composer require seregazhuk/pinterest-bot
```

## Quick Start

```php 
// You may need to amend this path to locate composer's autoloader
require('vendor/autoload.php'); 

use seregazhuk\PinterestBot\Factories\PinterestBot;

$bot = PinterestBot::create();

// login
$bot->auth->login('mypinterestlogin', 'mypinterestpassword');

// get lists of your boards 
$boards = $bot->boards->forUser('yourUserName');

// create a pin
$bot->pins->create('http://exmaple.com/image.jpg', $boards[0]['id'], 'pin description');
```

*Note*: Some methods use pinterest navigation through results, for example, get user followers/following, pins
likes/dislikes, search and other feed queries. This means that for every batch of results there will be a 
request to Pinterest. These methods return an iterator object with Pinterest api results.
By default functions return the first 50 results. But you can specify another limit num as a second argument. Or pass 0
for no limit. For example, 
```php 
foreach($bot->pins->search('query', 20) as $pin) {
	// ...
}
```

will return only 2 pins of the search results.

To receive all results at once as array:
```php
// get all results as array
$results = $bot->pins->search('query', 20)->toArray();
```

Limit and offset in results:
```php
// skip first 50 results
$results = $bot->pins
    ->search('query')
    ->skip(50)
    ->get();

// skip first 50 results, and then take 20 
$results = $bot->pins
    ->search('query')
    ->take(20)
    ->skip(50)
    ->get();
```

To get all results pass `0` in `take()` method.

## Account

### Login

```php
$result = $bot->auth->login('mypinterestlogin', 'mypinterestpassword');
```
Login method returns `true` on success and `false` if fails:

```php
$result = $bot->auth->login('mypinterestlogin', 'mypinterestpassword');
if(!$result) {
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
if($bot->auth->isLoggedIn()) {
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

You can specify additional parameters with registration: age and country. By default they are: 18, "GB":

```php
$bot->auth->register('youremail@gmail.com', 'password', 'Name', "DE", 40);
```

Register a business account. The last parameter with website url is *optional*:

```php
$bot->auth->registerBusiness('youremail@gmail.com', 'password', 'BusinessName');

$bot->auth->registerBusiness('youremail@gmail.com', 'password', 'BusinessName', 'http://yoursite.com');
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
Change profile. Available settings are:
 - *last_name*, 
 - *first_name*, 
 - *username*, 
 - *about*,
 - *location*, 
 - *website_url*,
 - *country* (ISO2 code) 
 - *profile_image*:
```php
$bot->user->profile(['first_name'=>'My_name']);
```

You can change your profile avatar by setting *profile_image* key with a path to image:  
```php
$bot->user->profile([
	'first_name' => 'My_name',
	'profile_image' => $path_to_file
]);
```

You can get your current profile settings calling *profile* method without any params:
```php
$profile = $bot->user->profile();
echo $profile['username']; //prints your username
```
In result you can find your username, and all your account settings.

Get your current username:
```php
$username = $bot->user->username();
```

Check if your account is banned:
```php
if ($bot->user->isBanned() {
    // ... you have ban
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

### Invitation

To invite someone by email:

```php
$bot->user->invite($email);
```

## Boards

Get all user's boards.
```php
$boards = $bot->boards->forUser($username);
```

Get full board info by boardName and userName. Here you can get board id, for further functions
(for example, pin creating or following boards).

```php
$info = $bot->boards->info($username, $board);
```

Create a new board.

```php
// create a public board
$bot->boards->create('name', 'description');

// create a private board
$bot->boards->createPrivate('name', 'description');
```

Update a board by id.
```php
$bot->boards->update($boardId, ['name' => 'New title', 'description' => 'New description']);
```

You can pass more options in update: 'privacy' - is *public* by default and 'category' - is *other* by default.
```php
$bot->boards->update($boardId, [
    'name'        => 'New title',
    'description' => 'New description',
    'privacy'     => 'secret',
    'category'    => 'sports'
]);
```

Delete a board by id.
```php
$bot->boards->delete($boardId);
```

Follow/unfollow board by id.
```php
$bot->boards->follow($boardId);
$bot->boards->unfollow($boardId);
```

Get all pins for board by id (returns Generator object).
```php
foreach($bot->boards->pins($boardId) as $pin)
{
    // ...
}
```

Get board followers. Uses pinterest api pagination (return Generator object).
```php
foreach($bot->boards->followers($boardId) as $follower)
{
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
// send board with message
$bot->boards->sendWithMessage($boardId, 'message', $userId); // to a user
$bot->boards->sendWithMessage($boardId, 'message', [$userId1, $userId2]); // to many yusers

// send board by email
$bot->boards->sendWithEmail($boardId, 'message', 'friend@example.com'); // one email
$bot->boards->sendWithEmail($boardId, 'message', ['friend1@example.com', 'friend2@example.com']); // many
```

## Pins

Notice! Try not to be very aggressive when pinning or commenting pins, or Pinterest will gonna ban you.

Get pin info by its id.
```php
$info = $bot->pins->info(1234567890);
```

Create new pin. Accepts image url, board id, where to post image, description and preview url.

```php
$pinInfo = $bot->pins->create('http://exmaple.com/image.jpg', $boardId, 'pin description');
print_r($pinfInfo['id']);
```

You can pass a path to your local image. It will be uploaded to pinterest:

```php
$pinInfo = $bot->pins->create('image.jpg', $boardId, 'pin description');
```
    
You can specify a link for pin (source) as fourth argument. If not set, link is equal to image url.    
```php
$pinInfo = $bot->pins->create(
    'http://exmaple.com/image.jpg', 
    $boardId, 
    'pin description', 
    'http://site.com'
);
```
    
Repin a pin by its id.

```php
$pinInfo = $bot->pins->repin($pinId, $boardId, 'my repin');
``` 

Edit pin by id. You can change pin's description, link or board.

```php
// change description and link
$bot->pins->edit($pinId, 'new description', 'new link');
// change board
$bot->pins->edit($pinId, 'new description', 'new link', $newBoardId);
``` 

Move pin to a new board.
```php
// change board
$bot->pins->moveToBoard($pinId, $newBoardId);
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

Write a comment.
```php
$result = $bot->comments->create($pinId, 'your comment'); 
// Result contains info about written comment. For example,
// comment_id if you want to delete it.
```

Delete a comment.
```php
$bot->comments->delete($pinId, $commentId);
```

Get pins from a specific url. For example: https://pinterest.com/source/flickr.com/ will return 
recent pins from flickr.com:
```php
foreach ($bot->pins->fromSource('flickr.com') as $pin) {
    // ...
}
```

Get user pins feed. Method *feed()* returns Generator object.
```php
foreach ($bot->pins->feed() as $pin) {
    //...
}

// only first 20 pins from feed
foreach ($bot->pins->feed(20) as $pin) {
    //...
}
```

Get activity of a pin:

```php
foreach ($bot->pins->activity($pinId) as $data) {
    //...
}
```

If you don't want to get all activity records, you can pass a limit as the second parameter.
Get  5 last activity records:

```php
$activities = $bot->pins->activity($pinId, 5);
// print_r($activities->toArray());
foreach($activities as $activity) {
	//...
}
```

Get related pins for current pin:
```php
foreach($bot->pins->related($pinId) as $pin) {
	//...
}
```

Get last 10 related pins for current pin:
```php
$related = $bot->pins->related($pinId, 10);
// print_r($related->toArray());
foreach($related as $pin) {
	//...
}
```

Get the pinners who have tied this pin:
```php
$pinners = $bot->pins->tried($pinId);
// print_r($pinners->toArray()); 
foreach($pinners as $pinner) {
    // ...
}
```

Get visual similar pins:
```php
$result = $bot->pins->visualSimilar($pinId);
```

Send pin with message or by email:
```php
// send pin with message
$bot->pins->sendWithMessage($pinId, 'message', $userId); // to a user
$bot->pins->sendWithMessage($pinId, 'message', [$userId1, $userId2]); // to many users 
// send pin by email
$bot->pins->sendWithEmail($pinId, 'message', 'friend@example.com'); // one email
$bot->pins->sendWithEmail($pinId, 'message', ['friend1@example.com', 'friend2@example.com']); // many
```

## Pinners

Follow/unfollow user by id.

```php
$bot->pinners->follow($userId);
$bot->pinners->unfollow($userId);
```

Get user info by username.

```php
$userData = $bot->pinners->info($username);
```

Get user following info. By default returns following users. Uses pinterest api pagination.
```php
foreach($bot->pinners->following('username') as $following)
{
	// ...
}
```

You can specify type of entities to be returned: *people*, *interests* or *boards*. For example:
```php
foreach($bot->pinners->following('username', 'people') as $user)
{
	// loop through people
}

foreach($bot->pinners->following('username', 'boards') as $board)
{
	// loop through boards
}

foreach($bot->pinners->following('username', 'interests') as $interest)
{
	// loop through interests
}
```

Also you can use special methods-helpers to achieve the same results:

```php
foreach($bot->pinners->followingPeople('username') as $user)
{
	// loop through people
}

foreach($bot->pinners->followingBoards('username') as $board)
{
	// loop through boards
}

foreach($bot->pinners->followingInterests('username') as $interest)
{
	// loop through interests
}
```

Get user followers. Returns Generator object.
```php
foreach($bot->pinners->followers('username') as $follower)
{
	// ...
}
```

Get the newest pins of a pinner. Returns Generator object.

```php
foreach($bot->pinners->pins('username') as $pin)
{
    // ...
}
```

Get the last 20 pins of a pinner 

```php
foreach($bot->pinners->pins('username', 20) as $pin)
{
    // ...
}
```

Get pins that user likes. Returns Generator object.

```php
foreach($bot->pinners->likes('username') as $like)
{
    // ...
}
```

Block a user:

```php
// by name
$bot->pinners->block('username');

// by id. For example after calling info() method
$pinnerInfo = $bot->pinners->info('username');
$bot->pinners->block($pinnerInfo['id']);
```

## Interests

Get a list of main categories. Required bot to be logged in.

```php
$categories = $bot->interests->main();
```

Get category info by name (can be taken from *main()*).

```php
$info = $bot->interests->info("gifts");
```

Get related topics for interest:

```php
$topics = $bot->interests->getRelatedTopics('videos');
```

Get pins for specific interest:

```php
foreach($bot->interests->pins('videos') as $pin) {
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

Get pins for a specific topic:

```php
foreach($bot->topics->pins('content-marketing') as $pin) {
    // ...
}
```

Get related topics for topic (similar as related topics for interest):

```php
$topics = $bot->topics->getRelatedTopics('content-marketing');
```

## Search

**Notice!** generator object is not an array. If you want to fetch search results as an
array use PHP `iterator_to_array()` function:

```php
$results = iterator_to_array($bot->pins->search('query'));
```

Search functions use Pinterest pagination in fetching results and return a generator object.

```php
$pins = $bot->pins->search('query')->toArray();
print_r($pins);

// or iterate with requests
foreach($bot->pins->search('query') as $pin)
{
	// ...
}

// search only in my pins
$pins = $bot->pins->searchInMyPins('query')->toArray();


// search in people
foreach($bot->pinners->search('query') as $pinner)
{
	// ...
}

// search in boards
foreach($bot->boards->search('query') as $board);
{
	// ...
}
```

## Inbox

### News

Get your current user's news:
```php
// get result as array
$news = $bot->inbox->news()->asArray();

// iterate with requests
foreach($bot->inbox->news() as $new) {
    // ...
}
```

### Notifications

Get user's notifications:
```php
// get result as array
$notifications = $bot->inbox->notifications()->asArray();

// iterate with requests
foreach($bot->inbox->notifications() as $notification) {
    // ...
}
```

### Conversations

Get array of last conversations.
```php
$conversations = $bot->inbox->conversations();
print_r($conversations);
```

### Write a message
Write a message to a user by id. You may specify one user by id, or pass an array of user ids. 

```php
$bot->inbox->sendMessage($userId, 'message text');
```

Attach pin by id to message.
```php
$pinId = 123456789;
$bot->inbox->sendMessage($userId, 'message text', $pinId);
```

### Send email
Email param may be string or array of emails.
```php
$bot->inbox->sendEmail('mail@domain.com', 'message text');
```

Attach pin to email.
```php
$bot->inbox->sendEmail('mail@domain.com', 'message text', $pindId);
```

## Keywords
Get recommended keywords for the query.

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

// with authentication
$bot->getHttpClient()->useSocksProxy('192.168.1.1', '12345', 'username:password');
```

## Custom request settings

It is possible to add some additional Curl options for bot requests. For example you can 
set proxy and user-agent like this:

```php
$bot->getHttpClient()->setOptions([
    CURLOPT_PROXY => 'xx.xx.xxx.xx:xxxx',
    CURLOPT_PROXYTYPE => CURLPROXY_HTTP // or CURLPROXY_SOCKS5,
    CURLOPT_USERAGENT => 'Your_User_Agent',
]);
```

With every request Pinterest returns an array with your current client info, with such info as 
OS, browser, ip and others:

```php
$info = $bot->getClientInfo();
```

By default it uses client info from the last request. To reload client context pass `true` argument:

```php
// force to reload client info
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
**Notice!** This path should have write permissions:

```php
$bot->getHttpClient()->setCookiesPath($yourCustomPathForCookies);

$currentPath = $bot->getHttpClient()->getCookiesPath();
```

Remove your cookies:
```php
$bot->getHttpClient()->removeCookies();
```

## How can I thank you?
Why not star the GitHub repo? I'd love the attention!

Thanks! 
