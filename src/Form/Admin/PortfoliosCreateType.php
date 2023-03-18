<?php

namespace App\Form\Admin;

use App\Entity\Portfolios;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use App\Form\Autocomplete\ProjetsAutocompleteField;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PortfoliosCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('projet', ProjetsAutocompleteField::class)
            ->add('url',UrlType::class, [
                'label' => 'Url du projet',
            ])
            ->add('details', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'rows' => 5
                ]
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
