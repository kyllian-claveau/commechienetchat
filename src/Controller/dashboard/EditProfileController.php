<?php

namespace App\Controller\dashboard;

use App\Entity\User;
use App\Entity\Vendor;
use App\Form\dashboardUser\ChangeUserPasswordType;
use App\Form\dashboardUser\EditUserProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class EditProfileController extends AbstractController
{
    #[Route('/dashboard/user/user-profile', name: 'app_user_profile')]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $error = null;
        $formPassword = $this->createForm(ChangeUserPasswordType::class);
        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $data = $formPassword->getData();
            $user = $this->getUser();
            if ($passwordHasher->isPasswordValid($user, $data['oldPassword'])) {
                $newEncodedPassword = $passwordHasher->hashPassword($user, $data['newPassword']);
                $user->setPassword($newEncodedPassword);
                $entityManager->persist($user);
                $entityManager->flush();

            } else {
                $error = "L'ancien mot de passe n'est pas correct";
            };
        };

                /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(EditUserProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getAvatarFile() !== null) {
                $filename = uniqid() . '.' . $user->getAvatarFile()->guessClientExtension();

                $path = $this->getParameter('kernel.project_dir') . '/public/avatar/' . $filename;
                $content = $user->getAvatarFile()->getContent();
                file_put_contents($path, $content);
                $user->setAvatarFilename($filename);
                $user->setAvatarFile(null);
            }
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('dashboard/users-profile.html.twig', [
            'form' => $form->createView(),
            'formPassword' => $formPassword->createView(),
            'error' => $error
        ]);
    }
}