<?php

namespace App\Controller;

use App\Form\SearchVetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route("/")]
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $clientCount = $entityManager->getRepository('App\Entity\User')->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $vetCount = $entityManager->getRepository('App\Entity\Vendor')->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $dogCount = $entityManager->getRepository('App\Entity\Pet')->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.type = :type')
            ->setParameter('type', 'DOG_TYPE')
            ->getQuery()
            ->getSingleScalarResult();

        $catCount = $entityManager->getRepository('App\Entity\Pet')->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.type = :type')
            ->setParameter('type', 'CAT_TYPE')
            ->getQuery()
            ->getSingleScalarResult();

        $form = $this->createForm(SearchVetType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('vet_list', [
                'zipcode' => $form->get('zipcode')->getData(),
            ]);
        }

        return $this->render('index.html.twig', [
            'form' => $form->createView(),
            'clientCount' => $clientCount,
            'vetCount' => $vetCount,
            'dogCount' => $dogCount,
            'catCount' => $catCount,
        ]);
    }
}