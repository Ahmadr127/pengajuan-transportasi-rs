<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AclCache
{
    public const VERSION_KEY = 'acl.version';

    public static function version(): string
    {
        return (string) Cache::rememberForever(self::VERSION_KEY, function () {
            return (string) now()->timestamp;
        });
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, now()->timestamp . '-' . Str::random(6));
    }
}
