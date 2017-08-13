<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Providers\User;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

/**
 * @property ProvidersContainer container
 */
trait ResolvesCurrentUser
{
    /**
     * @return string
     */
    protected function resolveCurrentUsername()
    {
        return $this->getUserProvider()->username();
    }

    /**
     * @return string
     */
    protected function resolveCurrentUserId()
    {
        return $this->getUserProvider()->id();
    }

    /**
     * @return User|Provider
     */
    protected function getUserProvider()
    {
        return $this->container->getProvider('user');
    }
}
