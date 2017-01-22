<?php

namespace seregazhuk\tests\Bot\Api;

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
            ->assertEquals($categories, $this->provider->main());
    }

    /** @test */
    public function it_should_return_category_info()
    {
        $info = ['name' => 'category1'];

        $this->apiShouldReturnData($info);

        $this->assertEquals($info, $this->provider->info(1));
    }

    /** @test */
    public function it_should_return_generator_for_pins()
    {
        $response = $this->paginatedResponse;

        $this->apiShouldReturnPagination($response)
            ->assertIsPaginatedResponse($pins = $this->provider->pins('test'))
            ->assertPaginatedResponseEquals($response, $pins);
    }
}
