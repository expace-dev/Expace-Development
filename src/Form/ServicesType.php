<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ServicesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'label' => 'Type d\'évènement',
                'constraints' => [
                    new NotBlank(['message' => "Veuillez préciser le type d'évènement"]),
                ]
            ])
            ->add('quantite', TextType::class, [
                'label' => 'Quantité',
                'constraints' => [
                    new NotBlank(['message' => "Veuillez préciser la quantité"]),
                ]
                
            ])
            ->add('tarif', TextType::class, [
                'label' => 'Prix unitaire',
                'constraints' => [
                    new NotBlank(['message' => "Veuillez préciser le prix unitaire"]),
                ]
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
