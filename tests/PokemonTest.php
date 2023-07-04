<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class PokemonTest extends ApiTestCase
{
    public function testGetPokemons(): void
    {
        $response = static::createClient()->request('GET', '/api/pokemon');
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertCount(50, $data['hydra:member']);
    }

    public function testGetPokemonsSize(): void
    {
        $response = static::createClient()->request('GET', '/api/pokemon?itemsPerPage=10');
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertCount(10, $data['hydra:member']);
    }
}
