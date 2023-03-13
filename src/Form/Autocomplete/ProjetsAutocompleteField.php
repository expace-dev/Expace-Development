<?php

namespace App\Form\Autocomplete;

use App\Entity\Projets;
use App\Repository\ProjetsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class ProjetsAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Projets::class,
            'placeholder' => 'Sélectionner une option',
            'choice_label' => 'titre',
            'constraints' => [
                new NotNull(['message' => 'Veuillez sélectionner un projet'])
            ],
            'label' => 'Choisir le projet',
            'placeholder' => 'Sélectionnez une option...',

            'query_builder' => function(ProjetsRepository $projetsRepository) {
                return $projetsRepository->createQueryBuilder('projets');
            },
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
