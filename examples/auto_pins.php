<?php
require __DIR__ . '/../vendor/autoload.php';

use seregazhuk\PinterestBot\Factories\PinterestBot;

$blogUrl = 'http://awasome-blog-about-cats.com';
$keywords = ['cats', 'kittens', 'funny cats', 'cat pictures', 'cats art'];

$bot = PinterestBot::create();
$bot->auth->login('mypinterestlogin', 'mypinterestpassword');

if ($bot->user->isBanned()) {
    echo "Account has been banned!\n";
    die();
}

// get board id
$boards = $bot->boards->forUser('my_username');
$boardId = $boards[0]['id'];

// select image for posting
$images = glob('images/*.*');
if (empty($images)) {
    echo "No images for posting\n";
    die();
}

$image = $images[0];

// select keyword
$keyword = $keywords[array_rand($keywords)];

// create a pin
$bot->pins->create($image, $boardId, $keyword, $blogUrl);

// remove image
unlink($image);
