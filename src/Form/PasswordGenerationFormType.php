<?php
// src/Form/PasswordGenerationFormType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordGenerationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nb_mots', ChoiceType::class, [
                'label' => 'Nombre de mots dans le mot de passe',
                'choices' => [
                    '2 mots' => 2,
                    '3 mots' => 3,
                    '4 mots' => 4,
                    '5 mots' => 5,
                    '6 mots' => 6,
                    '7 mots' => 7,
                ],
                'constraints' => [
                    new Assert\Choice([
                        'choices' => [2, 3, 4, 5, 6, 7],
                        'message' => 'Le nombre de mots sélectionné est invalide.',
                    ]),
                ],
            ])
            ->add('longueur_minimale', IntegerType::class, [
                'label' => 'Longueur minimale du mot de passe',
                'attr' => ['min' => 8, 'max' => 50],
                'constraints' => [
                    new Assert\Range([
                        'min' => 8,
                        'max' => 50,
                        'notInRangeMessage' => 'La longueur minimale doit être comprise entre {{ min }} et {{ max }} caractères.',
                    ]),
                    new Assert\Type([
                        'type' => 'integer',
                        'message' => 'La longueur minimale doit être un nombre entier.',
                    ]),
                ],
            ])
            ->add('separateur', ChoiceType::class, [
                'label' => 'Séparateur entre les mots',
                'choices' => [
                    'Aléatoire' => 'random',
                    'Tiret (-)' => '-',
                    'Underscore (_)' => '_',
                    'Espace ( )' => ' ',
                    'Etoile (*)' => '*',
                    'Slash (/)' => '/',
                    'Plus (+)' => '+',
                ],
                'constraints' => [
                    new Assert\Choice([
                        'choices' => ['random', '-', '_', ' ', '*', '/', '+'],
                        'message' => 'Sélectionnez un séparateur valide.',
                    ]),
                ],
            ])
            ->add('majuscule_debut', CheckboxType::class, [
                'label' => 'Majuscule au début de chaque mot',
                'required' => false,
                'constraints' => [
                    new Assert\Type([
                        'type' => 'bool',
                        'message' => 'La valeur de majuscule au début doit être un booléen.',
                    ]),
                ],
            ])
            ->add('majuscule_aleatoire', CheckboxType::class, [
                'label' => 'Majuscule à un endroit aléatoire dans chaque mot',
                'required' => false,
                'constraints' => [
                    new Assert\Type([
                        'type' => 'bool',
                        'message' => 'La valeur de majuscule aléatoire doit être un booléen.',
                    ]),
                ],
            ])
            ->add('longueur_nombre', ChoiceType::class, [
                'label' => 'Nombre de chiffres à la fin du mot de passe',
                'choices' => [
                    '0 chiffre' => 0,
                    '1 chiffre' => 1,
                    '2 chiffres' => 2,
                    '3 chiffres' => 3,
                    '4 chiffres' => 4,
                    '5 chiffres' => 5,
                ],
                'constraints' => [
                    new Assert\Choice([
                        'choices' => [0, 1, 2, 3, 4, 5],
                        'message' => 'Le nombre de chiffres est invalide.',
                    ]),
                ],
            ])
            ->add('caractere_special', ChoiceType::class, [
                'label' => 'Caractère spécial à la fin',
                'choices' => [
                    'Aléatoire' => 'random',
                    'Aucun' => 'none',
                    'Dollar ($)' => '$',
                    'Point d\'exclamation (!)' => '!',
                    'Croisillon (#)' => '#',
                    'Point d\'interrogation (?)' => '?',
                    'Tiret (-)' => '-',
                    'Plus (+)' => '+',
                    'Arobase (@)' => '@',
                    'Virgule (,)' => ',',
                    'Point virgule (;)' => ';',
                    'Deux Points (:)' => ':',
                    'Etoile (*)' => '*',
                ],
                'constraints' => [
                    new Assert\Choice([
                        'choices' => ['random', 'none', '$', '!', '#', '?', '-', '+', '@', ',', ';', ':', '*'],
                        'message' => 'Sélectionnez un caractère spécial valide.',
                    ]),
                ],
            ])
            ->add('caracteres_accentues', CheckboxType::class, [
                'label'    => 'Autoriser les caractères accentués',
                'required' => false,
                'constraints' => [
                    new Assert\Type([
                        'type' => 'bool',
                        'message' => 'La valeur doit être un booléen.',
                    ]),
                ],
            ])
            ->add('generate', SubmitType::class, ['label' => 'Générer les mots de passe']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
?>