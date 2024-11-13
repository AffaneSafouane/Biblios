<?php

namespace App\Controller\Admin;

//use App\Form\AuthorType;
//use App\Repository\AuthorRepository;
//use Doctrine\ORM\EntityManagerInterface;
//use Pagerfanta\Doctrine\ORM\QueryAdapter;
//use Pagerfanta\Pagerfanta;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Adapter\GoogleBooksAdapter;
use App\Entity\Author;
use Google_Client;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/author')]
class AuthorController extends AbstractController
{
//    #[Route('', name: 'app_admin_author_index')]
//    public function index(AuthorRepository $repository, Request $request): Response
//    {
//        $dates = [];
//        if ($request->query->has('start')) {
//            $dates['start'] = $request->query->get('start');
//        }
//
//        if ($request->query->has('end')) {
//            $dates['end'] = $request->query->get('end');
//        }
//
//        $authors = Pagerfanta::createForCurrentPageWithMaxPerPage(
//            new QueryAdapter($repository->findByDateOfBirth()),
//            $request->query->get('page', 1),
//            10
//        );
//
//        return $this->render('admin/author/index.html.twig', [
//            'authors' => $authors,
//        ]);
//    }
//
//    #[IsGranted("ROLE_AJOUT_DE_LIVRE")]
//    #[Route('/new', name: 'app_admin_author_new', methods: ['GET', 'POST'])]
//    #[Route('/{id}/edit', name: 'app_admin_author_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
//    public function new(?Author $author, Request $request, EntityManagerInterface $manager): Response
//    {
//        if ($author) {
//            $this->denyAccessUnlessGranted("EDIT", $author);
//        }
//
//        if ($author === null) {
//            $this->denyAccessUnlessGranted("CREATE", $author);
//        }
//
//        $author ??= new Author();
//        $form = $this->createForm(AuthorType::class, $author);
//
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $manager->persist($author);
//            $manager->flush();
//
//            return $this->redirectToRoute('app_admin_author_show', ['id' => $author->getId()]);
//        }
//
//        return $this->render('admin/author/new.html.twig', [
//            'form' => $form,
//        ]);
//    }
//
//    #[IsGranted('ROLE_EDITION_DE_LIVRE')]
//    #[Route('/{id}/delete', name: "app_admin_author_delete", requirements: ['id' => '\d+'], methods: ['DELETE'])]
//    public function delete(Author $author, Request $request, EntityManagerInterface $manager): Response
//    {
//        $this->denyAccessUnlessGranted("DELETE", $author);
//
//        /** @var string|null $token */
//        $token = $request->getPayload()->get('token');
//
//        if ($this->isCsrfTokenValid('delete', $token)) {
//            $manager->remove($author);
//            $manager->flush();
//        }
//
//        return $this->redirectToRoute('app_admin_author_index');
//    }
//
//    #[Route('/{id}', name: 'app_admin_author_show', requirements: ['id' => '\d+'] , methods: ['GET'])]
//    public function show(?Author $author): Response
//    {
//        return $this->render('admin/author/show.html.twig', [
//            'author' => $author,
//        ]);
//    }
}
