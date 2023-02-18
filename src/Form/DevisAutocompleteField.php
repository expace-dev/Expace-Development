<?php

namespace App\Form;

use App\Entity\Devis;
use App\Repository\DevisRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class DevisAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Devis::class,
            'placeholder' => 'Choose a Devis',
            //'choice_label' => 'name',

            'query_builder' => function(DevisRepository $devisRepository) {
                return $devisRepository->createQueryBuilder('devis');
            },
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
