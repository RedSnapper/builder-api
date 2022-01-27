<?php

namespace RedSnapper\Builder\Tests\Unit;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RedSnapper\Builder\BuilderResponse;
use RedSnapper\Builder\Tests\TestCase;
use RedSnapper\Builder\Tests\Fixtures;

class BuilderResponseTest extends TestCase
{
    /** @test */
    public function can_instantiate_builder_response()
    {
        Http::fake();
        $response = new BuilderResponse(Http::get('test.com')->toPsrResponse());

        $this->assertTrue($response->successful());
    }

    /** @test */
    public function can_get_builder_data()
    {
        Http::fake([
            '*' => Http::response(Fixtures::successfulResponse()),
        ]);

        $response = new BuilderResponse(Http::get('test.com')->toPsrResponse());
        $data = $response->data();

        $this->assertCount(2, $data);
        $this->assertEquals('UK 12345', Arr::get($data, '0.code'));
        $this->assertEquals('About Page', Arr::get($data, '1.title'));
    }

    /** @test */
    public function data_returns_empty_array_if_no_data_exists_in_response()
    {
        Http::fake([
            '*' => Http::response(Fixtures::errorMsgResponse()),
        ]);

        $response = new BuilderResponse(Http::get('test.com')->toPsrResponse());

        $this->assertEmpty($response->data());
    }

    /** @test */
    public function can_get_builder_error_msg()
    {
        $errorMsg = 'Macro expansion requires a macro to expand';
        Http::fake([
            '*' => Http::response(Fixtures::errorMsgResponse($errorMsg), 422),
        ]);

        $response = new BuilderResponse(Http::get('test.com')->toPsrResponse());

        $this->assertEquals($errorMsg, $response->builderErrorMsg());
    }

    /** @test */
    public function null_is_returned_if_no_builder_error_msg_exists()
    {
        Http::fake([
            '*' => Http::response(Fixtures::emptyErrorMsgResponse(), 422),
        ]);

        $response = new BuilderResponse(Http::get('test.com')->toPsrResponse());

        $this->assertNull($response->builderErrorMsg());
    }
}