<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('societe', TextType::class, [
                'label' => 'Nom de l’entreprise',
                'required' => false,
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom*',
                'attr' => [
                    'autocomplete' => 'off',
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Entrez un Nom'
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',
                        'max' => 30,
                        'maxMessage' => 'Trop long, maximum {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/^([a-zA-Z]+)$/',
                        'message' => 'Uniquement des lettres'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom*',
                'attr' => [
                    'autocomplete' => 'off',
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Entrez un prénom'
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',
                        'max' => 30,
                        'maxMessage' => 'Trop long, maximum {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/^([a-zA-Z]+)$/',
                        'message' => 'Uniquement des lettres'
                    ])
                ]
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse*',
                'trim' => false,
                'attr' => [
                    'autocomplete' => 'off',
                    'data-live-ignore' => 'true'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Entrez une adresse'
                    ]),
                ]
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal*',
                'attr' => [
                    'autocomplete' => 'off',
                    'data-live-ignore' => 'true',
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Trop court'
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Trop court',
                    ]),
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville*',
                'attr' => [
                    'autocomplete' => 'off',
                    'data-live-ignore' => 'true'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Entrez une ville'
                    ]),
                ]
            ])
            ->add('etat', TextType::class, [
                'required' => false,
                'label' => 'Etat',
                'attr' => [
                    'autocomplete' => 'off',
                    'data-live-ignore' => 'true'
                ],
            ])
            ->add('pays', TextType::class, [
                'label' => 'Pays*',
                'attr' => [
                    'autocomplete' => 'off',
                    'data-live-ignore' => 'true'
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Entrez un Pays'
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone*',
                'attr' => [
                    'autocomplete' => 'off',
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Entrez un téléphone'
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Trop court, minimum {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('mobile', TelType::class, [
                'label' => 'Mobile',
                'attr' => [
                    'autocomplete' => 'off',
                ],
                'required' => false
            ])
            ->add('fax', TelType::class, [
                'label' => 'Fax',
                'attr' => [
                    'autocomplete' => 'off',
                ],
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail*',
                'attr' => [
                    'autocomplete' => 'off',
                ],
                'constraints' => [
                    new Email(['message' => 'Entrez un E-mail valide']),
                    new NotNull(['message' => 'Entrez un E-mail']),
                ]
            ])
            ->add('web', UrlType::class, [
                'label' => 'Adresse Web',
                'attr' => [
                    'autocomplete' => 'off',
                ],
                'required' => 'false',
                'constraints' => [
                    new Url(['message' => 'Entrez une url valide'])
                ],
                'required' => false
            ])
            ->add('username', TextType::class, [
                'label' => 'Nom d’utilisateur*',
                'attr' => [
                    'autocomplete' => 'off',
                ],
                'constraints' => [
                    new NotNull(['message' => 'Entrez un nom d\'utilisateur'])
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'always_empty' => false,
                'label' => 'Mot de passe*',
                'constraints' => [
                    new Regex([
                        'pattern' => '/[a-z]/',
                        'message' => 'Au moins une lettre minuscule'
                    ]),
                    new Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Au moins une lettre majuscule'
                    ]),
                    new Regex([
                        'pattern' => '/[1-9]/',
                        'message' => 'Au moins un chiffre'
                    ]),
                    new Length([
                        'min' => 14,
                        'minMessage' => 'Au moins {{ limit }} caractères',
                    ]),
                    new NotCompromisedPassword(['message' => 'Ce mot de passe est compromis']),
                ],
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => "gen"
                ],
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
