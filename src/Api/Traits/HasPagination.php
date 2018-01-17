<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\Pagination;

trait HasPagination
{
    /**
     * @param callable $callback
     * @param int $limit
     * @return Pagination
     */
    abstract protected function paginateCustom(callable $callback, $limit = Pagination::DEFAULT_LIMIT);
}
