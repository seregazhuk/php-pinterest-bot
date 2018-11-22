# Pins

- [Pin info](#pin-info)
- [Create a pin](#create-a-pin)
- [Repin](#repin)
- [Edit pin](#edit-pin)
- [Move pin to a new board](#move-pin-to-a-new-board)
- [Delete pin](#delete-pin)
- [Copy/move pin](#copymove-pin)
- [Save image on disk](#save-image-on-disk)
- [Delete pin from board](#delete-pin-from-board)
- [Get list of comments](#get-list-of-comments)
- [Write comment](#add-comment-to-pin)
- [Delete comment from pin](#delete-comment-from-pin)
- [Pins for source](#get-pins-for-source)
- [User feed](#pins-feed)
- [Activity for pin](#activity)
- [Related pins](#related-pins)
- [Trending pins](#trending-pins)
- [Visual similar pins](#visual-similar-pins)
- [Send via message/email](#send-pin-via-message/email)
- [Pin analytics](#pin-analytics)
- [Share pin](#share-pin)
- [Leave reaction to pin](#leave-reaction-to-pin)
- [Try pin](#try-pin)

Notice! Try not to be very aggressive when pinning or commenting pins, or Pinterest will gonna ban you.

## Pin info

Get pin info by its id:
```php
$info = $bot->pins->info(1234567890);
```

## Create a pin

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

If you have [Rich Pins](https://business.pinterest.com/en/rich-pins) enabled,
you can specify a title of the pin:

```php
$pinInfo = $bot->pins->create(
    'http://exmaple.com/image.jpg',
    $boardId,
    'Pin description',
    'http://site.com',
    'Pin title'
);
```

You can specify a board section id as the last argument:

```php
$pinInfo = $bot->pins->create(
    'http://exmaple.com/image.jpg',
    $boardId,
    'Pin description',
    'http://site.com',
    'Pin title'
    $sectionId,
);


    
## Repin
 
Repin a pin by its id. You need a pin id and a board id where you want to put this pin. The third parameter
is a pin description and it is optional.

```php
$pinInfo = $bot->pins->repin($pinId, $boardId, 'my repin');
``` 

## Edit pin

Edit pin by id. You can change pin's description, link or board:

```php
// Change description and link
$bot->pins->edit($pinId, 'new description', 'new link');

// Change board
$bot->pins->edit($pinId, 'new description', 'new link', $newBoardId);

// Change section
$bot->pins->edit($pinId, 'new description', 'new link', $newBoardId, $newSectionId);
``` 

If you have [Rich Pins](https://business.pinterest.com/en/rich-pins) enabled,
you can change a title of the pin:

```
$bot->pins->edit($pinId, 'new description', 'new link', $newBoardId, 'new
title');
```

## Move pin to a new board

Move pin to a new board:
```php
// Change board
$bot->pins->moveToBoard($pinId, $newBoardId);
```
## Delete pin

Delete pin by id:
```php
$bot->pins->delete($pinId);
```   

## Copy/move pin

Copy/move pins to board. To copy/move one pin, pass it's id as the first argument. Pass an array of ids
to copy/move many pins:
```php
$bot->pins->copy($pinId, $boardId);
$bot->pins->move($pinId, $boardId);
```

## Save image on disk

Save image from pin to the disk. Saves original image of the pin to the specified path:
```php
$imagePath = $bot->pins->saveOriginalImage($pinId, $pathForPics);
```

## Delete pin from board

Delete pins from board. To delete one pin, pass it's id as the first argument. Pass an array of ids 
to delete many pins:
```php
$bot->pins->deleteFromBoard($pinId, $boardId);
```

## Get list of comments
Get list of comments for a specified pin (returns [Pagination](#pagination) object):

```php
$comments = $bot->comments->getList($pinId);
foreach($comments as $comment) {
    // ...
}

// retrieve all comments
$commnets = $bot->comments->getList($pinId)->toArray();
```

## Add comment to pin

Write a comment:
```php
$result = $bot->comments->create($pinId, 'your comment'); 
// Result contains info about written comment. For example,
// comment_id if you want to delete it.
```

## Delete comment from pin

Delete a comment:
```php
$bot->comments->delete($pinId, $commentId);
```

## Get pins for source

Get pins from a specific url. For example: https://pinterest.com/source/flickr.com/ will return 
recent pins from flickr.com (returns [Pagination](#pagination) object):
```php
foreach ($bot->pins->fromSource('flickr.com') as $pin) {
    // ...
}
```

## Pins feed

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

## Activity 

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

## Related pins

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

## Trending pins

Get trending pins for a specific topic from http://pinterest.com/discover page. Uses topic id, that can be received
from `$bot->topics->explore()` method (returns [Pagination](#pagination) object):

```php

$trendingTopics = $bot->topics->explore();
$firstTopicId = $trendingTopics[0]['id'];

$pins = $bot->pins->explore($firstTopicId)->toArray();
```

## Visual similar pins

Get visual similar pins (returns [Pagination](#pagination) object):
```php
$result = $bot->pins->visualSimilar($pinId)->toArray();

foreach($bot->pins->visualSimilar($pinId) as $similarData) {
    // ...
}
```

## Send pin via message/email

Send pin with message or by email:
```php
// Send pin with message
$bot->pins->sendWithMessage($pinId, 'message', $userId); // To a user
$bot->pins->sendWithMessage($pinId, 'message', [$userId1, $userId2]); // To many users

// Send pin by email
$bot->pins->sendWithEmail($pinId, 'message', 'friend@example.com'); // One email
$bot->pins->sendWithEmail($pinId, 'message', ['friend1@example.com', 'friend2@example.com']); // Many
```

## Pin analytics

Get your pin analytics, like numbers of clicks, views and repins (only for business account);
```php
$analytics = $bot->pins->analytics($pinId);
```

## Share pin

Share a link with a pin where a user can leave his or her reaction on this pin:
```php
$link = $bot->pins->share($pinId);
```

## Leave reaction to pin

Leave a reaction when you were given a sharing link with a pin. 
The link looks like this: `http://pin.it/cTwZfG_`. When you open it in your browser Pinterest redirects you to 
the following url `https://www.pinterest.de/pin/332703491213209642/sent/?sfo=1&sender=731835145606177283&invite_code=6cb1d66946464b3f9d0084f623c7822b`.
To *react* on this link you need to know a pinId (number after `pin`) and a userId (number after `sender`), who sent you a link:
 
```php
// like
$bot->pins->leaveGoodReaction($pinId, $senderId);
// don't like
$bot->pins->leaveBadReaction($pinId, $senderId);
```

## Try pin

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
