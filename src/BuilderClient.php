<?php

namespace RedSnapper\Builder;

use Illuminate\Support\Facades\Http;
use RedSnapper\Builder\Exception\MissingBuilderAuthenticationException;
use RedSnapper\Builder\Exception\MissingSiteNameException;

class BuilderClient
{
    /** @var string|null The name of the site in Builder */
    private ?string $siteName;

    /** @var bool Whether the api should query the 'preview' version of the Builder site or not */
    private bool $preview;

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
        ?string $siteName,
        ?string $user,
        ?string $password,
        ?bool $preview = false,
    ) {
        $this->siteName = $siteName;
        $this->user = $user;
        $this->password = $password;
        $this->preview = $preview;
    }

    /**
     * @throws MissingSiteNameException
     * @throws MissingBuilderAuthenticationException
     */
    public function get(string $macro, array $params = []): BuilderResponse
    {
        $this->validateSettings();

        $url = $this->buildRequestUrl($macro, $params);

        $response = Http::withBasicAuth($this->user, $this->password)->get($url);

        return new BuilderResponse($response->toPsrResponse());
    }

    public function setAuth(string $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function setSiteName(string $siteName)
    {
        $this->siteName = $siteName;
    }

    public function setPreview(bool $preview = true)
    {
        $this->preview = $preview;
    }

    /**
     * @throws MissingSiteNameException
     * @throws MissingBuilderAuthenticationException
     */
    private function validateSettings()
    {
        if (empty($this->siteName)) {
            throw new MissingSiteNameException();
        }

        if (empty($this->user) || empty($this->password)) {
            throw new MissingBuilderAuthenticationException();
        }
    }

    private function buildRequestUrl(string $macro, array $params): string
    {
        $url = ($this->preview ? self::PREVIEW_SWITCH : '');
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