<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Providers\User;
use seregazhuk\PinterestBot\Api\ProvidersContainer;

/**
 * @property ProvidersContainer container
 */
trait ResolvesCurrentUsername
{
    protected function resolveCurrentUsername()
    {
        /** @var User $userProvider */
        $userProvider = $this->container->getProvider('user');

        return $userProvider->username();
    }
}