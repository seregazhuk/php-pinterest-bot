<?php

namespace seregazhuk\tests\Bot\Api;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\tests\Helpers\FollowResponseHelper;
use seregazhuk\PinterestBot\Api\Providers\Interests;

/**
 * Class InterestsTest.
 */
class InterestsTest extends ProviderTest
{
    /**
     * @var Interests
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = Interests::class;

    /** @test */
    public function it_should_return_main_categories()
    {
        $categories = ['category1', 'category2'];

        $this->apiShouldReturnData($categories)
            ->assertEquals($categories, $this->provider->getMain());
    }

    /** @test */
    public function it_should_return_category_info()
    {
        $info = ['name' => 'category1'];

        $this->apiShouldReturnData($info);

        $this->assertEquals($info, $this->provider->getInfo(1));
    }

    /** @test */
    public function it_should_return_generator_for_pins()
    {
        $this->apiShouldReturnPagination()
            ->apiShouldReturnEmpty()
            ->assertCount(2, iterator_to_array($this->provider->getPinsFor('test')));
    }
}
