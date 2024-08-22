<?php

namespace RedSnapper\Builder;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

class BuilderResponse
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function data(): array
    {
        return $this->response->json('result.data', []) ?? [];
    }

    public function errorMsg(): ?string
    {
        if ($this->successful()) {
            return null;
        }

        $messages = $this->response->json('log.messages', []);
        if (count($messages) > 1) {
            // get the message string from the second message item - the first message item always contains
            // build information, if there is an error msg it will be the second item
            return Arr::get($messages[1], 'message');
        }

        return null;
    }

    public function successful(): bool
    {
        return $this->response->successful();
    }

    public function failed(): bool
    {
        return $this->response->failed();
    }

    public function status(): int
    {
        return $this->response->status();
    }

    /**
     * @throws RequestException
     */
    public function throw(): static
    {
        $this->response->throw();

        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}