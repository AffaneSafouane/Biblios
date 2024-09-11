<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;']
            ])
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
                'input' => 'datetime_immutable',
            ])
            ->add('dateOfDeath', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
                'input' => 'datetime_immutable',
                'required' => false,
            ])
            ->add('nationality', TextType::class, [
                'required' => false,
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
            ])
            ->add('books', EntityType::class, [
                'class' => Book::class,
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
            ])
            ->add('certification', CheckboxType::class, [
                'mapped' => false,
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
                'label' => "Je certifie l'exactitude des informations fournies",
                'constraints' => [
                    new Assert\IsTrue(message: "Vous devez cocher la case pour soumettre le formulaire.")
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
