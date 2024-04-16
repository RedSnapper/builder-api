<?php

namespace RedSnapper\Builder\Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RedSnapper\Builder\Exception\MissingBuilderUsernameException;
use RedSnapper\Builder\Exception\MissingSiteNameException;
use RedSnapper\Builder\PendingRequest;
use RedSnapper\Builder\Tests\TestCase;

class PendingRequestTest extends TestCase
{
	/** @test */
	public function if_site_name_is_not_provided_exception_is_thrown()
	{
		$this->expectException(MissingSiteNameException::class);
		$pendingRequest = new PendingRequest(siteName: null, user: null, password: null);
		$pendingRequest->get('apiPages');
	}

	/** @test */
	public function if_username_is_not_provided_exception_is_thrown()
	{
		$this->expectException(MissingBuilderUsernameException::class);

		$pendingRequest = new PendingRequest(siteName: 'testsite', user: null, password: '123');
		$pendingRequest->get('apiPages');
	}

	/** @test */
	public function can_make_get_request()
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
			]),
		]);

		$pendingRequest = new PendingRequest(siteName: 'testsite', user: 'test-user', password: 'key-123');
		$response = $pendingRequest->get('apiPages');

		$expectedUrl = 'https://testsite-edit.redsnapper.net/x/build.cgi?-E+-macro+apiPages';
		Http::assertSent(function (Request $request) use ($expectedUrl) {
			return $request->url() === $expectedUrl
				&& $request->hasHeader('Authorization');
		});

		$data = $response->data();
		$this->assertCount(2, $data);
	}

	/** @test */
	public function can_make_get_request_with_preview_switch()
	{
		Http::fake();

		// setting 'published' to false will add the preview switch
		$pendingRequest = new PendingRequest(
			siteName: 'testsite',
			user: 'test-user',
			password: 'key-123',
			published: false
		);

		$pendingRequest->get('apiPages');

		$expectedUrl = 'https://testsite-edit.redsnapper.net/x/build.cgi?-E+-macro+apiPages-P+';

		Http::assertSent(function (Request $request) use ($expectedUrl) {
			return $request->url() === $expectedUrl;
		});
	}

	/** @test */
	public function can_make_get_request_with_params()
	{
		Http::fake();

		$pendingRequest = new PendingRequest(siteName: 'testsite', user: 'test-user', password: 'key-123');
		$pendingRequest->get('apiPages', ['foo', 'bar']);

		$expectedUrl = 'https://testsite-edit.redsnapper.net/x/build.cgi?-E+-macro+apiPages+-parms+foo,bar+-lpt';

		Http::assertSent(function (Request $request) use ($expectedUrl) {
			return $request->url() === $expectedUrl;
		});
	}

	/** @test */
	public function can_make_get_request_with_preview_switch_and_params()
	{
		Http::fake();

		$pendingRequest = new PendingRequest(
			siteName: 'testsite',
			user: 'test-user',
			password: 'key-123',
			published: false
		);

		$pendingRequest->get('apiPages', ['foo', 'bar']);

		$expectedUrl = 'https://testsite-edit.redsnapper.net/x/build.cgi?-E+-macro+apiPages+-parms+foo,bar+-lpt-P+';

		Http::assertSent(function (Request $request) use ($expectedUrl) {
			return $request->url() === $expectedUrl;
		});
	}

	/** @test */
	public function can_make_request_with_username_only()
	{
		Http::fake();

		$pendingRequest = new PendingRequest(siteName: 'testsite', user: 'test-user');
		$pendingRequest->get('apiPages');

		Http::assertSent(function (Request $request) {
			return $request->hasHeader('X-USER', 'test-user')
				&& !$request->hasHeader('Authorization');
		});
	}

	/** @test */
	public function can_use_fluent_setters_to_overwrite_constructor_config()
	{
		Http::fake();

		$pendingRequest = new PendingRequest(siteName: 'testsite', user: 'test-user', password: null, published: true);
		$pendingRequest->forSite('second-site')
			->withAuth('second-user')
			->unpublished()
			->get('apiPages');

		$expectedUrl = 'https://second-site-edit.redsnapper.net/x/build.cgi?-E+-macro+apiPages-P+';

		Http::assertSent(function (Request $request) use ($expectedUrl) {
			return $request->url() === $expectedUrl && $request->hasHeader('X-USER', 'second-user');
		});
	}
}