<?php

namespace seregazhuk\tests\Bot;

use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\tests\Helpers\ResponseHelper;
use seregazhuk\PinterestBot\Helpers\Pagination;

/**
 * Class RequestTest.
 */
class PaginationTest extends TestCase
{
    use ResponseHelper;

    /** @test */
    public function it_returns_first_result_when_no_bookmarks()
    {
        $pagination = new Pagination();
        $responseData = $this->createSuccessApiResponse($this->paginatedResponse);

        $pagination->paginateOver(function() use ($responseData){
            return (new Response())->fill($responseData);
        });

        $this->assertEquals($this->paginatedResponse, $pagination->toArray());
    }

    /** @test */
    public function it_always_return_iterator_object()
    {
        $pagination = new Pagination();

        $this->assertInstanceOf(\IteratorAggregate::class, $pagination);
        $this->assertEmpty($pagination->toArray());
    }

    /** @test */
    public function it_uses_bookmarks_for_iteration_over_responses()
    {
        $pagination = new Pagination();
        $responseData = $this->createPaginatedResponse($this->paginatedResponse, 'my_bookrmarks');

        $pagination->paginateOver(function() use ($responseData){
            return (new Response())->fill($responseData);
        });

        $this->assertCount(Pagination::DEFAULT_LIMIT, $pagination->toArray());
    }

    /** @test */
    public function it_can_skip_n_first_results()
    {
        $skip = 2;
        $pagination = new Pagination();
        $data = [];

        foreach (range(1, Pagination::DEFAULT_LIMIT + $skip) as $value) {
            $data[] = ['id'=> $value];
        }

        $responseData = $this->createPaginatedResponse($data, 'my_bookrmarks');

        $pagination->paginateOver(function() use ($responseData){
            return (new Response())->fill($responseData);
        })->skip($skip);

        $expected = array_slice($data, 2);

        $this->assertEquals($expected, $pagination->toArray());
    }

    /** @test */
    public function it_can_limit_results()
    {
        $limit = 2;
        $data = [];

        foreach (range(1, Pagination::DEFAULT_LIMIT) as $value) {
            $data[] = ['id'=> $value];
        }

        $responseData = $this->createPaginatedResponse($data, 'my_bookrmarks');

        $pagination = new Pagination();
        $pagination->paginateOver(function() use ($responseData){
            return (new Response())->fill($responseData);
        })->take($limit);

        $expected = array_slice($data, 0, $limit);

        $this->assertEquals($expected, $pagination->toArray());
    }

    /** @test */
    public function it_accepts_limit_in_constructor()
    {
        $limit = 2;
        $data = [];

        foreach (range(1, Pagination::DEFAULT_LIMIT) as $value) {
            $data[] = ['id'=> $value];
        }

        $responseData = $this->createPaginatedResponse($data, 'my_bookrmarks');

        $pagination = new Pagination($limit);
        $pagination->paginateOver(function() use ($responseData){
            return (new Response())->fill($responseData);
        });

        $expected = array_slice($data, 0, $limit);

        $this->assertEquals($expected, $pagination->toArray());
    }

    /** @test */
    public function it_stops_when_response_is_empty()
    {
        $pagination = new Pagination();
        $pagination->paginateOver(function(){
            return (new Response())->fill([]);
        });

        $this->assertCount(0, $pagination->toArray());
    }

    /** @test */
    public function it_has_helper_to_retrieve_an_iterator()
    {
        $pagination = (new Pagination())->get();

        $this->assertInstanceOf(\Traversable::class, $pagination);
    }
}


