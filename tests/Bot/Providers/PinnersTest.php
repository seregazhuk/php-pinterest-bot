<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Pinners;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class PinnersTest
 * @method Pinners getProvider()
 */
class PinnersTest extends ProviderBaseTest
{
    /** @test */
    public function it_returns_info_by_username()
    {
        $provider = $this->getProvider();
        $provider->info('johnDoe');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_INFO, ['username' => 'johnDoe']);
    }

    /** @test */
    public function it_can_block_user_by_name()
    {
        $provider = $this->getProvider();
        $provider->block('johnDoe');

        $this->pinterestShouldReturn(['id' => '12345']);

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_BLOCK_USER, ['blocked_user_id' => '12345']);
    }

    /** @test */
    public function it_fetches_user_info_to_block_a_user_by_name()
    {
        $provider = $this->getProvider();
        $provider->block('johnDoe');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_INFO, ['username' => 'johnDoe']);
    }

    protected function getProviderClass()
    {
        return Pinners::class;
    }
}
