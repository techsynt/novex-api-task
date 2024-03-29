<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityNotFoundException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Exception\ValidatorException;

#[Route('/api')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    private function buildResponse(
        string $status = 'success',
        $errors = null,
        $data = null,
        int $statusCode = 200
    ): JsonResponse {
        if ($status === 'failed') {
            return new JsonResponse(
                [
                    'status' => $status,
                    'errors' => $errors,
                ],
                $statusCode
            );
        }
        return $this->json([
            'status' => $status,
            'data' => $data,
        ]);
    }

    #[OA\Post(
        summary: 'Добавляет нового пользователя',
        requestBody: new OA\RequestBody(
            description: 'Добавить нового пользователя',
            content: new OA\JsonContent(
                ref: new Model(type: User::class)
            )
        ),
        responses: [
            new OA\Response(
                response: 400,
                description: 'Ошибка валидации',
            ),
            new OA\Response(
                response: 200,
                description: 'Пользователь успешно добавлен'
            )]
    )]
    #[Route('/user', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $this->userService->create($request);
        } catch (ValidatorException $e) {
            return $this->buildResponse(
                'failed',
                json_decode($e->getMessage(), true),
                null,
                400
            );
        }

        return $this->buildResponse();
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
                response: 400,
                description: 'Неправильный id',
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

            return $this->buildResponse(statusCode: 204);
        } catch (EntityNotFoundException $e) {
            return $this->buildResponse('failed', 'Пользователь не найден', statusCode: 404);
        } catch (\TypeError $e) {
            return $this->buildResponse('failed', 'Передан неправильный id', statusCode: 400);
        }
    }

    #[OA\Get(
        summary: 'Получает пользователя по id',
        parameters: [new OA\Parameter(
            name: 'id',
            description: 'id пользователя для получения',
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
                response: 200,
                description: 'Пользователь успешно получен'
            ),
            new OA\Response(
                response: 400,
                description: 'Неправильный id'
            )]
    )]
    #[Route('/user/{id}', methods: ['GET'])]
    public function show(UserService $userService, $id): JsonResponse
    {
        try {
            $user = $userService->get($id);
        } catch (EntityNotFoundException $e) {
            return $this->buildResponse('failed', 'Пользователь не найден', statusCode: 404);
        } catch (\TypeError $e) {
            return $this->buildResponse('failed', 'Передан неправильный id', statusCode: 400);
        }

        return $this->buildResponse(data: $user);
    }

    #[OA\Get(
        summary: 'Получает список пользователей',
        responses: [
            new OA\Response(
                response: 404,
                description: 'Пользователи не найдены',
            ),
            new OA\Response(
                response: 200,
                description: 'Пользователи успешно получены'
            )]
    )]
    #[Route('/user', methods: ['GET'])]
    public function list(UserService $userService): JsonResponse
    {
        try {
            $users = $userService->list();
        } catch (EntityNotFoundException $e) {
            return $this->buildResponse('failed', 'Пользователи не найдены', statusCode: 404);
        }
        return $this->buildResponse(data: $users);
    }

    #[OA\Put(
        summary: 'Обновляет пользователя по заданному id',
        requestBody: new OA\RequestBody(
            description: 'Обновить существующего пользователя',
            content: new OA\JsonContent(
                ref: new Model(type: User::class)
            )
        ),
        parameters: [new OA\Parameter(
            name: 'id',
            description: 'id пользователя для обновления',
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
                response: 422,
                description: 'Ошибка валидации',
            ),
            new OA\Response(
                response: 400,
                description: 'Неправильный id',
            ),
            new OA\Response(
                response: 200,
                description: 'Пользователь успешно обновлен'
            )]
    )]
    #[Route('/user/{id}', methods: ['PUT'])]
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $this->userService->update($request, $id);
        } catch (\TypeError $e) {
            return $this->buildResponse('failed', 'Передан неправильный id', statusCode: 400);
        } catch (ValidatorException $e) {
            return $this->buildResponse('failed', json_decode($e->getMessage(), true), statusCode: 422);
        } catch (EntityNotFoundException $e) {
            return $this->buildResponse('failed', 'Пользователь не найден', statusCode: 404);
        }

        return $this->buildResponse();
    }
}
