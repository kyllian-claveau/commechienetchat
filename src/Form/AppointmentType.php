<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Pet;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Security;

class AppointmentType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        $builder
            ->add('pet', EntityType::class, [
                'class' => Pet::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('p')
                        ->where('p.user = :user')
                        ->setParameter('user', $user);
                }
            ])
            ->add('appointmentDateTime', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy hh:mm',
                'html5' => false,
                'label' => 'Date',
                'required' => true
            ])
            ->add('description')
            ->add('phone', options: [
                'required' => false
            ])
            ->add('email');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
            'vendor' => null,
            'user' => null,
        ]);
    }
}