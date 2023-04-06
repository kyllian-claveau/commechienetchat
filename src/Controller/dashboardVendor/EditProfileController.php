<?php

namespace App\Controller\dashboardVendor;

use App\Entity\Vendor;
use App\Form\dashboardVendor\ChangePasswordType;
use App\Form\dashboardVendor\EditProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class EditProfileController extends AbstractController
{
    #[Route('/vendor/dashboard/user-profile', name: 'app_vendor_profile')]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $error = null;
        $formPassword = $this->createForm(ChangePasswordType::class);
        $formPassword->handleRequest($request);


        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $data = $formPassword->getData();
            $vendor = $this->getUser();
            if ($passwordHasher->isPasswordValid($vendor, $data['oldPassword'])) {
                $newEncodedPassword = $passwordHasher->hashPassword($vendor, $data['newPassword']);
                $vendor->setPassword($newEncodedPassword);
                $entityManager->persist($vendor);
                $entityManager->flush();
            } else {
                $error = "L'ancien mot de passe n'est pas correct";
            };
        };

        /** @var Vendor $vendor */
        $vendor = $this->getUser();
        $form = $this->createForm(EditProfileFormType::class, $vendor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($vendor->getAvatarFile() !== null) {
                $filename = uniqid() . '.' . $vendor->getAvatarFile()->guessClientExtension();

                $path = $this->getParameter('kernel.project_dir') . '/public/avatar/' . $filename;
                $content = $vendor->getAvatarFile()->getContent();
                file_put_contents($path, $content);
                $vendor->setAvatarFilename($filename);
                $vendor->setAvatarFile(null);
            }
            $entityManager->persist($vendor);
            $entityManager->flush();
        }
        return $this->render('dashboardVendor/users-profile.html.twig', [
            'form' => $form->createView(),
            'formPassword' => $formPassword->createView(),
            'error' => $error
        ]);
    }
}