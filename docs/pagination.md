# Pagination

- [Description](#description)
- [As array](#as-array)
- [Limit](#limit)
- [Offset](#offset)

## Description

Most of methods use Pinterest pagination. For example, when you run `$bot->pins->search('query')`, Pinterest returns
only 20 results for request, you cannot get all the pins at once with only one request. So these methods return
`Pagination` object. You can iterate over it to get results:

```php
$pagination = $bot->pins->search('query');

foreach ($pagination as $pin) {
    // ...
}
 ```
 
## As array

Or you can grab all results at once as an array, but it will require some time, to loop through all Pinterest pages to get these results:
```php
$pagination = $bot->pins->search('query');

$results = $pagination->toArray();
// Or
$results = $bot->pins->search('query')->toArray();
```

## Limit

By default methods return the first 50 results. For example, `$bot->pins->search('query')` will return only first 50
pins. But you can specify another limit num as a second argument. Or pass 0
for no limit. For example,
```php
foreach ($bot->pins->search('query', 20) as $pin) {
    // ...
}
```

Will return only 20 pins of the search results.

## Offset

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
