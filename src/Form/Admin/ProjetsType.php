<?php

namespace App\Form\Admin;

use App\Entity\Users;
use App\Entity\Projets;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class ProjetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Nom du projet',
                'trim' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez préciser un titre'
                    ]),
                    new Length([
                        'min' => 20,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',
                        'max' => 180,
                        'maxMessage' => 'Trop long, maximum {{ limit }} caractères'

                    ]),
                                    ]
            ])
            ->add('besoinClient', CKEditorType::class, [
                'row_attr' => [
                    'data-live-ignore' => 'true'
                ],
                'label' => 'Besoin du client',
                'config_name' => 'default',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez préciser les besoins'
                    ]),
                    new Length([
                        'min' => 50,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',

                    ]),
                ],
            ])
            ->add('propositionCommercial', FileType::class, [
                'attr' => [
                    'is' => 'drop-files',
                    'label' => 'Déposez la proposition commerciale ici.',
                    'help' => 'Seul les fichiers pdf sont accepté',
                ],
                'label' => false,
                'multiple' => false,
                'data_class' => null,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'Seul les fichiers PDF sont acceptés'
                    ])
                ]
            ])
            ->add('cahierCharge', FileType::class, [
                'attr' => [
                    'is' => 'drop-files',
                    'label' => 'Déposez le cahier des charges ici.',
                    'help' => 'Seul les fichiers pdf sont accepté',
                ],
                'label' => false,
                'multiple' => false,
                'data_class' => null,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'Seul les fichiers PDF sont acceptés'
                    ])
                ]
            ])
            ->add('client', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'email',
                'label' => 'Choisir le client',
                'placeholder' => 'Sélectionnez une option...',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un client'
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projets::class,
        ]);
    }
}
