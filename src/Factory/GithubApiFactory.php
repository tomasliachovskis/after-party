<?php

namespace App\Factory;

use Github\Api\ApiInterface;
use Github\Client;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GithubApiFactory
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string $apiType
     * @return ApiInterface
     */
    public function create($apiType): ApiInterface
    {
        $client = new Client();

        $token = $this->tokenStorage->getToken();
        if ($token && $token instanceof OAuthToken) {
            $client->authenticate($token->getAccessToken(), null, Client::AUTH_HTTP_TOKEN);
        }

        return $client->api($apiType);
    }
}
