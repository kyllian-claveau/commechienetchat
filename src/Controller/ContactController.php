<?php

namespace App\Controller;
use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route("/contact", name: 'app_contact')]
    public function base(Request $request, EntityManagerInterface $entityManager):Response
    {
        $message = new Contact();
        $form = $this->createForm(ContactType::class, $message, ['action'=> $this->generateUrl("app_contact")]);

        $form->handleRequest($request);
        $sent = false;
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($message);
            $entityManager->flush();
            $sent = true;
        }

        return $this->render("contact.html.twig", [
        'form' => $form->createView(),
        'sent' => $sent
    ]);
    }


}