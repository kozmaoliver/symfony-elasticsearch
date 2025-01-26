<?php

namespace App\Controller;

use App\Elasticsearch\Movie\MovieClient;
use App\Form\MovieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/movie', name: 'app_movie_')]
class MovieController extends AbstractController
{

    public function __construct(
        private readonly MovieClient $movieClient,
    )
    {
    }

    #[Route('/init', name: 'init')]
    public function init(): JsonResponse
    {
        try {
            $this->movieClient->createIndex();

        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()]);
        }

        return new JsonResponse(['message' => 'OK']);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, string $id): Response
    {
        $movie = $this->movieClient->get($id);

        $form = $this->createForm(MovieType::class, $movie['_source']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->movieClient->update($id, $form->getData());

            return $this->redirectToRoute('app_movie_edit', ['id' => $id]);
        }

        return $this->render('form/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function createAction(Request $request): Response
    {
        $form = $this->createForm(MovieType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->movieClient->add($data);
        }

        return $this->render('form/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}