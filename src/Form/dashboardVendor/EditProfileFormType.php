<?php

namespace App\Form\dashboardVendor;

use App\Entity\Vendor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatarFile',FileType::class,options: [
                'required' => false
            ])
            ->add('name')
            ->add('email')
            ->add('about', options: [
                'required' => false
            ])
            ->add('company', options: [
                'required' => false
            ])
            ->add('job', options: [
                'required' => false
            ])
            ->add('country', options: [
                'required' => false
            ])
            ->add('address',TextType::class, options: [
                'attr' => ['id' => 'address'],
                'required' => false
            ])
            ->add('zipcode', TextType::class, options:[
                'attr' => ['id' => 'zipcode'],
                'label' => 'Code postal',
                'required' => false
            ])
            ->add('city', TextType::class, options:[
                'attr' => ['id' => 'city'],
                'label' => 'Ville',
                'required' => false
            ])
            ->add('phone', options: [
                'required' => false
            ])
            ->add('twitter', options: [
                'required' => false
            ])
            ->add('facebook', options: [
                'required' => false
            ])
            ->add('linkedin', options: [
                'required' => false
            ])
            ->add('instagram', options: [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vendor::class,
        ]);
    }
}
