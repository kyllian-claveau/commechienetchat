<?php

namespace App\Controller\dashboard;
use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route("/dashboard", name: "app_dashboard")]
    public function base(EntityManagerInterface $entityManager, Request $request)
    {
        $user = $this->getUser();

        $lastLogin = $user->getLastLogin();
        $ipAddress = $request->server->get('REMOTE_ADDR');

        $appointments = $entityManager->getRepository(Appointment::class)->findBy(['user' => $user]);

// Calculer le nombre de rendez-vous par vétérinaire
        $appointmentsByVet = $entityManager->createQuery(
            'SELECT COUNT(a.id) as nb, v.name 
     FROM App\Entity\Appointment a 
     JOIN a.vendor v 
     WHERE a.user = :user 
     GROUP BY v.id 
     ORDER BY nb DESC'
        )->setParameter('user', $user)
            ->getResult();

// Récupérer le nombre de rendez-vous avec le vétérinaire ayant eu le plus de rendez-vous
        $mostAppointmentsVetCount = null;
        $mostAppointmentsVet = null;
        if (!empty($appointmentsByVet)) {
            $mostAppointmentsVet = $appointmentsByVet[0]['name'];
            $mostAppointmentsVetCount = $appointmentsByVet[0]['nb'];
        }
        $lastCompletedAppointment = $entityManager->createQuery(
            'SELECT a FROM App\Entity\Appointment a 
     WHERE a.user = :user AND a.appointmentDateTime < :appointmentDateTime
     ORDER BY a.appointmentDateTime DESC'
        )->setMaxResults(1)
            ->setParameter('user', $user)
            ->setParameter('appointmentDateTime', new \DateTime())
            ->getOneOrNullResult();

// Récupérer le dernier rendez-vous de l'utilisateur
        $nextAppointment = $entityManager->createQuery(
            'SELECT a FROM App\Entity\Appointment a 
     WHERE a.user = :user AND a.appointmentDateTime > :appointmentDateTime
     ORDER BY a.appointmentDateTime ASC'
        )->setMaxResults(1)
            ->setParameter('user', $user)
            ->setParameter('appointmentDateTime', new \DateTime())
            ->getOneOrNullResult();
        return $this->render("dashboard/index.html.twig", [
            'lastAppointment' => $nextAppointment,
            'lastCompletedAppointment' => $lastCompletedAppointment,
            'mostAppointmentsVet' => $mostAppointmentsVet,
            'mostAppointmentsVetCount' => $mostAppointmentsVetCount,
            'ipAddress' => $ipAddress,
            'lastLogin' => $lastLogin,
        ]);
    }
}