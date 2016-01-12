<?php

namespace seregazhuk\PinterestBot\Contracts;

interface ProvidersContainerInterface
{
    public function getProvider($provider);
}