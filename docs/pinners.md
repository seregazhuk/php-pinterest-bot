# Pinners

- [Follow/unfollow](#followunfollow-users)
- [User info](#user-info)
- [Following boards/pinners/interests](#following-boardspinnersinterests)
- [User followers](#user-followers)
- [User pins](#user-pins)
- [Liked pins](#liked-pins)
- [Block a user](#block-a-user)

## Follow/unfollow users
Follow/unfollow user. You can use both id or username.
**Notice:** When using username, bot will make one additional request to resolve user'id for his name:

```php
$bot->pinners->follow($userId);
$bot->pinners->unfollow($userId);
// or
$bot->pinners->follow($username);
$bot->pinners->unfollow($username);
```

## User info

Get user info by username:

```php
$userData = $bot->pinners->info($username);
```
## Following boards/pinners/interests

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

## User followers

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

## User pins

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

## Liked pins

Get pins that user likes (returns [Pagination](#pagination) object):

```php
foreach ($bot->pinners->likes('username') as $like) {
    // ...
}
```

## Block a user

Block a user:

```php
// By name
$bot->pinners->block('username');

// By id. For example, after calling info() method
$pinnerInfo = $bot->pinners->info('username');
$bot->pinners->block($pinnerInfo['id']);
```
