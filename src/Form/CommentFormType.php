<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'label' => 'Note',
                'choices' => [
                    '⭐ 1' => 1,
                    '⭐⭐ 2' => 2,
                    '⭐⭐⭐ 3' => 3,
                    '⭐⭐⭐⭐ 4' => 4,
                    '⭐⭐⭐⭐⭐ 5' => 5,
                ],
                'expanded' => true,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => ['rows' => 4, 'placeholder' => 'Partagez votre avis sur ce livre...'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
