<?php

namespace App\Controller;

use App\Elasticsearch\Movie\MovieClient;
use App\Form\MovieType;
use App\Transformer\Movie\MovieResponseTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/movie', name: 'app_movie_')]
class MovieController extends AbstractController
{

    public function __construct(
        private readonly MovieClient              $movieClient,
        private readonly MovieResponseTransformer $movieResponseTransformer,
    )
    {
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function listAction(
        #[MapQueryParameter]
        ?int $page = 1,
    ): Response
    {
        $item = $this->movieClient->search(page: $page);

        $item = ($this->movieResponseTransformer)($item);

        return $this->render('movie/list.html.twig', [
            'items' => $item,
        ]);
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

        return $this->render('movie/edit.html.twig', [
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

        return $this->render('movie/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}