<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CGUController extends AbstractController
{
    #[Route("/cgu", name: "app_cgu")]
    public function base()
    {
        return $this->render("CGU.html.twig");
    }
}