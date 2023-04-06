<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Vendor;
use App\Form\AppointmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AppointmentController extends AbstractController
{
    #[Route("/appointment/vendor/{id}/new", name: 'appointment_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, Vendor $vendor, Security $security): Response
    {
        $appointment = new Appointment();
        $user = $security->getUser();
        $appointment->setUser($user);
        $appointment->setVendor($vendor);
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($appointment);
            $entityManager->flush();
        }

        return $this->render('appointment/new.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView(),
            'vendor' => $vendor
        ]);
    }
}