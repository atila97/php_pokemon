<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use App\Repository\PokemonTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/api')]
class ApiController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager, 
    private PokemonRepository $pokemonRepository, private PokemonTypeRepository $pokemonTypeRepository)
    {
    }

    #[Route('/pokemons', name: 'app_api')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('size', 50);;
        $totalItems = $this->pokemonRepository->countPokemons(null);
        $paginatedData = $this->pokemonRepository->paginate($page, $pageSize, null);
        return $this->json([
            'paginatedData' => $paginatedData,
            'currentPage' => $page,
            'totalPages' => ceil($totalItems / $pageSize)
        ]);
    }
    
    #[Route('/pokemon/{id}', name: 'api_pokemon_update', methods: 'PUT')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, ?Pokemon $pokemon): Response
    {
        if($pokemon->getLegendary()) {
            return $this->json([
                'message' => "Un pokemon légéndaire ne peut pas être modifié",
            ], Response::HTTP_UNAUTHORIZED);
        }
        $data = json_decode($request->getContent(), true);
        $pokemon->setName($data["name"] ?? null)
            ->setLegendary($data["legendary"] ?? null)
            ->setGeneration($data["generation"] ?? null);
        $type1 =  $this->pokemonTypeRepository->findOneBy(["id" => $data['type1'] ?? 0]);
        $type2 =  $this->pokemonTypeRepository->findOneBy(["id" => $data['type2'] ?? 0]);
        $pokemon->setTypes($type1, $type2);
        $this->entityManager->persist($pokemon);
        $this->entityManager->flush();
        return $this->json([
            'status' => $pokemon,
        ]);
    }

    #[Route('/pokemon/{id}', name: 'api_pokemon_delete', methods: 'DELETE')]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, ?Pokemon $pokemon): Response
    {
        if($pokemon->getLegendary()) {
            return $this->json([
                'message' => "Un pokemon légéndaire ne peut pas être supprimé",
            ], Response::HTTP_UNAUTHORIZED);
        }

        $this->entityManager->remove($pokemon);
        $this->entityManager->flush();
        return $this->json(['status' => 'OK']);
    }


}
