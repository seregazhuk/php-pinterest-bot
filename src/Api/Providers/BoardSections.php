<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Providers\Core\Provider;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class BoardSections extends Provider
{
    /**
     * @var string[]
     */
    protected $loginRequiredFor = [
        'create',
        'edit',
        'delete',
        'forBoard',
    ];

    /**
     * @param string $boardId
     * @return array|bool
     */
    public function forBoard($boardId)
    {
        return $this->get(UrlBuilder::RESOURCE_GET_BOARD_SECTIONS, ['board_id' => $boardId]);
    }

    /**
     * @param string $boardId
     * @param string $title
     * @return bool
     */
    public function create($boardId, $title)
    {
        $requestOptions = [
            'board_id' => $boardId,
            'name' => $title,
            'initial_pins' => []
        ];

        return $this->post(UrlBuilder::RESOURCE_ADD_BOARD_SECTION, $requestOptions);
    }

    /**
     * @param string $sectionId
     * @param string $title
     * @return bool
     */
    public function update($sectionId, $title)
    {
        $requestOptions = [
            'section_id' => $sectionId,
            'name' => $title,
        ];

        return $this->post(UrlBuilder::RESOURCE_EDIT_BOARD_SECTION, $requestOptions);
    }

    /**
     * @param string $sectionId
     * @return bool
     */
    public function delete($sectionId)
    {
        return $this->post(UrlBuilder::RESOURCE_DELETE_BOARD_SECTION, ['section_id' => $sectionId]);
    }
}
