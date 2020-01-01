<?php

namespace App\Entity;

use Doctrine\DBAL\Driver\AbstractOracleDriver\EasyConnectString;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Company")
     * @ORM\JoinColumn()
     * @Assert\NotBlank(message="Project must be attached to company")
     * @var Company
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=7)
     * @Assert\NotBlank(message="Project must have a code")
     * @Assert\Regex(pattern="/^[A-Z]{1,7}$/i", htmlPattern="^[A-Z]{1,7}$", message="Code must satisfy pattern: [A-Z]{1,7}")
     * @var string
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @var User
     */
    private $owner;

    /**
     * @Assert\NotBlank(message="Name must not be empty")
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="projects")
     * @var PersistentCollection
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="TaskList", mappedBy="project")
     * @ORM\JoinColumn()
     * @var PersistentCollection
     */
    private $taskLists;

    /**
     * @ORM\OneToMany(targetEntity="TaskSection", mappedBy="project")
     * @ORM\JoinColumn()
     * @var PersistentCollection
     */
    private $taskSections;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="project")
     * @ORM\JoinColumn()
     * @var PersistentCollection
     */
    private $tasks;

    public static function create(User $user, Company $company, string $code, string $name): Project
    {
        return (new self())
            ->setOwner($user)
            ->setCompany($company)
            ->setCode($code)
            ->setName($name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Company
     */
    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * @param Company $company
     * @return Project
     */
    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Project
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

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
     * @return Project
     */
    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Project
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return PersistentCollection|array
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     * @return Project
     */
    public function addUser(User $user): self
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * @return PersistentCollection|null
     */
    public function getTaskLists(): ?PersistentCollection
    {
        return $this->taskLists;
    }

    /**
     * @return PersistentCollection|null
     */
    public function getTasks(): ?PersistentCollection
    {
        return $this->tasks;
    }

    /**
     * @param PersistentCollection $tasks
     * @return Project
     */
    public function setTasks(PersistentCollection $tasks): self
    {
        $this->tasks = $tasks;

        return $this;
    }
}
