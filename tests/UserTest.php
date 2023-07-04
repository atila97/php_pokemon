<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
{

    public function testCreateUser(): void
    {
        $params = [
            'email' => "",
            'password' => "",
        ];

        $client = static::createClient();
        $client->request('POST', '/api/register',);
        $this->assertResponseStatusCodeSame(401);
        $params = [
            'email' => "avalid_email@gmail.com",
            'password' => "Correct-pass123",
        ];
        $client->request('POST', '/api/register', [ 'body' => json_encode($params),]);
        $this->assertResponseStatusCodeSame(200);
        $client->request('POST', '/api/register', [ 'body' => json_encode($params),]);
        $this->assertResponseStatusCodeSame(403);
    }

    public function loginTest(): void
    {
        $params = [
            'email' => "",
            'password' => "",
        ];

        $client = static::createClient();
        $client->request('POST', '/api/login',);
        $this->assertResponseStatusCodeSame(401);

        $params = [
            'email' => "avalid_email@gmail.com",
            'password' => "Correct-pass123",
        ];

        $response = $client->request('POST', '/api/login', [ 'body' => json_encode($params),]);
        $data = json_decode($response->getContent());
        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayHasKey('token', $data);
    }
}
