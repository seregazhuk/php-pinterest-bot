# Keywords

Get recommended keywords for the query:

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
