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

    #[Route('/{book_id}/new', name: 'app_comment_new', requirements: ['book_id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(Book $book, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('COMMENT_CREATE');

        $comment = new Comment();
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

    #[Route('/{book_id}/edit/{comment_id}', name: 'app_comment_edit', requirements: ['book_id' => '\d+', 'comment_id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Book $book, int $comment_id, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = $manager->getRepository(Comment::class)->find($comment_id);

        if ($comment === null) {
            throw $this->createNotFoundException('Comment not found');
        }

        $this->denyAccessUnlessGranted('COMMENT_EDIT', $comment);

        $form = $this->createForm(CommentType::class, $comment, [
            'book' => $book,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
            'comment' => $comment,
        ]);
    }

    #[Route('/{book_id}/delete/{comment_id}', name: 'app_comment_delete', requirements: ['book_id' => '\d+', 'comment_id' => '\d+'], methods: ['DELETE'])]
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
