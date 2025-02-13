<?php

namespace App\Services\OAuth2\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class MyobResourceOwner implements ResourceOwnerInterface
{
    protected array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId()
    {
        return $this->response['uid'] ?? null;
    }

    public function toArray(): array
    {
        return $this->response;
    }
}
