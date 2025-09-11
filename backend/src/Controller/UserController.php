<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;

final class UserController extends AbstractController
{
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(?UserInterface $user, SerializerInterface $serializer): Response
    {
        if (!$user) {
            return $this->json([
                'status'  => 'error',
                'code'    => 'AUTHENTICATION_REQUIRED',
                'message' => 'Authentication required.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $json = $serializer->serialize($user, 'json', [
            'groups' => ['user:read'],
        ]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
