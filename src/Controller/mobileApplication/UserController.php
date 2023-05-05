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

        if ($currentUser instanceof Vendor) {
            $userOrVendor = $entityManager->getRepository(Vendor::class)->find($currentUser->getId());
        } elseif ($currentUser instanceof User) {
            $userOrVendor = $entityManager->getRepository(User::class)->find($currentUser->getId());
        } else {
            throw new \RuntimeException('Should never happen');
        }

        $requestData = json_decode($request->getContent(), true);

        if (isset($requestData['name'])) {
            $userOrVendor->setName($requestData['name']);
        }

        if (isset($requestData['email'])) {
            $userOrVendor->setEmail($requestData['email']);
        }

        $entityManager->flush();

        $userOrVendorInformationJson = [
            'id' => $userOrVendor->getId(),
            'name' => $userOrVendor->getName(),
            'email' => $userOrVendor->getEmail(),
        ];

        return $this->json([
            'userOrVendorInformation' => $userOrVendorInformationJson,
        ]);
    }

}