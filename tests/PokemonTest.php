<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Factory\PokemonFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PokemonTest extends ApiTestCase
{
    use ResetDatabase, Factories;
    
    private $token;

    public function testGetPokemons(): void
    {
        PokemonFactory::createMany(100);

        $response = static::createClient()->request('GET', '/api/pokemon');
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertCount(50, $data['hydra:member']);
    }

    public function testGetPokemonsSize(): void
    {
        PokemonFactory::createMany(100);
        $response = static::createClient()->request('GET', '/api/pokemon?itemsPerPage=10');
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertCount(10, $data['hydra:member']);
    }

    public function testUpdatePokemon(): void
    {
        $client = static::createClient();
        $pokemon = PokemonFactory::randomOrCreate([
            'legendary' => false,
            "name" => "Pikachu",
            "generation" => 3
        ]);

        $client->request('PUT', '/api/pokemon/'.$pokemon->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $client = $this->createClientWithCredentials($this->token);

        $params = [
            "name" => 'Pikacchu Test',
            "generation" => 1,
            'legendary' => true
        ];

        $response = $client->request('PUT', '/api/pokemon/'.$pokemon->getId(),
            [ 'body' => json_encode($params),]
        );
        $data = json_decode($response->getContent());;
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals($params["name"], $data->status->name);
        $this->assertEquals($params["generation"], $data->status->generation);
        $this->assertEquals($params["legendary"], $data->status->legendary);

    }

    public function testUpdatePokemonLegendary(): void
    {
        $client = static::createClient();
        $pokemon = PokemonFactory::randomOrCreate([
            'legendary' => true
        ]);

        $client->request('PUT', '/api/pokemon/'.$pokemon->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $client = $this->createClientWithCredentials($this->token);
        try {
            $response = $client->request('PUT', '/api/pokemon/'.$pokemon->getId());
            $data = json_decode($response->getContent());;
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Contracts\HttpClient\Exception\ExceptionInterface) {
                $statusCode = $e->getCode();
                $this->assertEquals(Response::HTTP_UNAUTHORIZED, $statusCode);
            }
        }
    }

    
    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getUserToken();
        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$token]]);
    }

    protected function getUserToken(): string
    {
        if (null !== $this->token) {
            return $this->token;
        }

        $params = [
            'email' => "avalid_email@gmail.com",
            'password' => "Correct-pass123",
        ];
        static::createClient()->request('POST', '/api/register', [ 'body' => json_encode($params),]);
        $this->assertResponseStatusCodeSame(200);

        $response = static::createClient()->request('POST', '/api/login', [ 
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($params)
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());
        $this->token = $data->token;
        return $data->token;
    }
}
