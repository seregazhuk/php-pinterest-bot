<?php

require __DIR__ . '/vendor/autoload.php';

use Pinterest\ApiRequest;
use Pinterest\PinterestBot;

$api = new ApiRequest();
$bot = new PinterestBot('seregazhuk88@gmail.com', 'Awesometest', $api);
$bot->login();
$res = $bot->unLikePin('562950022149304286');
var_dump($res);