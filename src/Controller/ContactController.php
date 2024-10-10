<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($contact);
            $manager->flush();

            $mail = (new TemplatedEmail())
                ->to('contact@exemple.fr')
                ->from($contact->getEmail())
                ->subject($contact->getObject())
                ->htmlTemplate("emails/contact_email.html.twig")
                ->context(['contact' => $contact])
                ;
            $mailer->send($mail);
            $this->addFlash('success', "Votre email a bien été envoyé");

            return $this->redirectToRoute('app_main_index');
        }

        return $this->render('contact/new.html.twig', [
            "form" => $form,
        ]);
    }
}
