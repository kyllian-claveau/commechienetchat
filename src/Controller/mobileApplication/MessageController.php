<?php

namespace App\Controller\mobileApplication;

use App\Entity\Message;
use App\Entity\User;
use App\Entity\Vendor;
use App\Form\mobileApplication\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mobile')]
class MessageController extends AbstractController
{
    #[Route('/messages/create/{id}', methods: ['POST'])]
    public function createMessage(Request $request, int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $currentUser = $this->getUser();

        if ($currentUser instanceof Vendor) {
            $senderType = Message::SENDER_TYPE_VENDOR;
            $vendor = $currentUser;
            $user = $entityManager->getRepository(User::class)->find($id);
        } else if ($currentUser instanceof User) {
            $senderType = Message::SENDER_TYPE_USER;
            $user = $currentUser;
            $vendor = $entityManager->getRepository(Vendor::class)->find($id);
        } else {
            throw new \RuntimeException('Should never happen');
        }
        $message = new Message();
        $message
            ->setUser($user)
            ->setVendor($vendor)
            ->setSenderType($senderType)
        ;

        $form = $this->createForm(MessageType::class, $message);
        $form->submit($request->toArray());

        if ($form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->json([
                'result' => true,
            ]);
        }

        $errorsForm = $form->getErrors(true);

        $errors = [];
        foreach($errorsForm as $errorForm)
        {
            $errors[] = $errorForm->getMessage();
        }

        return $this->json([
            'result' => false,
            'errors' => $errors,
        ]);
    }

    #[Route('/messages/list/{id}')]
    public function listMessages(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $currentUser = $this->getUser();

        if ($currentUser instanceof Vendor) {
            $vendor = $currentUser;
            $user = $entityManager->getRepository(User::class)->find($id);
        } else if ($currentUser instanceof User) {
            $user = $currentUser;
            $vendor = $entityManager->getRepository(Vendor::class)->find($id);
        } else {
            throw new \RuntimeException('Should never happen');
        }

        $messages = $entityManager->getRepository(Message::class)->findBy([
            'vendor' => $vendor,
            'user' => $user,
        ]);

        $messagesJson = [];

        foreach($messages as $message) {
            $messagesJson[] = [
                'id' => $message->getId(),
                'vendor_id' => $message->getVendor()->getId(),
                'user_id' => $message->getUser()->getId(),
                'message' => $message->getMessage(),
                'created_at' => $message->getCreatedAt()->format(DATE_ATOM),
                'sender_type' => $message->getSenderType(),
            ];
        }

        return $this->json([
            'messages' => $messagesJson,
        ]);
    }
}