<?php

namespace App\Form\dashboardVendor;

use App\Entity\Vendor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpenCloseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('openingTime', TimeType::class, [
                'label' => 'Heure d\'ouverture'
            ])
            ->add('closingTime', TimeType::class, [
                'label' => 'Heure de fermeture'
            ])
            ->add('appointment_duration', ChoiceType::class, [
                'label' => 'Durée des rendez-vous',
                'choices' => [
                    '15 minutes' => '00:15:00',
                    '30 minutes' => '00:30:00',
                    '45 minutes' => '00:45:00',
                    '1 heure' => '01:00:00',
                    '1 heure 30' => '01:30:00',
                    '2 heures' => '02:00:00',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vendor::class,
        ]);
    }
}