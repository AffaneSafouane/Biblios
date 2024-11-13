<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\EmailVerifier;
use App\Security\Voter\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Exception\AssetNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use function Webmozart\Assert\Tests\StaticAnalysis\null;

#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(private readonly EmailVerifier $emailVerifier)
    {
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_user_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?User $user, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($user) {
            $this->denyAccessUnlessGranted("EDIT", $user);
        }

        $user ??= new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(["ROLE_USER"]);
            if ($user->getIsVerified() === null || $user->getIsVerified() === false) {
                $user->setIsVerified(false);
            } else {
                $user->setIsVerified(true);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('saf@demo.com'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre Email')
                    ->htmlTemplate('admin/user/confirmation_email.html.twig')
            );

            return $this->redirectToRoute('app_main_index');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, ?TokenInterface $token): Response
    {
        $this->redirectToRoute("app_login");
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $token->getUser();

        if (!$user instanceof User) {
            throw new AssetNotFoundException("User not found");
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
    }

    #[Route('/{id}/show', name: 'app_user_show', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted("USER_VIEW", $user);

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
