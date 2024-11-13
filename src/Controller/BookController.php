<?php

namespace App\Controller;

//use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use App\Entity\Book;
//use App\Repository\CommentRepository;
use App\Adapter\GoogleBooksAdapter;
use Google_Client;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/index/{query}', name: 'app_book_index', methods: ['GET'])]
    #[Route('/{id}', name: 'app_book_show', methods: ['GET'])]
    public function books(Request $request, ?string $query, ?string $id): Response
    {
        $client = new Google_Client();
        $client->setApplicationName('Biblios');
        $client->setDeveloperKey($_ENV['GOOGLE_BOOKS_API_KEY']);
        $service = new \Google_Service_Books($client);

        // Define options for the Google Books API query
        $optParams = [
            "printType" => "books"
        ];

        if ($id !== null) {
            $optParams = [];
            $book = $service->volumes->get($id, $optParams);

            return $this->render('book/show.html.twig', [
                'book' => $book
            ]);
        }

        if ($query !== null) {
            $adapter = new GoogleBooksAdapter($service, $query, $optParams);
            $pagerfanta = new Pagerfanta($adapter);

            $currentPage = max((int)$request->get('page', 1), 1);
            $pagerfanta->setCurrentPage($currentPage);
            $pagerfanta->setMaxPerPage(10);

            return $this->render('book/search.html.twig', [
                'books' => $pagerfanta,
            ]);
        }

        return $this->redirectToRoute('app_main_index');
    }

//    #[Route('/{id}', name: 'app_book_show', methods: ['GET'])]
//    public function show(string $id): Response
//    {
//        $client = new Google_Client();
//        $client->setApplicationName('Biblios');
//        $client->setDeveloperKey($_ENV['GOOGLE_BOOKS_API_KEY']);
//        $service = new \Google_Service_Books($client);
//
//        // Define options for the Google Books API query
//        $optParams = [
//            "printType" => "books"
//        ];
//        $book = $service->volumes->get($id, $optParams);
//
//        return $this->render('book/show.html.twig', [
//            'book' => $book
//        ]);
//    }

//    #[Route('/index', name: 'app_book_index', methods: ['GET'])]
//    public function index(BookRepository $repository, Request $request): Response
//    {
//        $books = Pagerfanta::createForCurrentPageWithMaxPerPage(
//            new QueryAdapter($repository->createQueryBuilder('b')),
//            $request->query->get('page', 1),
//            20
//        );
//
//        return $this->render('book/index.html.twig', [
//            'books' => $books,
//        ]);
//    }

//    #[Route('/{id}', name: 'app_book_show', requirements: ['id' => '\d+'], methods: ['GET'])]
//    public function show(Book $book, CommentRepository $commentRepository): Response
//    {
//        $comments = $commentRepository->findByBook($book)
//            ->getQuery()
//            ->getResult();
//
//        return $this->render('book/show.html.twig', [
//            'book' => $book,
//            'comments' => $comments,
//        ]);
//    }
}
