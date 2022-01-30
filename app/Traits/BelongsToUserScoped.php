<?php

namespace App\Traits;

use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;

trait BelongsToUserScoped
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function bootBelongsToUser() : void
    {
        $userScope = self::SCOPE;

        if ($userScope == 'none') {
            return;
        }

        if ($userScope !== 'notShared') {
            static::addGlobalScope(new UserScope);

            return;
        }

        static::addGlobalScope(new UserScopeNotShared);
    }
}
