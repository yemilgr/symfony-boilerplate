<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PasswordResetRepository")
 */
class PasswordReset
{
    /**
     * The time before a user can retry a password reset (30 min)
     */
    const TTL_TIME = 1800;

    /**
     * Password reset expiration - 60 min
     */
    const EXPIRE_TIME = 3600;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="passwordReset")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken($token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param  UserInterface  $user
     *
     * @return PasswordReset
     */
    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isInTtl(): bool
    {
        return (time() - $this->createdAt->getTimestamp()) < self::TTL_TIME;
    }

    public  function isExpired(): bool
    {
        return (time() - $this->createdAt->getTimestamp()) > self::EXPIRE_TIME;
    }

}
