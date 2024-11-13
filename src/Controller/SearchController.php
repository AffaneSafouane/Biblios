<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/search')]
class SearchController extends AbstractController
{
    #[Route('/title', name: 'app_search_title', methods: ['POST'])]
    public function search(): Response
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
            $query = "intitle:".str_replace(" ", "+", $_POST['query']);
            return $this->redirectToRoute('app_book_index', ['query' => $query]);
        } else {
            throw new InvalidArgumentException("Veuillez effectuer une recherche.");
        }
    }

    #[Route('/author/{query}', name: 'app_search_author', methods: ['GET'])]
    public function searchAuthor(string $query): Response
    {
        $query = "inauthor:".str_replace(" ", "+", $query);
        return $this->redirectToRoute('app_book_index', ['query' => $query]);
    }

    #[Route('/editor/{query}', name: 'app_search_editor', methods: ['GET'])]
    public function searchEditor(string $query): Response
    {
        $query = "inpublisher:".str_replace(" ", "+", $query);
        return $this->redirectToRoute('app_book_index', ['query' => $query]);
    }
}
