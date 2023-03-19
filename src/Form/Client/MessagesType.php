<?php

namespace App\Form\Client;

use App\Entity\Messages;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MessagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'trim' => false,
                'constraints' => [
                    new NotNull(['message' => 'Veuillez préciser le sujet']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Trop long, maximum {{ limit }} caractères'

                    ]),
                ]
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
                'label' => 'Message',
                'trim' => false,
                'constraints' => [
                    new NotNull(['message' => 'Veuillez rédiger votre message']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',
                    ]),
                ],
                'row_attr' => [
                    'data-live-ignore' => 'true'
                ],
            ])
            ->add('docsMessages', FileType::class, [
                'attr' => [
                    'is' => 'drop-files',
                    'label' => 'Déposez vos fichiers ou cliquez pour ajouter.',
                    'help' => 'Seul les fichiers jpg jpeg png et pdf sont accepté',
                ],
                'label' => false,
                'required' => false,
                'multiple' => true,
                'data_class' => null,
                'mapped' => false,
                'constraints' => [
                    new All([
                        new File([
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'application/pdf'
                            ],
                            'mimeTypesMessage' => 'Seul les images et pdf sont acceptés'
                        ])
                    ])
                    
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messages::class,
        ]);
    }
}
