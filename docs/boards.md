# Boards

- [User boards](#user-boards)
- [Current user boards](#current-user-boards)
- [Board info](#board-info)
- [Create](#create)
- [Update](#update)
- [Delete](#delete)
- [Follow/unfollow](#followunfollow)
- [Board pins](#board-pins)
- [Board followers](#board-followers)
- [Title suggestions](#title-suggestions)
- [Send via message/email](#send-via-message/email)
- [Board sections](#board-sections)
    - [Get list for board](#get-list-for-board)
    - [Create section](#create-section)
    - [Update section](#update-section)
    - [Delete section](#delete-section)
- [Invites](#invites)
    - [Get active invites](#get-active-invites)
    - [Get invites for a board](#get-invites-for-a-board)
    - [Invite someone to board](#invite-someone-to-board)
    - [Accept invite](#accept-invite)
    - [Ignore invite](#ignore-invite)
    - [Delete invite](#delete-invite)
    - [Leave board](#leave-board)
    
## User boards

Get all user's boards:
```php
$boards = $bot->boards->forUser($username);
```

## Current user boards

Get all current logged-in user's boards.
```php
$boards = $bot->boards->forMe();
```

## Board info

Get full board info by boardName and userName. Here you can get board id, for further functions
(for example, pin creating or following boards):

```php
$info = $bot->boards->info($username, $board);
```

## Create

Create a new board:

```php
// Create a public board
$bot->boards->create('Name', 'Description');

// Create a private board
$bot->boards->createPrivate('Name', 'Description');
```

## Update

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

## Delete

Delete a board by id:
```php
$bot->boards->delete($boardId);
```

## Follow/unfollow

Follow/unfollow board by id:
```php
$bot->boards->follow($boardId);
$bot->boards->unfollow($boardId);
```

## Board pins

Get all pins for board by id (returns [Pagination](#pagination) object):
```php
foreach ($bot->boards->pins($boardId) as $pin) {
    // ...
}
```

## Board followers

Get board followers. Uses pinterest api pagination (returns [Pagination](#pagination) object):
```php
foreach($bot->boards->followers($boardId) as $follower) {
	// ...
}
```

## Title suggestions

When you repin, Pinterest suggests you some board titles for it. You can get these
suggestions for pin by its id:
```
$suggestions = $bot->boards->titleSuggestionsFor($pinId);
```

## Send via message/email

Send board with message or by email:
```php
// Send board with message
$bot->boards->sendWithMessage($boardId, 'Message', $userId); // To a user
$bot->boards->sendWithMessage($boardId, 'Message', [$userId1, $userId2]); // To many yusers

// Send board by email
$bot->boards->sendWithEmail($boardId, 'Message', 'friend@example.com'); // One email
$bot->boards->sendWithEmail($boardId, 'Message', ['friend1@example.com', 'friend2@example.com']); // many
```

## Board Sections

### Get list for board

Every board can have several sections. Get a list of sections for a specified boardId. Returns an array of sections data.

```php
$sections = $bot->boardSections->forBoard($boardId);
```

### Create section

Create a section for a board:
```php
$bot->boardSections->create($boardId, 'Section name');
```

### Update section

Update a section's name. You need a section id, which can be retrieved by calling ` $bot->boardSections->forBoard($boardId)`:
```php
$bot->boardSections->update($sectionId, 'New section name');
```

### Delete section

Delete a section. You need a section id, which can be retrieved by calling ` $bot->boardSections->forBoard($boardId)`
```php
$bot->boardSections->delete($sectionId);
```

## Invites

### Get active invites

Get all your active boards invites:
```php
$invites = $bot->boards->invites();
```

### Get invites for a board

Get invites for a specified board, including accepted and declined invites (returns [Pagination](#pagination) object):
 
 ```php
foreach($bot->boards->invitesFor($boardId) as $invite) {
    // loop through invites
}
 ```

### Invite someone to board

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

### Accept invite

Accept an invite to a board:
```php
$bot->boards->acceptInvite($boardId);
```

### Ignore invite

Ignore an invite to a board:
```php
$bot->boards->ignoreInvite($boardId);
```

### Delete invite

Delete invite. Removes from the board collaborators, requires an id of the user, you want to remove from the board:
```php
$bot->boards->deleteInvite($boardId, $userId);
// also you can ban a user specifying third argument as true
$bot->boards->deleteInvite($boardId, $userId, true);
```

### Leave board

Leave a board you have been invited to:
```php
$bot->boards->leave($boardId);
```
