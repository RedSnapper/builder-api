<?php

namespace RedSnapper\Builder\Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RedSnapper\Builder\BuilderClient;
use RedSnapper\Builder\Exception\MissingBuilderUsernameException;
use RedSnapper\Builder\Exception\MissingSiteNameException;
use RedSnapper\Builder\Tests\Fixtures;
use RedSnapper\Builder\Tests\TestCase;

class BuilderClientTest extends TestCase
{
    /** @test */
    public function if_site_name_is_not_provided_exception_is_thrown()
    {
        $this->expectException(MissingSiteNameException::class);
        $client = new BuilderClient(null, null, null);
        $client->get('apiPages');
    }

    /** @test */
    public function if_auth_is_not_provided_exception_is_thrown()
    {
        $this->expectException(MissingBuilderUsernameException::class);

        $client = new BuilderClient('testsite', null, null);
        $client->get('apiPages');
    }

    /** @test */
    public function if_user_is_not_provided_exception_is_thrown()
    {
        $this->expectException(MissingBuilderUsernameException::class);

        $client = new BuilderClient('testsite', null, '123');
        $client->get('apiPages');
    }

    /** @test */
    public function can_make_get_request()
    {
        Http::fake([
            '*' => Http::response(Fixtures::successfulResponse()),
        ]);

        $client = new BuilderClient('testsite', 'test-user', 'key-123');
        $response = $client->get('apiPages');

        $expectedUrl = 'https://testsite-edit.redsnapper.net/x/build.cgi?-E+-macro+apiPages';
        Http::assertSent(function (Request $request) use ($expectedUrl) {
            return $request->url() === $expectedUrl
                && $request->hasHeader('Authorization');
        });

        $data = $response->data();
        $this->assertCount(2, $data);
    }

    /** @test */
    public function basic_auth_is_set_correctly()
    {
        Http::fake();
        Http::shouldReceive('withBasicAuth')->with('test-user', 'key-123')->once()->andReturnSelf();
        Http::shouldReceive('get')->once()->andReturn(new Response(new \GuzzleHttp\Psr7\Response()));

        $client = new BuilderClient('testsite', 'test-user', 'key-123');
        $client->get('apiPages');
    }

    /** @test */
    public function can_make_get_request_with_preview_switch()
    {
        Http::fake();

        $client = new BuilderClient('testsite', 'test-user', 'key-123', true);
        $client->get('apiPages');

        $expectedUrl = 'https://testsite-edit.redsnapper.net/x/build.cgi?-E+-P+-macro+apiPages';

        Http::assertSent(function (Request $request) use ($expectedUrl) {
            return $request->url() === $expectedUrl;
        });
    }

    /** @test */
    public function can_make_get_request_with_params()
    {
        Http::fake();

        $client = new BuilderClient('testsite', 'test-user', 'key-123');
        $client->get('apiPages', ['foo', 'bar']);

        $expectedUrl = 'https://testsite-edit.redsnapper.net/x/build.cgi?-E+-macro+apiPages+-parms+foo,bar';

        Http::assertSent(function (Request $request) use ($expectedUrl) {
            return $request->url() === $expectedUrl;
        });
    }

    /** @test */
    public function can_make_get_request_with_preview_switch_and_params()
    {
        Http::fake();

        $client = new BuilderClient('testsite', 'test-user', 'key-123', true);
        $client->get('apiPages', ['foo', 'bar']);

        $expectedUrl = 'https://testsite-edit.redsnapper.net/x/build.cgi?-E+-P+-macro+apiPages+-parms+foo,bar';

        Http::assertSent(function (Request $request) use ($expectedUrl) {
            return $request->url() === $expectedUrl;
        });
    }

    /** @test */
    public function can_make_request_with_username_only()
    {
        Http::fake();

        $client = new BuilderClient('testsite', 'test-user');
        $client->get('apiPages');

        Http::assertSent(function (Request $request) {
            return $request->hasHeader('X-USER', 'test-user')
                && !$request->hasHeader('Authorization');
        });
    }

    /** @test */
    public function basic_auth_is_not_set_when_username_only_provided()
    {
        Http::fake();
        Http::shouldReceive('withBasicAuth')->never();
        Http::shouldReceive('withHeaders')->andReturnSelf()->once();
        Http::shouldReceive('get')->once()->andReturn(new Response(new \GuzzleHttp\Psr7\Response()));

        $client = new BuilderClient('testsite', 'test-user');
        $client->get('apiPages');
    }
}