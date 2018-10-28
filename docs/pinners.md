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

### People
Get people the user follows. Returns [Pagination](#pagination) object:
```php
foreach ($bot->pinners->followingPeople('username') as $user) {
    // Loop through people
}
```

Method behaves like https://pinterest.com/following page: includes recent pins for these pinners. 


### Boards
Get boards the user follows. Returns [Pagination](#pagination) object:
```php
foreach ($bot->pinners->followingBoards('username') as $user) {
    // Loop through boards
}
```

### Interests

Get interests the user follows. Returns [Pagination](#pagination) object:
```php
foreach ($bot->pinners->followingInterests('username') as $user) {
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

Check if you follow a certain user:

```php
if ($bot->pinners->isFollowedByMe('username)) {
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
