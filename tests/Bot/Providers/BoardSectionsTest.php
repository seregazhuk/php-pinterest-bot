<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\BoardSections;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class BoardSectionsTest
 * @method BoardSections getProvider()
 */
class BoardSectionsTest extends ProviderBaseTest
{
    /** @test */
    public function it_fetches_sections_for_a_specified_board()
    {
        $provider = $this->getProvider();

        $provider->forBoard('12345');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_BOARD_SECTIONS, ['board_id' => '12345']);
    }

    /** @test */
    public function it_edits_a_section()
    {
        $this->login();
        $this->getProvider()->update('12345', 'test');

        $request = [
            'section_id' => '12345',
            'name'       => 'test',
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_EDIT_BOARD_SECTION, $request);
    }

    /** @test */
    public function it_removes_section()
    {
        $this->login();
        $this->getProvider()->delete('12345');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_DELETE_BOARD_SECTION, ['section_id' => '12345']);
    }

    /**
     * @return string
     */
    protected function getProviderClass()
    {
        return BoardSections::class;
    }
}
