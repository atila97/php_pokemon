<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Form\PokemonFilterType;
use App\Form\PokemonFormType;
use App\Form\PokemonType;
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
        if (null === $pokemon) {
            return $this->render('index/404.html.twig', []);
        }

        return $this->render('index/show.html.twig', [
            'pokemon' => $pokemon
        ]);
    }

    #[Route('/pokemon/{id}/edit', name: 'pokemon_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, ?Pokemon $pokemon, EntityManagerInterface $entityManager): Response
    {
        if (null === $pokemon) {
            return $this->render('index/404.html.twig', []);
        }

        if ($pokemon->getLegendary()) {
            $this->addFlash('error', "Un pokemon légéndaire ne peut pas être modifié.");
            return $this->redirectToRoute('pokemon_show', ["id" => $pokemon->getId()]);
        }

        $form = $this->createForm(PokemonFormType::class, $pokemon, ['method' => 'GET']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pokemon);
            $entityManager->flush();
            $this->addFlash('success', "Ce pokemon a été modifié");
            return $this->redirectToRoute('pokemon_show', ["id" => $pokemon->getId()]);
        }

        return $this->render('index/edit.html.twig', [
            'pokemon' => $pokemon,
            'form' => $form->createView()
        ]);
    }

    #[Route('/pokemon/{id}/delete', name: 'pokemon_delete', methods: ['DELETE', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, ?Pokemon $pokemon, EntityManagerInterface $entityManager): Response
    {
        if (null === $pokemon) {
            return $this->render('index/404.html.twig', []);
        }

        if ($pokemon->getLegendary()) {
            $this->addFlash('error', "Un pokemon légéndaire ne peut pas être supprimé.");
            return $this->redirectToRoute('pokemon_show', ["id" => $pokemon->getId()]);
        }

        if ($this->isCsrfTokenValid('delete'.$pokemon->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pokemon);
            $entityManager->flush();
            return $this->redirectToRoute('app_index');
        }
        $this->addFlash('success', "Un pokemon a été supprimé");
        return $this->redirectToRoute('pokemon_show', ["id" => $pokemon->getId()]);
    }

    #[Route('/index-with-paginator-bundle', name: 'app_index_paginator_bundle')]
    public function indexPaginator(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
