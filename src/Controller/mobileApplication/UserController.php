<?php

namespace App\Controller\mobileApplication;

use App\Entity\User;
use App\Entity\Vendor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mobile')]
class UserController extends AbstractController
{
    #[Route('/show/user-information')]
    public function show(EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getUser();

        if ($currentUser instanceof Vendor) {
            $userOrVendorInformations = $entityManager->getRepository(Vendor::class)->find($currentUser->getId());
        } elseif ($currentUser instanceof User) {
            $userOrVendorInformations = $entityManager->getRepository(User::class)->find($currentUser->getId());
        } else {
            throw new \RuntimeException('Should never happen');
        }

        $userOrVendorInformationJson = [
            'id' => $userOrVendorInformations->getId(),
            'name' => $userOrVendorInformations->getName(),
            'email' => $userOrVendorInformations->getEmail(),
        ];

        return $this->json([
            'userOrVendorInformation' => $userOrVendorInformationJson,
        ]);
    }

    #[Route('/modify/user-information')]
    public function update(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            throw new \RuntimeException('Only users can modify their information');
        }

        $data = json_decode($request->getContent(), true);

        // Modifier les champs de l'utilisateur avec les données de la requête
        $currentUser->setName($data['nom']);
        $currentUser->setEmail($data['email']);

        // Enregistrer les modifications dans la base de données
        $entityManager->persist($currentUser);
        $entityManager->flush();

        return $this->json([
            'message' => 'User information updated successfully',
            'user' => [
                'id' => $currentUser->getId(),
                'name' => $currentUser->getName(),
                'email' => $currentUser->getEmail(),
            ],
        ]);
    }

}