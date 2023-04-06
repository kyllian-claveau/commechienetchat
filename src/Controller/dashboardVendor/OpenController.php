<?php

namespace App\Controller\dashboardVendor;

use App\Entity\Vendor;
use App\Form\dashboardVendor\OpenCloseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OpenController extends AbstractController
{
    #[Route("/vendor/horaires", name: "app_vendor_horaires")]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
          /** @var Vendor $vendor */
        $vendor = $this->getUser();
        $form = $this->createForm(OpenCloseType::class, $vendor);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vendor);
            $entityManager->flush();

            return $this->redirectToRoute('app_vendor_horaires');
        }

        return $this->render('dashboardVendor/openclose.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}