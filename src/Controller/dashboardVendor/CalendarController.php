<?php

namespace App\Controller\dashboardVendor;

use App\Entity\Appointment;
use App\Entity\Vendor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    #[Route("/vendor/calendar", name: "app_vendor_calendar")]
    public function afficherAgendaVendeur(Request $request)
    {
        return $this->render('dashboardVendor/calendar.html.twig');
    }

    #[Route("/vendor/appointment/confirmation", name: "appointment_confirmation")]
    public function getAppointments(Request $request, EntityManagerInterface $entityManager, Vendor $vendor)
    {
        $openingTime = $vendor->getOpeningTime();
        $closingTime = $vendor->getClosingTime();
        $start = $request->query->get('start');
        $end = $request->query->get('end');

        $appointments = $entityManager->getRepository(Appointment::class)
            ->createQueryBuilder('a')
            ->andWhere('a.appointmentDateTime >= :start')
            ->andWhere('a.appointmentDateTime <= :end')
            ->andWhere('a.vendor = :vendor')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('vendor', $this->getUser())
            ->getQuery()
            ->getResult();

        $data = [];

        foreach ($appointments as $appointment) {
            $data[] = [
                'id' => $appointment->getId(),
                'title' => $appointment->getPet()->getName(),
                'start' => $appointment->getAppointmentDateTime()->format(\DateTime::ATOM),
                'end' => $appointment->getAppointmentDateTime()->add(new \DateInterval('PT30M'))->format(\DateTime::ATOM),
                'vendor' => $vendor
            ];
        }

        return new JsonResponse($data);
    }
}