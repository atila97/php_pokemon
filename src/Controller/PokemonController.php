<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Form\PokemonFilterType;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PokemonController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager, 
        private PokemonRepository $pokemonRepository)
    {
    }


    #[Route('/', name: 'app_index')]
    public function index(Request $request): Response
    {

        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('size', 50);
        $form = $this->createForm(PokemonFilterType::class, null, ['method' => 'GET']);
        $form->handleRequest($request);
        $totalItems = $this->pokemonRepository->countPokemons($form->getData());
        if ($form->isSubmitted()) {
         //   $page = 1;
        }

        $paginatedData = $this->pokemonRepository->paginate($page, $pageSize, $form->getData());
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'paginatedData' => $paginatedData,
            'currentPage' => $page,
            'totalPages' => ceil($totalItems / $pageSize),
            'form' => $form->createView()
        ]);
    }

    #[Route('/pokemon/{id}', name: 'pokemon_show')]
    public function show(?Pokemon $pokemon): Response
    {
        return $this->render('index/show.html.twig', [
            'pokemon' => $pokemon
        ]);
    }

    #[Route('/pokemon/{id}/edit', name: 'pokemon_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(?Pokemon $pokemon): Response
    {
        return $this->render('index/show.html.twig', [
            'pokemon' => $pokemon
        ]);
    }

    #[Route('/index-with-paginator-bundle', name: 'app_index_paginator_bundle')]
    public function indexPaginator(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
