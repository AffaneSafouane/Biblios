<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/{id}/index', name: 'app_comment_index', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function index(?Book $book, CommentRepository $repository, Request $request): Response
    {
        if ($book === null) {
            throw $this->createNotFoundException('Book not found');
        }

        $qb = $repository->findByBook($book);

        $comments = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($qb),
            $request->query->get('page', 1),
            20
        );

        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
            'book' => $book,
        ]);
    }

    #[Route('/{id}/new', name: 'app_comment_new', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_comment_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(Book $book, ?Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        if ($comment) {
            $this->denyAccessUnlessGranted('COMMENT_EDIT', $comment);
        }

        if ($comment === null) {
            $this->denyAccessUnlessGranted('COMMENT_CREATE', $comment);
        }

        $comment ??= new Comment();
        $form = $this->createForm(CommentType::class, $comment, [
            'book' => $book,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setBook($book);
            $comment->setCommentedBy($this->getUser());

            if ($comment->getStatus() === "published") {
                $comment->setPublishedAt(new \DateTimeImmutable());
            } else {
                $comment->setPublishedAt(null);
            }

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('app_comment_index', ['id' => $book->getId()]);
        }

        return $this->render('comment/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_comment_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted("COMMENT_DELETE", $comment);
        $token = $request->getPayload()->get('token');

        if ($this->isCsrfTokenValid('delete', $token)) {
            $manager->remove($comment);
            $manager->flush();
        }

        return $this->redirectToRoute('app_comment_index', ['id' => $comment->getBook()->getId()]);
    }
}
