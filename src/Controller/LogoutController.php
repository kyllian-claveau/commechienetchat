<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
  
class LogoutController extends AbstractController
{
    #[Route('/logout', name: 'app_user_authentication_logout')]
    public function logout(): void
    {
        // Should never be called, used only to defined "/logout" route
    }
}
 