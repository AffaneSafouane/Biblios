<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
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
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'first_options' => [
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
                    'label' => '* Mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'
                    ],
                ],
                'second_options' => [
                    'label' => '* Confirmez le mot de passe',
                    'attr' => [
                        'autocomplete' => 'same-password',
                        'style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'mapped' => false,
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
