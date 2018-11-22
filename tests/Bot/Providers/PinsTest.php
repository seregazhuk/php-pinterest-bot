<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class PinsTest
 * @method Pins getProvider()
 */
class PinsTest extends ProviderBaseTest
{
    /** @test */
    public function it_retrieves_detailed_info_for_a_pin()
    {
        $provider = $this->getProvider();
        $provider->info('12345');

        $request = [
            'id'            => '12345',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_PIN_INFO, $request);
    }

    /** @test */
    public function it_fetches_pins_for_a_specified_source()
    {
        $provider = $this->getProvider();
        $provider->fromSource('http://flickr.com')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_DOMAIN_FEED, ['domain' => 'http://flickr.com']);
    }

    /** @test */
    public function it_fetches_users_activity_for_a_specified_pin()
    {
        $provider = $this->getProvider();
        $this->pinterestShouldReturn(['aggregated_pin_data' => ['id' => '123456']]);

        $provider->activity('123456')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_ACTIVITY, ['aggregated_pin_data_id' => '123456']);
    }

    /** @test */
    public function it_deletes_a_pin()
    {
        $provider = $this->getProvider();
        $provider->delete('12345');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_DELETE_PIN, ['id' => '12345']);
    }

    /** @test */
    public function it_fetches_analytics_about_a_pin()
    {
        $provider = $this->getProvider();
        $provider->analytics('12345');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_PIN_ANALYTICS, ['pin_id' => '12345']);
    }

    /** @test */
    public function it_fetches_trending_pins()
    {
        $provider = $this->getProvider();
        $provider->explore($topicId = '12345')->toArray();

        $request = [
            "aux_fields" => [],
            "prepend"    => false,
            "offset"     => 180,
            "section_id" => '12345',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_EXPLORE_PINS, $request);
    }

    /** @test */
    public function it_fetches_visual_similar_pins_for_a_specified_one()
    {
        $provider = $this->getProvider();
        $provider->visualSimilar('12345')->toArray();

        $request = [
            'pin_id'          => '12345',
            // Some magic numbers, I have no idea about them
            'crop'            => [
                "x"                => 0.16,
                "y"                => 0.16,
                "w"                => 0.66,
                "h"                => 0.66,
                "num_crop_actions" => 1,
            ],
            'force_refresh'   => true,
            'keep_duplicates' => false,
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_VISUAL_SIMILAR_PINS, $request);
    }

    /** @test */
    public function it_fetches_related_pins_for_a_specified_one()
    {
        $provider = $this->getProvider();
        $provider->related('12345')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_RELATED_PINS, ['pin' => '12345', 'add_vase' => true]);
    }

    /** @test */
    public function it_returns_a_current_user_feed()
    {
        $provider = $this->getProvider();
        $provider->feed()->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_FEED);
    }

    /** @test */
    public function a_user_can_search_in_own_pins()
    {
        $provider = $this->getProvider();
        $provider->searchInMyPins('query')->toArray();

        $this->assertWasGetRequest(
            UrlBuilder::RESOURCE_SEARCH, [
                'scope' => 'my_pins',
                'query' => 'query',
            ]
        );
    }

    /** @test */
    public function a_pin_can_be_copied_to_another_board()
    {
        $provider = $this->getProvider();
        $provider->copy($pinId = '12345', $boardId = '56789');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_BULK_COPY, [
                'board_id' => '56789',
                'pin_ids'  => ['12345'],
            ]
        );
    }

    /** @test */
    public function multiple_pins_can_be_copied_to_another_board()
    {
        $provider = $this->getProvider();
        $provider->copy($pinIds = ['123', '456'], $boardId = '56789');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_BULK_COPY, [
                'board_id' => '56789',
                'pin_ids'  => ['123', '456'],
            ]
        );
    }

    /** @test */
    public function multiple_pins_can_be_deleted_from_a_board()
    {
        $provider = $this->getProvider();
        $provider->deleteFromBoard($pinIds = ['1234', '5678'], $boardId = '12345678');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_BULK_DELETE, [
                'board_id' => '12345678',
                'pin_ids'  => ['1234', '5678'],
            ]
        );
    }

    /** @test */
    public function a_pin_can_be_moved_from_one_board_to_another()
    {
        $provider = $this->getProvider();
        $provider->move($pinId = '12345', $boardId = '6789');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_BULK_MOVE, [
                'board_id' => '6789',
                'pin_ids'  => ['12345'],
            ]
        );
    }

    /** @test */
    public function multiple_pins_can_be_moved_from_one_board_to_another()
    {
        $provider = $this->getProvider();
        $provider->move($pinIds = ['123', '456'], $boardId = '6789');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_BULK_MOVE, [
                'board_id' => '6789',
                'pin_ids'  => ['123', '456'],
            ]
        );
    }

    /** @test */
    public function a_pin_can_be_repinned_to_a_user_board()
    {
        $provider = $this->getProvider();
        $provider->repin(
            $pinId = '12345',
            $boardId = '56789',
            $description = 'my new pin description'
        );

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_REPIN, [
                'board_id'    => '56789',
                'description' => 'my new pin description',
                'link'        => '',
                'is_video'    => null,
                'pin_id'      => '12345',
            ]
        );
    }

    /** @test */
    public function a_user_can_edit_a_pin()
    {
        $provider = $this->getProvider();
        $provider->edit(
            $pinId = '12345',
            $description = 'my description',
            $link = 'http://example.com',
            $boardId = '5678',
            $title = 'new title'
        );

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_UPDATE_PIN, [
                'id'          => '12345',
                'description' => 'my description',
                'link'        => 'http://example.com',
                'board_id'    => '5678',
                'title'       => 'new title',
            ]
        );
    }

    /** @test */
    public function a_user_can_edit_a_pin_with_section_id()
    {
        $provider = $this->getProvider();
        $provider->edit(
            $pinId = '12345',
            $description = 'my description',
            $link = 'http://example.com',
            $boardId = '5678',
            $title = 'new title',
            $sectionId = '6789'
        );

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_UPDATE_PIN, [
                'id'               => '12345',
                'description'      => 'my description',
                'link'             => 'http://example.com',
                'board_id'         => '5678',
                'title'            => 'new title',
                'board_section_id' => '6789',
            ]
        );
    }

    /** @test */
    public function a_user_can_create_a_pin_with_image_from_a_link()
    {
        $provider = $this->getProvider();
        $provider->create(
            $imageUrl = 'http://example.com/images/image.jpg',
            $boardId = '12345678',
            $description = 'my description for this pin',
            $link = 'http://example.com',
            $title = 'My title'
        );

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_CREATE_PIN, [
                'method'      => 'scraped',
                'description' => 'my description for this pin',
                'link'        => 'http://example.com',
                'image_url'   => 'http://example.com/images/image.jpg',
                'board_id'    => '12345678',
                'title'       => 'My title',
            ]
        );
    }

    /** @test */
    public function a_user_can_create_a_pin_with_image_from_a_link_with_section_id()
    {
        $provider = $this->getProvider();
        $provider->create(
            $imageUrl = 'http://example.com/images/image.jpg',
            $boardId = '12345678',
            $description = 'my description for this pin',
            $link = 'http://example.com',
            $title = 'title',
            $sectionId = '23456789'
        );

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_CREATE_PIN, [
                'method'      => 'scraped',
                'description' => 'my description for this pin',
                'link'        => 'http://example.com',
                'image_url'   => 'http://example.com/images/image.jpg',
                'board_id'    => '12345678',
                'title'       => 'title',
                'section'     => '23456789',
            ]
        );
    }

    /**
     * @return string
     */
    protected function getProviderClass()
    {
        return Pins::class;
    }
}
