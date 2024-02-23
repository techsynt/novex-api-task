<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    public SerializerInterface $serializer;
    public EntityManagerInterface $em;
    public ValidatorInterface $validator;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validation)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->validator = $validation;
    }

    public function validate($data): array
    {
        $errors = $this->validator->validate($data);
        $formattedErrors = [];
        foreach ($errors as $error) {
            $formattedErrors[$error->getPropertyPath()] = $error->getMessage();
        }

        return $formattedErrors;
    }

    public function deserializeUserData(Request $request): User
    {
        return $this->serializer->deserialize($request->getContent(), User::class, 'json', [
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
        ]);
    }

    public function create(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function delete(int $id): void
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (null != $user) {
            $this->em->remove($user);
            $this->em->flush();
        } else {
            throw new EntityNotFoundException('Пользователь не найден');
        }
    }
}
