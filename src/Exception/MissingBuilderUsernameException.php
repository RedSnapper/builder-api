<?php

namespace RedSnapper\Builder\Exception;

use Exception;

class MissingBuilderUsernameException extends Exception
{
    public function __construct()
    {
        parent::__construct('No user has been specified for the Builder API');
    }
}