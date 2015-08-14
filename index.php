<?php

require __DIR__ . '/vendor/autoload.php';

use Pinterest\ApiRequest;
use Pinterest\PinterestBot;

$api = new ApiRequest();
$bot = new PinterestBot('seregazhuk88@gmail.com', 'Awesometest', $api);
$bot->login();

print_r($bot->getBoards());