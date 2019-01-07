<?php

require __DIR__ . '/../vendor/autoload.php';

use seregazhuk\PinterestBot\Factories\PinterestBot;

$accounts = [
    [
        'login' => 'mylogin1',
        'password' => 'mypass1',
        'username' => 'cats_account',
        'images' => 'images/cats_pics',
        'link' => 'http://awasome-blog-about-cats.com',
        'proxy' => [
            'host' => '123.123.21.21',
            'port' => 1234
        ],
    ],
    [
        'login' => 'mylogin2',
        'password' => 'mypass2',
        'username' => 'dogs_account',
        'images' => 'images/dogs_pics',
        'link' => 'http://awasome-blog-about-dogs.com',
        'proxy' => [
            'host' => '123.123.22.22',
            'port' => 5678
        ]
    ]
];

/**
 * @param string $folder
 * @return string
 */
function getImage($folder)
{
    $images = glob("$folder/*.*");
    if (empty($images)) {
        echo "No images for posting\n";
        die();
    }

    return $images[0];
}

$bot = PinterestBot::create();

foreach ($accounts as $account) {
    // add proxy
    if (isset($account['proxy'])) {
        $proxy = $account['proxy'];
        $bot->getHttpClient()->useProxy($proxy['host'], $proxy['port']);
    }

    $bot->auth->login($account['login'], $account['password']);


    if ($bot->user->isBanned()) {
        $username = $account['username'];
        die("Account $username has been banned!\n");
    }

    // get board id
    $boards = $bot->boards->forUser($account['username']);
    $boardId = $boards[0]['id'];

    // select image for posting
    $image = getImage($account['images']);

    // select keyword
    $keywords = $account['keywords'];
    $keyword = $keywords[array_rand($keywords)];

    // create a pin
    $bot->pins->create($image, $boardId, $keyword, $account['link']);

    // remove image
    unlink($image);
    $bot->auth->logout();
}
