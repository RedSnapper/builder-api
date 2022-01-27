<?php

namespace RedSnapper\Builder\Exception;

use Exception;

class MissingBuilderAuthenticationException extends Exception
{
    public function __construct()
    {
        parent::__construct('No user and/or password has been specified for the Builder API');
    }
}