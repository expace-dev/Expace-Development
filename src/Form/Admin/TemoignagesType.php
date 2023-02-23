<?php

namespace App\Form\Admin;

use App\Entity\Temoignages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemoignagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Votre témoignage',
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('actif')
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
