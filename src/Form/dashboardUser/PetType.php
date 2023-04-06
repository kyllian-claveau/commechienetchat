<?php

namespace App\Form\dashboardUser;

use App\Entity\Pet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('avatarPetFile',FileType::class,options: [
                'required' => false
            ])
            ->add('birthdayDate',BirthdayType::class)
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Chat' => Pet::CAT_TYPE,
                    'Chien' =>PET::DOG_TYPE,
                ],
            ])
            ->add('breed')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pet::class,
        ]);
    }
}