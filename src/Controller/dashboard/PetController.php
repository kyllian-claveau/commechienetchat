<?php

namespace App\Controller\dashboard;

use App\Entity\Pet;
use App\Form\dashboardUser\PetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PetController extends AbstractController
{
    #[Route('/dashboard/pet', name: 'app_add_pet')]
    public function addPet(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pet = new Pet();
        $pet->setUser($this->getUser());
        $form = $this->createForm(PetType::class, $pet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($pet->getAvatarPetFile()!==null)
            {
                $filename = uniqid() . '.' . $pet->getAvatarPetFile()->guessClientExtension();

                $path = $this->getParameter('kernel.project_dir') . '/public/avatar/pet/' . $filename;
                $content = $pet->getAvatarPetFile()->getContent();
                file_put_contents($path, $content);
                $pet->setAvatarPetFilename($filename);
                $pet->setAvatarPetFile(null);
            }
            $entityManager->persist($pet);
            $entityManager->flush();

            return $this->redirectToRoute("app_add_pet");
        }
        $user = $this->getUser();
        $pets = $entityManager->getRepository(Pet::class)->findBy(['user' => $user]);
        $today = new \DateTime();
        return $this->render('dashboard/pet.html.twig', [
            'form' => $form->createView(),
            'pets' => $pets,
            'datetime' => $today
        ]);
    }
    #[Route('/dashboard/pet/delete/{id}', name: 'app_delete_pet')]
    public function deletePet(Request $request, Pet $pet, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($pet);
        $entityManager->flush();
        return $this->redirectToRoute("app_add_pet");
    }
}


