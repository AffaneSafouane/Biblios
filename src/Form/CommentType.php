<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Comment;
use App\Enum\CommentStatus;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextAreaType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
            ])
            ->add('review', FloatType::class, [
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
            ])
            ->add('status', EnumType::class, [
                'class' => CommentStatus::class,
                'attr' => ['style' => 'background-color: #1b1e1f; color: white; border: 1px solid gray;'],
                'choice_label' => function (CommentStatus $status) {
                    return $status->getLabel();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);

        $resolver->setRequired('book');
        $resolver->setAllowedTypes('book', Book::class);
    }
}
