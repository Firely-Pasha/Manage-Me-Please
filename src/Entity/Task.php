<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $relativeId;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tasks")
     * @ORM\JoinColumn()
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $description;

//    private $section;

//    private $sprint;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="assigned_to", nullable=true)
     * @var User
     */
    private $assignedTo;

    /**
     * @ORM\ManyToOne(targetEntity="TaskList", inversedBy="tasks")
     * @ORM\JoinColumn()
     * @var TaskList
     */
    private $taskList;

    /**
     * @ORM\ManyToMany(targetEntity="TaskSection", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=true)
     * @var TaskSection
     */
    private $taskSection;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="assigned_by")
     * @var User
     */
    private $assignedBy;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $deleted;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="tasks")
     * @var PersistentCollection
     */
    private $tags;

    /**
     * @param Project $project
     * @param string $name
     * @param User $assignedBy
     * @param TaskList $taskList
     * @param User|null $assignedTo
     * @param string|null $description
     * @param Tag[] $tags
     * @return static
     */
    public static function create(Project $project, string $name, User $assignedBy, TaskList $taskList, ?User $assignedTo = null, ?string $description, array $tags): self
    {
        $task = (new self())
            ->setProject($project)
            ->setName($name)
            ->setAssignedBy($assignedBy)
            ->setTaskList($taskList)
            ->initTags();

        if ($assignedTo !== null) {
            $task->setAssignedTo($assignedTo);
        }

        if ($description !== null) {
            $task->setDescription($description);
        }

        if ($tags !== null) {
            $task->addTags($tags);
        }

        $task->setDeleted(false);

        return $task;
    }


    public function getId(): ?int
    {
        return $this->id;
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
     * @return Task
     */
    public function setProject(Project $project): self
    {
        $this->project = $project;

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
     * @return Task
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Task
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return TaskList|null
     */
    public function getTaskList(): ?TaskList
    {
        return $this->taskList;
    }

    /**
     * @param TaskList $taskList
     * @return Task
     */
    public function setTaskList(?TaskList $taskList): self
    {
        $this->taskList = $taskList;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    /**
     * @param User $assignedTo
     * @return Task
     */
    public function setAssignedTo(?User $assignedTo): self
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    /**
     * @return User
     */
    public function getAssignedBy(): User
    {
        return $this->assignedBy;
    }

    /**
     * @param User $assignedBy
     * @return Task
     */
    public function setAssignedBy(User $assignedBy): self
    {
        $this->assignedBy = $assignedBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelativeId()
    {
        return $this->relativeId;
    }

    /**
     * @param mixed $relativeId
     * @return Task
     */
    public function setRelativeId($relativeId): self
    {
        $this->relativeId = $relativeId;

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
     * @return Task
     */
    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags->matching(Tag::getCriteria());
    }

    public function initTags(): self
    {
        $this->tags = new ArrayCollection();
        return $this;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
        return $this;
    }

    /**
     * @param Tag[] $tags
     * @return $this
     */
    public function addTags(array $tags): self
    {
        foreach ($tags as $tag) {
            $this->tags->add($tag);
        }

        return $this;
    }
}
