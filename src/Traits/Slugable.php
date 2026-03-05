<?php

namespace Endone777\Roles\Traits;

use Illuminate\Support\Str;

trait Slugable
{
    public function setSlugAttribute(string $value): void
    {
        $this->attributes['slug'] = Str::slug($value, config('roles.separator'));
    }
}
