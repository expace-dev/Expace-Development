<?php

namespace App\Form;

use App\Entity\Portfolios;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\All;

class PortfoliosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', UsersAutocompleteField::class, [
                'placeholder' => 'Sélectionnez une option...',
            ])
            ->add('url',UrlType::class, [
                'label' => 'Url du projet',
            ])
            ->add('details', CKEditorType::class, [
                'label' => 'Description',
            ])
            
            ->add('imagesPortfolios', FileType::class, [
                'attr' => [
                    'is' => 'drop-files',
                    'label' => 'Déposez vos images ou cliquez pour ajouter.',
                    'help' => 'Seul les fichiers jpg jpeg et png sont accepté',
                ],
                'label' => false,
                'multiple' => true,
                'data_class' => null,
                'mapped' => false,
                'constraints' => [
                    new All([
                        new File([
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png'
                            ],
                            'mimeTypesMessage' => 'Seul les images sont acceptés'
                        ])
                    ])
                    
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Portfolios::class,
        ]);
    }
}
