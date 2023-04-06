<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Vendor;
use App\Form\AppointmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SearchVetController extends AbstractController
{
    #[Route("/vet/list/{zipcode}/{id}", name: "vet_list")]
    public function list(Request $request, EntityManagerInterface $entityManager, string $zipcode, Security $security, ?int $id = null)
    {
        $appointment = new Appointment();
        $user = $security->getUser();
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vendor = $entityManager->getRepository(Vendor::class)->find($id);

            // Vérifier s'il y a déjà un rendez-vous à l'horaire sélectionné pour ce vendeur
            $existingAppointment = $entityManager->getRepository(Appointment::class)->findOneBy([
                'vendor' => $vendor,
                'appointmentDateTime' => $appointment->getAppointmentDateTime()
            ]);

            if ($existingAppointment) {
                // Si oui, afficher un message d'erreur
                $this->addFlash('danger', 'Ce créneau horaire est déjà pris pour ce vendeur.');

                // Rediriger vers la même page pour afficher le formulaire et le message d'erreur
                return $this->redirectToRoute('vet_list', ['zipcode' => $zipcode, 'id' => $id]);
            }

            // Sinon, enregistrer le nouveau rendez-vous
            $appointment->setUser($user);
            $appointment->setVendor($vendor);
            $entityManager->persist($appointment);
            $entityManager->flush();

            return $this->redirectToRoute('vet_list', ['zipcode' => $zipcode]);
        }

        $query = $entityManager->createQuery(
            'SELECT s
        FROM App\Entity\Vendor s
        WHERE SUBSTRING(s.zipcode, 1, 2) = :zipcode'
        )->setParameter('zipcode', substr($zipcode, 0, 2));
        $vets = $query->getResult();

        // Trier les vétérinaires en fonction de la similitude de leur code postal
        usort($vets, function($a, $b) use ($zipcode) {
            $levA = levenshtein($a->getZipcode(), $zipcode);
            $levB = levenshtein($b->getZipcode(), $zipcode);
            return $levA - $levB;
        });

        return $this->render('vet/list.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView(),
            'vets' => $vets,
            'zipcode' => $zipcode
        ]);
    }

    #[Route("/list/vet/appointments/{id}", name: "vet_list_appointments")]
    #[Route("/list/vet/appointments/{id}", name: "vet_list_appointments")]
    public function listEventJson(Request $request, EntityManagerInterface $entityManager, Vendor $vendor)
    {
        $appointments = $entityManager->getRepository(Appointment::class)->findBy(['vendor' => $vendor]);
        $appointmentsArray = [];
        foreach ($appointments as $appointment) {
            $start = $appointment->getAppointmentDateTime();
            $end = $start->add(self::convertStringToDateInterval($appointment->getVendor()->getAppointmentDuration()));
            $isBooked = false;

            // Vérifier si ce créneau horaire est déjà réservé
            foreach ($appointmentsArray as $existingAppointment) {
                if ($start >= $existingAppointment['start'] && $start < $existingAppointment['end']) {
                    $isBooked = true;
                    break;
                } elseif ($end > $existingAppointment['start'] && $end <= $existingAppointment['end']) {
                    $isBooked = true;
                    break;
                }
            }

            // Ajouter le créneau horaire à la liste
            $appointmentInfo = [
                'start' => $start->format(DATE_ATOM),
                'end' => $end->format(DATE_ATOM),
                'title' => $isBooked ? 'Déjà réservé' : 'Non disponible',
                'backgroundColor' => $isBooked ? '#000' : '#ff0000'
            ];
            $appointmentsArray[] = $appointmentInfo;
        }

        return $this->json($appointmentsArray);
    }


   static private function convertStringToDateInterval(string $timeString): ?\DateInterval {
        // Valider le format de la chaîne de caractères
        if (!preg_match("/^(\d{2}):(\d{2}):(\d{2})$/", $timeString, $matches)) {
            return null;
        }

        // Extraire les heures, minutes et secondes
        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];
        $seconds = (int) $matches[3];

        // Créer un objet DateInterval
        $intervalSpec = sprintf("PT%dH%dM%dS", $hours, $minutes, $seconds);
        $interval = new \DateInterval($intervalSpec);

        return $interval;
    }
}