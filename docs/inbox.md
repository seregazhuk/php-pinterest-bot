# Inbox

- [News](#news)
- [Notifications](#notifications)
- [Conversations](#conversations)
- [Write a message](#write-a-message)
- [Send email](#send-email)
- [Contact requests](#contact-requests)

## News

Get your current user's news (returns [Pagination](#pagination) object):
```php
// Get result as array
$news = $bot->inbox->news()->toArray();

// Iterate with requests
foreach ($bot->inbox->news() as $new) {
    // ...
}
```

## Notifications

### Get all

Get user's notifications (returns [Pagination](#pagination) object):

```php
// Get result as array
$notifications = $bot->inbox->notifications()->toArray();

// Iterate over notifications
foreach ($bot->inbox->notifications() as $notification) {
    // ...
}
```

### Details for notification

Each notification from the method above is represented by array. This array contains `id` field, which is an
id of the current notification (or consider as news id). This id can be used to retrieve details 
for this notification (returns [Pagination](#pagination) object):

```php
// Get details for notification by its id
$details = $bot->inbox->newsHubDetails($notificationId);

// Iterate over details
foreach ($bot->inbox->newsHubDetails($notificationId) as $detail) {
    // ...
}
```

## Conversations

Get a list of last conversations (returns [Pagination](#pagination) object):

```php
$conversations = $bot->inbox->conversations();

// Iterate over conversations
foreach($conversations as $conversation) {
    // ...
}
```

Get messages for a specified conversation (returns [Pagination](#pagination) object). 
Conversation id can be retrieved via `$bot->inbox->conversations()` method:

```php
$conversations = $bot->inbox->conversations()->toArray();

// Iterate over messages
foreach($bot->inbox->getMessages($conversations[0]['id']) as $message) {
    // ...
}
```

## Write a message
**Notice** that, when you are sending a message to unknown person Pinterest doesn't show your message. 
It suggests this person to create a contact with you. Only then you can send messages, 
see [Contact requests](#contact-requests)

Write a message to a user by id. You may specify one user by id, or pass an array of user ids:

```php
$bot->inbox->sendMessage($userId, 'message text');
```

Attach pin by id to message:
```php
$pinId = 123456789;
$bot->inbox->sendMessage($userId, 'message text', $pinId);
```

## Send email
Email param may be string or array of emails:
```php
$bot->inbox->sendEmail('mail@domain.com', 'message text');
```

Attach pin to email:
```php
$bot->inbox->sendEmail('mail@domain.com', 'message text', $pindId);
```

## Contact requests
When someone at first sends you an invitation to a board or a message, you receive a contact request.
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
