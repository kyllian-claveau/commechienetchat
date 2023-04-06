<?php

namespace App\Controller\dashboard;

use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppointmentController extends AbstractController
{
    #[Route("/appointments", name: "app_appointment")]
    public function afficherAgendaUtilisateur(EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $appointments = $entityManager->getRepository(Appointment::class)->findBy(['user' => $user]);

        return $this->render('dashboard/appointment.html.twig', [
            'appointments' => $appointments
        ]);
    }
}