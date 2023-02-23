<?php

namespace App\Form;

use App\Entity\Activites;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Time;

class ActivitesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'Date',
                'data' => new DateTime()
            ])
            ->add('debut', TimeType::class, [
                'label' => 'Heure de début',
                'minutes' => [0, 15, 30, 45],
                'data' => new DateTime()
            ])
            ->add('fin', TimeType::class, [
                'label' => 'Heure de fin',
                'minutes' => [0, 15, 30, 45],
                'data' => new DateTime()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activites::class,
        ]);
    }
}
