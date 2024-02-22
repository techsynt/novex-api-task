<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class UserService
{
    public SerializerInterface $serializer;
    public EntityManagerInterface $em;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public function create(Request $request)
    {
        $userData = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $this->em->persist($userData);
        $this->em->flush();

        return 'hi';
    }
}
