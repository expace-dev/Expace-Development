<?php

namespace App\Form\Blog\Admin;

use App\Entity\Articles;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotNull;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez une catégorie',
                'label' => 'Catégorie',
                'constraints' => [
                    new NotNull(['message' => 'Choisissez une catégorie'])
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre de l\'article'
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Contenu',
                'config_name' => 'main_config',
            ])
            ->add('img', FileType::class, [
                'attr' => [
                    'is' => 'drop-files',
                    'label' => 'Déposez votre photo ou cliquez pour ajouter.',
                    'help' => 'Seul les fichiers jpg jpeg et png sont accepté',
                ],
                'label' => false,
                'multiple' => false,
                'data_class' => null,
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
