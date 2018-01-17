<?php

require __DIR__ . '/../vendor/autoload.php';

use seregazhuk\PinterestBot\Factories\PinterestBot;

const IMAGES_DIR = 'images';
$bot = PinterestBot::create();

$pins = $bot->pins->search('cats')->take(100)->toArray();

foreach ($pins as $pin) {
    $originalUrl = $pin['images']['orig']['url'];
    $destination = IMAGES_DIR . DIRECTORY_SEPARATOR . basename($originalUrl);
    file_put_contents($destination, file_get_contents($originalUrl));
}
