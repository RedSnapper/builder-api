<?php

namespace RedSnapper\Builder\Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RedSnapper\Builder\BuilderRequestFactory;
use RedSnapper\Builder\Facades\BuilderApi;
use RedSnapper\Builder\PendingRequest;
use RedSnapper\Builder\Tests\TestCase;

class BuilderRequestFactoryTest extends TestCase
{
    /** @test */
    public function can_make_a_new_pending_request()
    {
        $factory = new BuilderRequestFactory();
        $pendingRequest = $factory->new();

        $this->assertInstanceOf(PendingRequest::class, $pendingRequest);
    }

    /** @test */
    public function pending_request_will_use_configuration_passed_in_factory_constructor_by_default()
    {
        Http::fake();

        $factory = new BuilderRequestFactory('test-site', 'testuser', '123');
        $pendingRequest = $factory->new();

        $pendingRequest->get('apiPages');

        $expectedUrl = 'https://test-site-edit.redsnapper.net/x/build.cgi?-E+-macro+apiPages';
        Http::assertSent(function (Request $request) use ($expectedUrl) {
            return $request->url() === $expectedUrl;
        });
    }

    /** @test */
    public function can_use_facade_to_make_request()
    {
        $pendingRequest = BuilderApi::new();

        $this->assertInstanceOf(PendingRequest::class, $pendingRequest);
    }
}