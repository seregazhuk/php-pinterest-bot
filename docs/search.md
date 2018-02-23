# Search

- [Pins](#search-in-pins)
- [Pinners](#search-in-pinners)
- [Boards](#search-in-boards)
- [Search suggestions](#search-suggestions)
- [Tags suggestions](#tags-suggestions)

Search functions use Pinterest pagination in fetching results and return [Pagination](#pagination) object.

## Search in pins
The results may differ when you search for pins being logged in and when not. Under the hood Pinterest personalizes
search results if you are logged in. So keep this in mind.

```php
$pins = $bot->pins->search('query')->toArray();
print_r($pins);

// Or iterate with requests
foreach ($bot->pins->search('query') as $pin) {
    // ...
}

// Search only in my pins
$pins = $bot->pins->searchInMyPins('query')->toArray();
```

## Search in pinners

Pinterest allows to search for pinners only if you are logged in. If not, the bot throws `AuthRequired` exception.


```php
// Search in people
foreach($bot->pinners->search('query') as $pinner) {
    // ...
}
```

## Search in boards

```php
// Search in boards
foreach($bot->boards->search('query') as $board) {
    // ...
}
```

## Search suggestions

You can get type-ahead suggestions for you search query (returns pins, pinners and boards suggestions):
 
```php
$suggestions = $bot->suggestions->searchFor('cats');
```

## Tags suggestions

You can get type-ahead suggestions for tags (returns tags and pins counts for these tags):
 
```php
$suggestions = $bot->suggestions->tagsFor('cats');
```

There is no need to prepend your query with `#` symbol. 
