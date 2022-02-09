<?php

namespace RedSnapper\Builder\Tests\Unit;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RedSnapper\Builder\BuilderResponse;
use RedSnapper\Builder\Tests\TestCase;

class BuilderResponseTest extends TestCase
{
    /** @test */
    public function can_instantiate_builder_response()
    {
        Http::fake();
        $response = new BuilderResponse(Http::get('test.com'));

        $this->assertTrue($response->successful());
    }

    /** @test */
    public function can_get_builder_data()
    {
        Http::fake([
            '*' => Http::response([
                    'result' => [
                        'data' => [
                            [
                                'code' => 'UK 123',
                            ],
                            [
                                'code' => 'UK 456',
                            ],
                        ],
                    ],
                ]
            ),
        ]);

        $response = new BuilderResponse(Http::get('test.com'));
        $data = $response->data();

        $this->assertCount(2, $data);
        $this->assertEquals('UK 123', Arr::get($data, '0.code'));
        $this->assertEquals('UK 456', Arr::get($data, '1.code'));
    }

    /** @test */
    public function data_returns_empty_array_if_no_data_exists_in_response()
    {
        Http::fake([
            '*' => Http::response(
                [
                    'result' => [],
                ]
            ),
        ]);

        $response = new BuilderResponse(Http::get('test.com'));

        $this->assertEmpty($response->data());
    }

    /** @test */
    public function can_get_builder_error_msg()
    {
        $errorMsg = 'Macro expansion requires a macro to expand';

        Http::fake([
            '*' => Http::response([
                'log' => [
                    'messages' => [
                        [
                            'message' => 'Builder v2022.012',
                        ],
                        [
                            'message' => $errorMsg,
                        ],
                    ],
                ],
            ], 422),
        ]);

        $response = new BuilderResponse(Http::get('test.com'));

        $this->assertEquals($errorMsg, $response->errorMsg());
    }

    /** @test */
    public function null_is_returned_if_no_builder_error_msg_exists()
    {
        Http::fake([
            '*' => Http::response([
                'log' => [
                    'messages' => [
                        [
                            'message' => 'Builder v2022.012',
                        ],
                    ],
                ],
            ], 422),
        ]);

        $response = new BuilderResponse(Http::get('test.com'));

        $this->assertNull($response->errorMsg());
    }
}