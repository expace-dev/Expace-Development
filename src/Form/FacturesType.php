<?php

namespace App\Form;

use App\Entity\Factures;
use App\Form\ServicesType;
use App\Form\ProjetsAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class FacturesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('projet', ProjetsAutocompleteField::class, [
                'placeholder' => 'Sélectionnez une option...',
            ])
            ->add('services', LiveCollectionType::class, [
                'entry_type' => ServicesType::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Factures::class,
        ]);
    }
}
