<?php

namespace App\Controller;

use App\Service\UserService;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class UserController extends AbstractController
{
    #[Route('/user', methods: ['POST'])]
    public function createUser(UserService $userService, Request $request): JsonResponse
    {
        $user = $userService->deserializeUserData($request);
        $validationResult = $userService->validate($user);
        if (count($validationResult) > 0) {
            return new JsonResponse([
                'status' => Response::HTTP_BAD_REQUEST,
                'errors' => $validationResult,
            ],
                Response::HTTP_BAD_REQUEST);
        }
        $userService->create($user);

        return new JsonResponse(['status' => Response::HTTP_CREATED], Response::HTTP_CREATED);
    }

    #[Route('/user/{id}', methods: ['DELETE'])]
    public function deleteUser(UserService $userService, $id): JsonResponse
    {
        try {
            $userService->delete($id);

            return new JsonResponse(['status' => Response::HTTP_NO_CONTENT]);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(['status' => Response::HTTP_NOT_FOUND, 'message' => 'Пользователь не найден']);
        }
    }
}
