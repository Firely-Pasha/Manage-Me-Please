<?php

namespace App\Entity;

use DateInterval;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SimpleTokenRepository")
 */
class SimpleToken
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @var DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(name="expires_in", type="datetime")
     * @var DateTime
     */
    private $expiresIn;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $revoked;

    public function __construct()
    {
        $this->id = $this->genId();
        $this->createdAt = new DateTime();
        $expiresIn = new DateTime();
        $expiresIn->add(new DateInterval('PT24H'));
        $this->expiresIn = $expiresIn;
        $this->revoked = false;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function genId(): self
    {
        $this->id = md5(Uuid::uuid4()->toString());

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
     * @param User $user
     * @return SimpleToken
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return SimpleToken
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpiresIn(): DateTime
    {
        return $this->expiresIn;
    }

    /**
     * @param DateTime $expiresIn
     * @return SimpleToken
     */
    public function setExpiresIn(DateTime $expiresIn): self
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    /**
     * @param bool $revoked
     * @return SimpleToken
     */
    public function setRevoked(bool $revoked): self
    {
        $this->revoked = $revoked;

        return $this;
    }
}
