<?php

namespace RedSnapper\Builder\Tests\Feature;

use Illuminate\Http\Client\Response;
use RedSnapper\Builder\BuilderResponse;
use RedSnapper\Builder\Tests\TestCase;

class BuilderResponseTest extends TestCase
{
    /** @test */
    public function data_method_returns_empty_array_if_data_key_does_not_exist()
    {
        $response = new Response(new \GuzzleHttp\Psr7\Response(200, [], json_encode([
            'result' => []
        ])));

        $builderResponse = new BuilderResponse($response);

        $this->assertEquals([], $builderResponse->data());
    }

    /** @test */
    public function data_method_returns_empty_array_if_data_key_is_null()
    {
        $response = new Response(new \GuzzleHttp\Psr7\Response(200, [], json_encode([
            'result' => [
                'data' => null
            ]
        ])));

        $builderResponse = new BuilderResponse($response);

        $this->assertEquals([], $builderResponse->data());
    }
}