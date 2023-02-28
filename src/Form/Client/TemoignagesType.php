<?php

namespace App\Form\Client;

use App\Entity\Temoignages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TemoignagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('description', TextareaType::class, [
            'label' => 'Votre témoignage',
            'attr' => [
                'rows' => 5
            ],
            'constraints' => [
                new NotBlank(['message' => 'Veuillez renseigner ce champs']),
                new Length([
                    'max' => 255,
                    'maxMessage' => 'Trop long, maximum {{ limit }} caractères',

                ]),
            ]
        ])
        ->add('note', RangeType::class, [
            'label' => 'Votre note sur 5',
            'attr' => [
                'min' => '0',
                'max' => '5',
                'step' => '0.5',
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Temoignages::class,
        ]);
    }
}
