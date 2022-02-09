<?php

namespace RedSnapper\Builder;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RedSnapper\Builder\Exception\MissingBuilderUsernameException;
use RedSnapper\Builder\Exception\MissingSiteNameException;

class PendingRequest
{
    /** @var string|null The name of the site in Builder */
    private ?string $siteName;

    /**
     * Whether the api should query the 'preview' version of the Builder site or not. if published is set to false, the
     * preview flag will be added
     *
     * @var bool
     */
    private bool $published;

    /**
     * If only the user is provided, a normal request will be made with an 'X-USER' header added, if user and password
     * are provided then a basic auth request will be made
     *
     * @var string|null
     */
    private ?string $user;
    private ?string $password;

    /**
     * The format of Builder's API url and its parts
     */
    private const REQUEST_URL_FORMAT = 'https://%s-edit.redsnapper.net/x/build.cgi?-E+%s';
    private const PREVIEW_SWITCH = '-P+';
    private const MACRO_FORMAT = '-macro+%s';
    private const PARAM_FORMAT = '+-parms+%s';

    public function __construct(
        ?string $siteName = null,
        ?string $user = null,
        ?string $password = null,
        ?bool $published = true,
    ) {
        $this->siteName = $siteName;
        $this->user = $user;
        $this->password = $password;
        $this->published = $published;
    }

    /**
     * @throws MissingBuilderUsernameException|MissingSiteNameException
     */
    public function get(string $macro, array $params = []): BuilderResponse
    {
        $this->validateSettings();

        $url = $this->buildRequestUrl($macro, $params);

        $response = $this->basicAuthRequired()
            ? $this->basicAuthRequest($url)
            : $this->userAuthRequest($url);

        return new BuilderResponse($response);
    }

    public function withAuth(string $user, string $password = null): static
    {
        $this->user = $user;
        $this->password = $password;

        return $this;
    }

    public function forSite(string $siteName): static
    {
        $this->siteName = $siteName;

        return $this;
    }

    public function published(): static
    {
        $this->published = true;

        return $this;
    }

    public function unpublished(): static
    {
        $this->published = false;

        return $this;
    }

    /**
     * @throws MissingSiteNameException
     * @throws MissingBuilderUsernameException
     */
    private function validateSettings()
    {
        if (empty($this->siteName)) {
            throw new MissingSiteNameException();
        }

        if (empty($this->user)) {
            throw new MissingBuilderUsernameException();
        }
    }

    private function basicAuthRequired(): bool
    {
        return !empty($this->password);
    }

    private function basicAuthRequest(string $url): Response
    {
        return Http::withBasicAuth($this->user, $this->password)->get($url);
    }

    private function userAuthRequest(string $url): Response
    {
        return Http::withHeaders(['X-USER' => $this->user])->get($url);
    }

    private function buildRequestUrl(string $macro, array $params): string
    {
        $url = ($this->published ? '' : self::PREVIEW_SWITCH);
        $url .= sprintf(self::MACRO_FORMAT, $macro);
        $url .= $this->buildParamsString($params);

        return sprintf(self::REQUEST_URL_FORMAT, $this->siteName, $url);
    }

    private function buildParamsString(array $params): string
    {
        if (empty($params)) {
            return '';
        }

        return sprintf(self::PARAM_FORMAT, implode(',', $params));
    }
}