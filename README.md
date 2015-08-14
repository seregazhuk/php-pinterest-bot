# Pinterest API for PHP
This PHP class will help you to work with your Pinterest account like
api calls from your script. It's a wrapper around the undocumented Pinterest
API calss.

## Dependencies

This API uses the CURL module and PHP 5.3 or above.


## Quick Start

	use Pinterest\ApiRequest;
	use Pinterest\PinterestBot;
	
	$api = new ApiRequest();
	$bot = new PinterestBot('mypinterestlogin', 'mypinterestpassword', $api);
	$bot->login();

Next, get your list of boards:

    $boards = $bot->getBoards();

## Pins

Create new pin. Accepts image url, board id, where to post image, description and preview url.
 
    $pinId = $bot->pin('http://exmaple.com/image.jpg', $boards[0]['id'], 'pin description');
    
Repin other pin by its id.

    $bot->repin($pinId, $boards[0]['id'], 'my repin');
    
Delete pin by id.
 
    $bot->deletePin($pinId);
   
Like/dislike pin by id.

	$bot->likePin($pinId);
	$bot->unLikePin($pinId);

Write a comment.

	$bot->commentPin($pinId, 'your comment');

## Pinners

Get your account name

	$bot->getAccountName();
	
Follow/unfollow user by ID

	$bot->followUser($userId);
	$bot->unFollowUser($userId);
	
Get user info by username

	$userData = $bot->getUserInfo($username);
	
Search queries coming soon.
Questions?  Email me:  seregazhuk88@gmail.com