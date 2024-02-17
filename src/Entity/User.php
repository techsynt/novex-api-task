<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ApiResource(
    operations: [
        new Get(
            openapiContext: [
                'summary' => 'Получить пользователя',
                'description' => '',
            ]
        ),
        new Patch(
            openapiContext: [
                'summary' => 'Обновить пользователя',
                'description' => '',
            ]
        ),
        new Post(
            openapiContext: [
                'summary' => 'Создать пользователя',
                'description' => '',
            ]
        ),
        new Delete(
            openapiContext: [
                'summary' => 'Удалить пользователя',
                'description' => '',
            ]
        ),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
#[ORM\HasLifecycleCallbacks]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['read', 'write'])]
    #[ORM\Column(length: 255)]
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
    #[ApiProperty(example: 'Алексей')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ApiProperty(example: 28)]
    #[Assert\Type('integer')]
    #[Groups(['read'])]
    #[ORM\Column]
    private ?int $age = null;

    #[Assert\NotBlank]
    #[Assert\Choice(['Male', 'Female'], message: 'Пол может быть только "Male" или "Female"')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(length: 255)]
    private ?string $sex = null;

    #[Assert\LessThanOrEqual(
        value: 'today',
        message: 'День рождения не может быть в будущем'
    )]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[ApiProperty(example: '2022-02-16', openapiContext: ['type' => 'string', 'format' => 'date'])]
    private ?\DateTimeInterface $birthday;

    #[ApiProperty(example: '+7(928)784-84-98')]
    #[AssertPhoneNumber()]
    #[Groups(['read', 'write'])]
    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[Groups(['read'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[Groups(['read'])]
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
        $this->age = $this->getBirthdayDate()->diff(new \DateTime())->y;

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

    public function getBirthdayDate(): \DateTimeInterface
    {
        return $this->birthday;
    }

    public function getBirthday(): string
    {
        return $this->birthday->format('Y-m-d');
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
