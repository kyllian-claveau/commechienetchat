<?php

namespace App\Controller\dashboard;

use App\Entity\Appointment;
use App\Entity\Note;
use App\Form\CommentsType;
use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    #[Route("/comments", name: "app_dashboard_comments_showclientpage")]
    public function showClientPage(Request $request, AppointmentRepository $appointmentRepository, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        /** @var Appointment $appointment */
        $appointment = $entityManager->createQuery(
            'SELECT a FROM App\Entity\Appointment a 
     WHERE a.user = :user AND a.appointmentDateTime < :appointmentDateTime
     ORDER BY a.appointmentDateTime DESC'
        )->setMaxResults(1)
            ->setParameter('user', $user)
            ->setParameter('appointmentDateTime', new \DateTime())
            ->getOneOrNullResult();

        // Si aucun rendez-vous passé, on redirige vers la page d'accueil
        if (!$appointment) {
            return $this->redirectToRoute('app_dashboard');
        }
        $comment = new Note();
        $form = $this->createForm(CommentsType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment -> setAppointment($appointment);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/comments.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment,
            'reviews' => $comment
        ]);
    }

}
