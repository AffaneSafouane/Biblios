<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\Voter\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use PHPUnit\Util\Exception;
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

#[Route('admin/user')]
class RegistrationController extends AbstractController
{
    public function __construct(private readonly EmailVerifier $emailVerifier)
    {
    }

    #[Route('/', name: 'app_admin_user_index', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function index(UserRepository $repository, Request $request): Response
    {
        $this->denyAccessUnlessGranted("USER_LIST", $this->getUser());

        $users = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->createQueryBuilder('u')),
            $request->query->get('page', 1),
            20
        );

        return $this->render('/admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[IsGranted(UserVoter::CREATE)]
    #[Route('/register', name: 'app_admin_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setIsVerified(false);

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

        return $this->render('admin/user/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_admin_user_show', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted("USER_VIEW", $this->getUser());

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
