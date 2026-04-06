<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Language;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Titre du livre'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 5, 'placeholder' => 'Description du livre'],
            ])
            ->add('isbn', TextType::class, [
                'label' => 'ISBN',
                'required' => false,
                'attr' => ['placeholder' => '978-X-XXXX-XXXX-X'],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Nombre d\'exemplaires',
                'attr' => ['min' => 0],
            ])
            ->add('publishedAt', DateType::class, [
                'label' => 'Date de publication',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('language', EntityType::class, [
                'class' => Language::class,
                'choice_label' => 'name',
                'label' => 'Langue',
                'required' => false,
                'placeholder' => '-- Sélectionner une langue --',
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'fullName',
                'label' => 'Auteurs',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => ['class' => 'select2'],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégories',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => ['class' => 'select2'],
            ])
            ->add('coverImageFile', VichImageType::class, [
                'label' => 'Image de couverture',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'image_uri' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
