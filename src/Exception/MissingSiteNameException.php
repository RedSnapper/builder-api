<?php

namespace RedSnapper\Builder\Exception;

use Exception;

class MissingSiteNameException extends Exception
{
    public function __construct()
    {
        parent::__construct('No site name has been specified for the Builder API');
    }
}