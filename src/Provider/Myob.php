<?php

namespace App\Services\OAuth2\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Myob extends AbstractProvider
{
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
    }

    public function getBaseAuthorizationUrl()
    {
        return 'https://secure.myob.com/oauth2/account/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://secure.myob.com/oauth2/v1/authorize';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return '';
    }

    protected function getDefaultScopes()
    {
        return [
            'CompanyFile',
            'la.global',
        ];
    }

    protected function getAccessTokenOptions(array $params)
    {
        return [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => $params['grant_type'] ?? 'authorization_code',
                'code' => $params['code'] ?? null,
                'refresh_token' => $params['refresh_token'] ?? null,
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => implode(' ', $this->getDefaultScopes()),
            ],
        ];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        \Log::info('MYOB Token Raw Response:', [
            'status' => $statusCode,
            'headers' => $response->getHeaders(),
            'body' => $body,
        ]);

        if ($body) {
            $data = json_decode($body, true) ?? ['error' => $body];
        }

        if ($statusCode >= 400 || ! empty($data['error'])) {
            \Log::error('MYOB API Error:', [
                'error' => $data['error'] ?? 'Unknown error',
                'description' => $data['error_description'] ?? 'No description',
                'status' => $statusCode,
                'response' => $data,
            ]);

            throw new IdentityProviderException(
                $data['error_description'] ?? $data['error'] ?? $response->getReasonPhrase(),
                $statusCode,
                $data
            );
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new MyobResourceOwner($response);
    }

    public function getCompanyUrl(AccessToken $token)
    {
        return config('services.myob.company_file_url', '');
    }
}
