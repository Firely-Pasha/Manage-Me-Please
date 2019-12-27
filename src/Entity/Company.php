<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Name must not be empty")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @var User
     */
    private $owner;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $create_date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_private;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="companies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employees;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="company")
     * @ORM\JoinColumn()
     * @var PersistentCollection
     */
    private $projects;

    public static function create(string $name, User $owner) : self
    {
        return (new Company())
            ->setName($name)
            ->setOwner($owner)
            ->setCreateDate(new DateTime())
            ->setIsPrivate(false);

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Company
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     * @return Company
     */
    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreateDate(): DateTime
    {
        return $this->create_date;
    }

    private function setCreateDate(DateTime $datetime): self
    {
        $this->create_date = $datetime;

        return $this;
    }

    public function getIsPrivate(): ?bool
    {
        return $this->is_private;
    }

    public function setIsPrivate(bool $is_private): self
    {
        $this->is_private = $is_private;

        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getEmployees(): PersistentCollection
    {
        return $this->employees;
    }

    public function addEmployee(User $user): self
    {
        $this->employees[] = $user;
        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getProjects(): PersistentCollection
    {
        return $this->projects;
    }
}
