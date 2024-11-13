<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/book')]
class BookController extends AbstractController
{
    #[Route('', name: 'app_admin_book')]
    public function index(BookRepository $repository, Request $request): Response
    {
        $books = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->createQueryBuilder('b')),
            $request->query->get('page', 1),
            20
        );

        return $this->render('admin/book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[IsGranted("ROLE_AJOUT_DE_LIVRE")]
    #[Route('/new', name: 'app_admin_book_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_book_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Book $book, Request $request, EntityManagerInterface $manager): Response
    {
        if ($book) {
            $this->denyAccessUnlessGranted("EDIT", $book);
        }

        if ($book === null) {
            $this->denyAccessUnlessGranted("CREATE", $book);
        }

        $book ??= new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            if (!$book->getId() && $user instanceof User) {
                $book->setCreatedBy($user);
            }

            $manager->persist($book);
            $manager->flush();

            return $this->redirectToRoute('app_admin_book_show', ['id' => $book->getId()]);
        }
        
        return $this->render('admin/book/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted("ROLE_EDITION_DE_LIVRE")]
    #[Route('/{id}/delete', name: "app_admin_book_delete", methods: ['DELETE'])]
    public function delete(Book $book, EntityManagerInterface $manager, Request $request): Response
    {
        $this->denyAccessUnlessGranted("DELETE", $book);

        /** @var string|null $token */
        $token = $request->getPayload()->get('token');

        if ($this->isCsrfTokenValid('delete', $token)) {
            $manager->remove($book);
            $manager->flush();
        }

        return $this->redirectToRoute('app_admin_book');
    }

    #[IsGranted("ROLE_MODERATEUR")]
    #[Route('/{id}', name: 'app_admin_book_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Book $book, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findByBook($book)
                                    ->getQuery()
                                    ->getResult();

        return $this->render('admin/book/show.html.twig', [
            'book' => $book,
            'comments' => $comments,
        ]);
    }
}
