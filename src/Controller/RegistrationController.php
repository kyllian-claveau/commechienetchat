<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vendor;
use App\Form\LoginType;
use App\Form\LoginVetoType;
use App\Form\RegistrationFormType;
use App\Form\RegistrationVetoFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);  
        $form->handleRequest($request);

        $vendor = new Vendor;
        $vetoform = $this->createForm(RegistrationVetoFormType::class, $vendor);
        $vetoform->handleRequest($request); 

        

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');

            // do anything else you need here, like send an email
        }

        if ($vetoform->isSubmitted() && $vetoform->isValid()) {
            // encode the plain password
            $vendor->setPassword(
                $userPasswordHasher->hashPassword(
                    $vendor,
                    $vetoform->get('plainPassword')->getData()
                )
            );
            $vendor->setRoles(['ROLE_VENDOR']);
            $entityManager->persist($vendor);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');

            // do anything else you need here, like send an email
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'registrationVetoForm' => $vetoform->createView(),
        ]);
    }
    #[Route('/vendor/login', name: 'app_vendor_login')]
    public function loginVendor(): Response
    {
        $loginVetoForm = $this->createForm(LoginVetoType::class);
        return $this->render('registration/veto_login.html.twig', [
            'loginVetoForm' => $loginVetoForm->createview()
        ]);
    }
    #[Route('/login', name: 'app_login')]
    public function login(EntityManagerInterface $entityManager): Response
    {
        $loginForm = $this->createForm(LoginType::class);
        // Si l'utilisateur est authentifié avec succès, mettez à jour la date et l'heure de la dernière connexion
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->getUser();
            $user->setLastLogin(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->render('registration/login.html.twig', [
            'loginForm' => $loginForm->createView()
        ]);
    }
}