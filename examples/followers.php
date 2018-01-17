<?php

require __DIR__ . '/../vendor/autoload.php';

use seregazhuk\PinterestBot\Factories\PinterestBot;

$bot = PinterestBot::create();

$bot->auth->login('mypinterestlogin', 'mypinterestpassword');
$peopleToFollow = $bot->pinners->followers('myusername')->toArray();

foreach ($peopleToFollow as $user) {
    $bot->pinners->follow($user['id']);
}
