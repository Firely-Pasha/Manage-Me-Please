<?php

namespace App\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskListRepository")
 */
class TaskList
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Name must not be empty")
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="taskLists")
     * @ORM\JoinColumn()
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $relativeId;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="taskList")
     * @var PersistentCollection
     */
    private $tasks;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $sort;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $deleted;

    public static function create(string $name, Project $project): self
    {
        return (new self())
            ->setName($name)
            ->setProject($project)
            ->setSort(0)
            ->setDeleted(false);
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
     * @return TaskList
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     * @return TaskList
     */
    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getTasks(): ?PersistentCollection
    {
        return $this->tasks;
    }

    /**
     * @return integer
     */
    public function getRelativeId(): int
    {
        return $this->relativeId;
    }

    /**
     * @param integer $relativeId
     * @return TaskList
     */
    public function setRelativeId(int $relativeId): self
    {
        $this->relativeId = $relativeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     * @return TaskList
     */
    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     * @return TaskList
     */
    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public static function getCriteria(): Criteria
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('deleted', false));
    }
}
