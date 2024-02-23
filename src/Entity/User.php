<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Эта почта уже используется')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Поле name не может быть пустым')]
    #[Assert\Email]
    #[ORM\Column(name: 'email', length: 255, unique: true)]
    private ?string $email = null;

    #[Assert\Type('string')]
    #[Assert\Length(
        min: 2,
        max: 255,
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-zА-Яа-яЁё-]+$/u',
        message: 'Имя должно содержать только буквы и тире'
    )]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\Type('integer')]
    #[ORM\Column]
    private ?int $age = null;

    #[Assert\NotBlank(message: 'Поле sex не может быть пустым')]
    #[Assert\Choice(['Male', 'Female'], message: 'Пол может быть только "Male" или "Female"')]
    #[ORM\Column(length: 255)]
    private ?string $sex = null;

    #[Assert\NotBlank(message: 'Поле birthday не может быть пустым')]
    #[Assert\LessThanOrEqual(
        value: 'today',
        message: 'День рождения не может быть в будущем'
    )]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthday = null;

    #[Assert\NotBlank(message: 'Поле phone не может быть пустым')]
    #[AssertPhoneNumber()]
    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    #[ORM\PrePersist]
    public function setAge(): static
    {
        $this->age = $this->getBirthday()->diff(new \DateTime())->y;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): static
    {
        $this->sex = $sex;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
