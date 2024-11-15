<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use App\Form\EditorType;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/editor')]
class EditorController extends AbstractController
{
    #[Route('', name: 'app_admin_editor')]
    public function index(Request $request, EditorRepository $repository): Response
    {
        $editors = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->createQueryBuilder('e')),
            $request->query->get('page', 1),
            10
        );

        return $this->render('admin/editor/index.html.twig', [
            'editors' => $editors,
        ]);
    }

    #[IsGranted("ROLE_AJOUT_DE_LIVRE")]
    #[Route('/new', name: 'app_admin_editor_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_editor_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]    
    public function new(?Editor $editor, Request $request, EntityManagerInterface $manager): Response
    {
        if ($editor) {
            $this->denyAccessUnlessGranted("EDIT", $editor);
        }

        if ($editor === null) {
            $this->denyAccessUnlessGranted("CREATE", $editor);
        }

        $editor ??= new Editor();
        $form = $this->createForm(EditorType::class, $editor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($editor);
            $manager->flush();

            return $this->redirectToRoute('app_admin_editor_show', ['id' => $editor->getId()]);
        }
        
        return $this->render('admin/editor/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted("ROLE_EDITION_DE_LIVRE")]
    #[Route('/{id}/delete', name: "app_admin_editor_delete", methods: ['DELETE'])]
    public function delete(Editor $editor, EntityManagerInterface $manager, Request $request): Response
    {
        $this->denyAccessUnlessGranted("DELETE", $editor);

        /** @var string|null $token */
        $token = $request->getPayload()->get('token');

        if ($this->isCsrfTokenValid('delete', $token)) {
            $manager->remove($editor);
            $manager->flush();
        }

        return $this->redirectToRoute('app_admin_book');
    }

    #[Route('/{id}', name: 'app_admin_editor_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show (Editor $editor): Response
    {
        return $this->render('admin/editor/show.html.twig', [
            'editor' => $editor,
        ]);
    }
}
