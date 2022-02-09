<?php

namespace RedSnapper\Builder;

class BuilderRequestFactory
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
     * Create a new PendingRequest
     *
     * @return PendingRequest
     */
    public function new(): PendingRequest
    {
        return new PendingRequest(
            $this->siteName,
            $this->user,
            $this->password,
            $this->published
        );
    }
}