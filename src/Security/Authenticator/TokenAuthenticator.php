<?php

namespace App\Security\Authenticator;

use App\Entity\User;
use App\Entity\Vendor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\Service\Attribute\Required;

class TokenAuthenticator extends AbstractAuthenticator
{
    private EntityManagerInterface $entityManager;

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): self
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('Authorization');

        if ($token === null) {
            throw new AccessDeniedException('Missing token');
        }

        return new SelfValidatingPassport(new UserBadge($token, [$this, 'getUserByToken']));
    }

    public function getUserByToken(string $token): null|User|Vendor
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        if (null !== $user) {
            return $user;
        }

        return $this->entityManager->getRepository(Vendor::class)->findOneBy(['token' => $token]);
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
    }
}