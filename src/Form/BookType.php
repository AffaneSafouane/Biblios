<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Enum\BookStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;']
            ])
            ->add('isbn', TextType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;']
            ])
            ->add('cover', UrlType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;']
            ])
            ->add('editedAt', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
                'input' => 'datetime_immutable',
            ])
            ->add('plot', TextareaType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;']
            ])
            ->add('pageNumber', IntegerType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;']
            ])
            ->add('status', EnumType::class, [
                'class' => BookStatus::class,
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
                'choice_label' => function (BookStatus $status) {
                    return $status->getLabel();
                }
            ])
            ->add('editor', EntityType::class, [
                'class' => Editor::class,
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
                'choice_label' => 'name',
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
                'choice_label' => 'name',
                'multiple' => true,
                'by_reference' => false,
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
            'data_class' => Book::class,
        ]);
    }
}
