<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;']
            ])
            ->add('firstname', TextType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;']
            ])
            ->add('username', TextType::class, [
                'required' => false,
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;']
            ]) 
            ->add('email', EmailType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;']
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur-rice' => 'ROLE_USER',
                    'Administrateur-rice' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 
                'style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
