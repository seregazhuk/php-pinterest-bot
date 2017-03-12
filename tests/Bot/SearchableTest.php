<?php

namespace seregazhuk\tests\Bot\Api;

use Mockery;
use ReflectionClass;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Api\Traits\Searchable;

/**
 * Class ProviderTest.
 *
 * @property string $providerClass
 * @property ReflectionClass $reflection
 */
class SearchableTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_returns_pagination_object_on_search()
    {
        /** @var Searchable $provider */
        $provider = $this->getMockForTrait(Searchable::class);
        $this->assertInstanceOf(Pagination::class, $provider->search('cats'));
    }
}
