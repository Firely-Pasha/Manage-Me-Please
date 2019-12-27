<?php

namespace App\Entity;

use App\Helpers\DateHelper;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("login", message="Login {{ value }} is already used")
 * @UniqueEntity("email", message="Email {{ value }} is already used")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=63)
     * @Assert\NotBlank(message="Name must not be empty")
     * @Assert\Length(max="63")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true, length=63)
     * @Assert\NotBlank(message="Surname must not be empty")
     * @Assert\Length(max="63")
     * @var string
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=63)
     * @Assert\NotBlank(message="Login must not be empty")
     * @Assert\Length(max="63")
     * @var string
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Password must not be empty")
     * @Assert\Length(max="255")
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Email must not be empty")
     * @Assert\Email()
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $create_date;

    /**
     * @ORM\ManyToMany(targetEntity="Company", mappedBy="employees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $companies;

    /**
     * @ORM\ManyToMany(targetEntity="Project", inversedBy="users")
     * @ORM\JoinColumn()
     * @var PersistentCollection
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="assignedTo")
     * @ORM\JoinColumn()
     * @var PersistentCollection
     */
    private $tasks;

    public function __construct(string $identifier = null)
    {
        $this->create_date = new DateTime();
        $this->identifier = null;
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return User
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     * @return User
     */
    public function setSurname($surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin($login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCreateDate(): DateTime
    {
        return $this->create_date;
    }

    public function setCreateDate($create_date): self
    {
        $this->create_date = DateHelper::stringToDatetime($create_date);

        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getCompanies(): PersistentCollection
    {
        return $this->companies;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return ["ROLE_USER"];
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->login;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return PersistentCollection
     */
    public function getProjects(): PersistentCollection
    {
        return $this->projects;
    }

    /**
     * @param Project $project
     * @return User
     */
    public function addProject(Project $project): self
    {
        $this->projects->add($project);

        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getTasks(): PersistentCollection
    {
        return $this->tasks;
    }
}
