<?php

namespace App\Form\Blog\Admin;

use App\Entity\Articles;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

class ArticlesType extends AbstractType
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
                ],
                'constraints' => [
                    new NotNull(['message' => 'Veillez donner un titre']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',
                        'max' => 180,
                        'maxMessage' => 'Trop long, maximum {{ limit }} caractères'
    
                    ]),
                ],
                'trim' => false,
            ])
            ->add('introduction', TextareaType::class, [
                'label' => 'Introduction',
                'row_attr' => [
                    'data-live-ignore' => 'true'
                ],
                'constraints' => [
                    new NotNull(['message' => 'Veuillez détailler votre intro']),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',
                    ]),
                ],
                'attr' => [
                    'class' => 'editor'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'row_attr' => [
                    'data-live-ignore' => 'true'
                ],
                'constraints' => [
                    new NotNull(['message' => 'Veuillez détailler votre article']),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',
                    ]),
                ],
                'attr' => [
                    'class' => 'editor'
                ]
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
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Seul les images sont acceptés'
                    ])
                ]
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
