<?php

namespace App\Controller\mobileApplication;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mobile')]
class AuthenticationController
{
    #[Route('/login', 'app_mobile_login')]
    public function login(): JsonResponse
    {
        throw new \RuntimeException('Should never be called');
    }

}