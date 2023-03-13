<?php

namespace App\Form\Admin;

use App\Entity\Devis;
use App\Form\Admin\ServicesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\Autocomplete\ProjetsAutocompleteField;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('projet', ProjetsAutocompleteField::class, [
            'placeholder' => 'Sélectionnez une option...',
        ])
        ->add('services', LiveCollectionType::class, [
            'entry_type' => ServicesType::class,
            'constraints' => [
                new NotBlank(['message' => 'veuillez donner des infos'])
            ]
        ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
