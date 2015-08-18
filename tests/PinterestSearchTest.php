<?php
use szhuk\PinterestAPI\PinterestBot;
use szhuk\PinterestAPI\ApiRequest;

class PinterestSearchTest extends PinterestBotTest
{

    public function testSearchWithoutBookmarks()
    {
        $response = [
            'module'   => [
                'tree' => [
                    'data'     => [
                        'results' => [
                            'my_first_result',
                        ],
                    ],
                    'resource' => [
                        'options' => [
                            'bookmarks' => ['my_bookmarks'],
                        ],
                    ],
                ],
            ],
            'resource' => [
                'options' => ['bookmarks' => 'my_bookmarks'],
            ],
        ];

        $expected = [
            'data'      => $response['module']['tree']['data']['results'],
            'bookmarks' => $response['module']['tree']['resource']['options']['bookmarks'],
        ];

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(0))->method('exec')->willReturn($response);

        $response['module']['tree']['data']['results'] = [];
        $mock->expects($this->at(2))->method('exec')->willReturn($response);


        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $res = $this->bot->search('cats', PinterestBot::SEARCH_PINS_SCOPES, []);
        $this->assertEquals($expected, $res);

        $res = $this->bot->search('cats', PinterestBot::SEARCH_PINS_SCOPES, []);
        $this->assertEquals([], $res);

        $res = $this->bot->search('cats', PinterestBot::SEARCH_PINS_SCOPES, []);
        $this->assertEquals([], $res);
    }

    public function testSearchWithBookmarks()
    {
        $response = [
            'resource_response' => [
                'data' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
            ],
            'resource'          => [
                'options' => ['bookmarks' => 'my_bookmarks'],
            ],
        ];

        $expected = [
            'data'      => $response['resource_response']['data'],
            'bookmarks' => $response['resource']['options']['bookmarks'],
        ];

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($response);

        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $res = $this->bot->search('cats', PinterestBot::SEARCH_PINS_SCOPES, ['bookmarks']);
        $this->assertEquals($expected, $res);

    }

    public function testSearchFunctions()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $expectedResultsNum = count($response['module']['tree']['data']['results']);
        $mock               = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($response);
        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $res = iterator_to_array($this->bot->searchPins('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);

        $res = iterator_to_array($this->bot->searchPinners('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);

        $res = iterator_to_array($this->bot->searchBoards('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }
}
