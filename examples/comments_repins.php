<?php

require __DIR__ . '/../vendor/autoload.php';

use seregazhuk\PinterestBot\Factories\PinterestBot;

$comments = ['Nice!', 'Cool!', 'Very beautiful!', 'Amazing!'];

$bot = PinterestBot::create();
$bot->auth->login('mypinterestlogin', 'mypinterestpassword');

$board = $bot->boards->info('my_username', 'Cats repins');

$pins = $bot->pins->search('cats')->toArray();

foreach ($pins as $pin) {
    // repin to our board
    $bot->pins->repin($pin['id'], $board['id']);
    // write a comment
    $comment = $comments[array_rand($comments)];
    $bot->comments->create($pin['id'], $comment);
}
