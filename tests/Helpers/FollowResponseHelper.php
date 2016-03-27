<?php

namespace seregazhuk\tests\Helpers;

trait FollowResponseHelper 
{

    protected function setFollowErrorResponse()
    {
        return $this->setResponse(
            $this->createErrorApiResponse(), 1, 'followMethodCall'
        );
    }


    protected function setFollowSuccessResponse()
    {
        return $this->setResponse(
            $this->createSuccessApiResponse(), 1, 'followMethodCall'
        );
    }

}
