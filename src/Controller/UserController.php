<?php

namespace App\Controller;

use App\Service\UserService;
use Doctrine\ORM\EntityNotFoundException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Exception\ValidatorException;

#[Route('/api')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    #[Route('/user', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $this->userService->create($request);
        } catch (ValidatorException $e) {
            return new JsonResponse([
                'status' => 'failed',
                'errors' => json_decode($e->getMessage(), true),
            ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(['status' => 'success'], Response::HTTP_CREATED);
    }

    #[OA\Delete(
        summary: 'Удаляет пользователя по заданному id',
        parameters: [new OA\Parameter(
            name: 'id',
            description: 'id пользователя для удаления',
            in: 'path',
            schema: new OA\Schema(
                type: 'integer'
            )
        )],
        responses: [
            new OA\Response(
                response: 404,
                description: 'Пользователь не найден',
            ),
            new OA\Response(
                response: 204,
                description: 'Пользователь успешно удален'
            )]
    )]
    #[Route('/user/{id}', methods: ['DELETE'])]
    public function delete(UserService $userService, $id): JsonResponse
    {
        try {
            $userService->delete($id);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(['status' => 'failed', 'errors' => 'Пользователь не найден'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/user/{id}', methods: ['GET'])]
    public function show(UserService $userService, $id): JsonResponse
    {
        try {
            $user = $userService->get($id);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(
                [
                    'status' => 'failed',
                    'errors' => 'Пользователь не найден',
                ], Response::HTTP_NOT_FOUND
            );
        }

        return $this->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    #[Route('/user/{id}', methods: ['PATCH'])]
    public function update(Request $request)
    {
        try {
            $this->userService->update($request);
        } catch (ValidatorException $e) {
            return new JsonResponse([
                'status' => 'failed',
                'errors' => json_decode($e->getMessage(), true),
            ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }
}
