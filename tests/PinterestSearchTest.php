<?php
use szhuk\PinterestAPI\PinterestBot;
use szhuk\PinterestAPI\ApiRequest;

class PinterestSearchTest extends PinterestBotTest
{

    public function testSearch()
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

        $res = $this->bot->search('cats', PinterestBot::SEARCH_PINS_SCOPES, 'bookmarks');
        $this->assertEquals($expected, $res);
    }


    public function testSearchFunctions()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];
        $expectedResultsNum                            = count($response['module']['tree']['data']['results']);

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($response);
        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $res = iterator_to_array($this->bot->searchPins('dogs'));
        $this->assertCount($expectedResultsNum, $res[0]);

        $res = iterator_to_array($this->bot->searchPinners('dogs'));
        $this->assertCount($expectedResultsNum, $res[0]);

        $res = iterator_to_array($this->bot->searchBoards('dogs'));
        $this->assertCount($expectedResultsNum, $res[0]);
    }

}
