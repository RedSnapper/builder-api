<?php

namespace RedSnapper\Builder;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use \Psr\Http\Message\ResponseInterface;

class BuilderResponse extends Response
{
    public function __construct(ResponseInterface $response)
    {
        parent::__construct($response);
    }

    public function data(): array
    {
        return $this->json('result.data', []);
    }

    public function builderErrorMsg(): ?string
    {
        if ($this->successful()) {
            return null;
        }

        $messages = $this->json('log.messages', []);
        if (count($messages) > 1) {
            // get the message string from the second message item - the first message item always contains
            // build information, if there is an error msg it will be the second item
            return Arr::get($messages[1], 'message');
        }

        return null;
    }
}