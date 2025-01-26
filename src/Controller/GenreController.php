<?php

namespace App\Controller;

use App\Elasticsearch\Genre\GenreClient;
use App\Form\GenreType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/genre', name: 'app_genre_')]
class GenreController extends AbstractController
{
    public function __construct(
        private readonly GenreClient $genreClient,
    )
    {
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, string $id): Response
    {
        $movie = $this->genreClient->get($id);

        $form = $this->createForm(GenreType::class, $movie['_source']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->genreClient->update($id, $form->getData());

            return $this->redirectToRoute('app_movie_edit', ['id' => $id]);
        }

        return $this->render('form/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function createAction(Request $request): Response
    {
        $form = $this->createForm(GenreType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->genreClient->add($data);
        }

        return $this->render('form/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}