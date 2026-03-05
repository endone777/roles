<?php

namespace Endone777\Roles\Exceptions;

class LevelDeniedException extends AccessDeniedException
{
    public function __construct(string|int $level)
    {
        parent::__construct(sprintf("You don't have a required [%s] level.", $level));
    }
}
