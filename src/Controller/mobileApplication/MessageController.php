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
        $currentUserType = $this->getUser()->getRoles();

        if ($currentUser instanceof Vendor) {
            $vendor = $currentUser;
            $user = $entityManager->getRepository(User::class)->find($id);
            $contactName = $user->getName();
        } else if ($currentUser instanceof User) {
            $user = $currentUser;
            $vendor = $entityManager->getRepository(Vendor::class)->find($id);
            $contactName = $vendor->getName();
        } else {
            throw new \RuntimeException('Should never happen');
        }

        $messages = $entityManager->getRepository(Message::class)->findBy([
            'vendor' => $vendor,
            'user' => $user,
        ]);

        $messagesJson = [];

        foreach($messages as $message) {
            $userName = $entityManager->getRepository(User::class)->find($message->getUser()->getId())->getName();
            $vendorName = $entityManager->getRepository(Vendor::class)->find($message->getVendor()->getId())->getName();
            $sentByCurrentUser = false;
            if ($currentUser instanceof Vendor && $message->getSenderType() === 'VENDOR' && $message->getVendor()->getId() === $currentUser->getId()) {
                $sentByCurrentUser = true;
            } elseif ($currentUser instanceof User && $message->getSenderType() === 'USER' && $message->getUser()->getId() === $currentUser->getId()) {
                $sentByCurrentUser = true;
            }

            $messagesJson[] = [
                'id' => $message->getId(),
                'vendor_id' => $message->getVendor()->getId(),
                'user_id' => $message->getUser()->getId(),
                'message' => $message->getMessage(),
                'created_at' => $message->getCreatedAt()->format(DATE_ATOM),
                'sender_type' => $message->getSenderType(),
                'sent_by_current_user' => $sentByCurrentUser,
                'user_name' => $userName,
                'vendor_name' => $vendorName,
                'currentUserRole' => $currentUserType,
                'contact_name' => $contactName // Ajoute le nom du contact
            ];
        }

        return $this->json([
            'contactName' => $contactName,
            'messages' => $messagesJson,
        ]);
    }




    #[Route('/usersorvendors/list')]
    public function listUsersOrVendors(EntityManagerInterface $entityManager)
    {
        $currentUser = $this->getUser();

        if ($currentUser instanceof Vendor) {
            $usersOrVendors = $entityManager->getRepository(User::class)->findAll();
        } elseif ($currentUser instanceof User) {
            $usersOrVendors = $entityManager->getRepository(Vendor::class)->findAll();
        } else {
            throw new \RuntimeException('Should never happen');
        }

        $usersOrVendorsJson = [];

        foreach($usersOrVendors as $userOrVendor) {
            $usersOrVendorsJson[] = [
                'id' => $userOrVendor->getId(),
                'name' => $userOrVendor->getName(),
                'avatarFileName' => $userOrVendor->getAvatarFilename(),
                'email' => $userOrVendor->getEmail(),
                'type' => ($userOrVendor instanceof Vendor) ? 'ROLE_VENDOR' : 'ROLE_USER',
            ];
        }

        return $this->json([
            'usersOrVendors' => $usersOrVendorsJson,
        ]);
    }

    #[Route('/usersorvendors/conversation-list')]
    public function conversationlistUsersOrVendors(EntityManagerInterface $entityManager)
    {
        $currentUser = $this->getUser();

        if ($currentUser instanceof Vendor) {
            $conversations = $entityManager->createQuery('
        SELECT m.id, u.id as userId, u.name as userName, u.email as userEmail, MAX(m.createdAt) as lastMessageDate, COUNT(m.id) as messageCount
        FROM App\Entity\Message m
        JOIN m.user u
        WHERE m.vendor = :vendor
        GROUP BY u.id, u.name, u.email, m.id
    ')
                ->setParameter('vendor', $currentUser)
                ->getResult();

            $userType = 'vendor'; // Ajouter une variable pour stocker le type d'utilisateur
        } elseif ($currentUser instanceof User) {
            $conversations = $entityManager->createQuery('
        SELECT m.id, v.id as vendorId, v.name as vendorName, v.email as vendorEmail, MAX(m.createdAt) as lastMessageDate, COUNT(m.id) as messageCount
        FROM App\Entity\Message m
        JOIN m.vendor v
        WHERE m.user = :user
        GROUP BY v.id, v.name, v.email, m.id
    ')
                ->setParameter('user', $currentUser)
                ->getResult();

            $userType = 'user'; // Ajouter une variable pour stocker le type d'utilisateur
        } else {
            throw new \RuntimeException('Should never happen');
        }

        return $this->json([
            'userType' => $userType, // Ajouter le type d'utilisateur dans la réponse JSON
            'conversations' => $conversations,
        ]);
    }



}