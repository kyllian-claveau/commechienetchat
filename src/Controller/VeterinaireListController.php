<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VeterinaireListController extends AbstractController
{
#[Route("/appointment/form", name: "app_appointment_form")]
function VeterinaireAppointment (){
    return $this->render('vet/appointment.html.twig');
}
}