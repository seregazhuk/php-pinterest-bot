# Interests and Topics

- [Interests](#interests)
    - [Main list](#main-interests-list)
    - [Category info](#category-info)
    - [Related topics](#related-topics)
    - [Interest pins](#interest-pins)
- [Topics](#topics)
    - [Follow/unfollow topic](#followunfollow-topic)
    - [Topic info](#topic-info)
    - [Topic pins](#topic-pins)
    - [Related topics for topic](#related-topics-for-topic)
    - [Trending topics](#trending-topics)

## Interests

### Main interests list

Get a list of main categories. Required bot to be logged in:

```php
$categories = $bot->interests->main();
```

### Category info

Get category info by name (can be taken from *main()*):

```php
$info = $bot->interests->info("gifts"); 
// gifts - can be any other string. Actualy it is a key field from one of the results returned by main() method.
```

### Related topics

Get related topics for interest:

```php
$topics = $bot->interests->getRelatedTopics('videos');
```

### Interest pins

Get pins for specific interest (returns [Pagination](#pagination) object):

```php
foreach ($bot->interests->pins('videos') as $pin) {
    // ...
}
```

## Topics

Each interest has a list of related topics.

### Follow/unfollow topic
Follow/unfollow a topic by name:

```php
$bot->topics->follow('content-marketing');
$bot->topics->unFollow('content-marketing');
```

### Topic info

Get a topic info:

```php
$info = $bot->topics->info('content-marketing');
```

### Topic pins

Get pins for a specific topic (returns [Pagination](#pagination) object):

```php
foreach ($bot->topics->pins('content-marketing') as $pin) {
    // ...
}
```

### Related topics for topic

Get related topics for topic (similar as related topics for interest):

```php
$topics = $bot->topics->getRelatedTopics('content-marketing');
```

### Trending topics

Get trending topics from http://pinterest.com/discover page. Then you can use an id of each topic
to get trending pins for this topic with `$bot->pins->explore()` method:

```php

$trendingTopics = $bot->topics->explore();
$firstTopicId = $trendingTopics[0]['id'];

$pins = $bot->pins->explore($firstTopicId)->toArray();
```
