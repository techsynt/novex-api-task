<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    private function deserialize(Request $request): User
    {
        return $this->serializer->deserialize($request->getContent(), User::class, 'json', [
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
        ]);
    }

    private function validate(object $data): array
    {
        $errors = $this->validator->validate($data);
        $formattedErrors = [];
        foreach ($errors as $error) {
            $formattedErrors[$error->getPropertyPath()] = $error->getMessage();
        }

        return $formattedErrors;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $user = $this->deserialize($request);

        $validationResult = $this->validate($user);
        if ($validationResult) {
            throw new ValidatorException(json_encode($validationResult));
        }
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param $id
     * @return User
     */
    public function get($id): User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new EntityNotFoundException();
        }

        return $user;
    }

    /**
     * @return array
     */
    public function list(): array
    {
        $users = $this->userRepository->findAll();
        if (!$users) {
            throw new EntityNotFoundException();
        }

        return $this->userRepository->findAll();
    }


    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $user = $this->userRepository->find($id);
        if (null != $user) {
            $this->em->remove($user);
            $this->em->flush();
        } else {
            throw new EntityNotFoundException();
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws \Exception
     */
    public function update(Request $request): void
    {
        $userData = json_decode($request->getContent(), true);
        $user = $this->userRepository->find($request->get('id'));
        if (!$user) {
            throw new EntityNotFoundException();
        }

        $user->setName($userData['name']);
        $user->setPhone($userData['phone']);
        $user->setBirthday(new \DateTime($userData['birthday']));
        $user->setSex($userData['sex']);
        $user->setEmail($userData['email']);

        $validationResult = $this->validate($user);
        if ($validationResult) {
            throw new ValidatorException(json_encode($validationResult));
        }
        $this->em->flush();
    }
}
