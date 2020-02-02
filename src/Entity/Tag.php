<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tags")
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $relativeId;

    /**
     * @ORM\ManyToOne(targetEntity="TagGroup", inversedBy="tags")
     * @var TagGroup
     */
    private $tagGroup;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="Color")
     * @var Color
     */
    private $color;

    /**
     * @ORM\ManyToMany(targetEntity="Task", mappedBy="tags")
     * @var Collection
     */
    private $tasks;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $deleted;

    public static function create(Project $project, ?TagGroup $tagGroup = null, ?string $title = null, ?Color $color = null): self
    {
        return (new self())
            ->setProject($project)
            ->setTitle($title)
            ->setColor($color)
            ->setDeleted(false);
    }

    public static function getCriteria(): Criteria
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('deleted', false));
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
     * @return Tag
     */
    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return int
     */
    public function getRelativeId(): int
    {
        return $this->relativeId;
    }

    /**
     * @param int $relativeId
     * @return Tag
     */
    public function setRelativeId(int $relativeId): self
    {
        $this->relativeId = $relativeId;

        return $this;
    }

    /**
     * @return TagGroup|null
     */
    public function getTagGroup(): ?TagGroup
    {
        return $this->tagGroup;
    }

    /**
     * @param TagGroup $tagGroup
     * @return Tag
     */
    public function setTagGroup(?TagGroup $tagGroup): self
    {
        $this->tagGroup = $tagGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Tag
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Color|null
     */
    public function getColor(): ?Color
    {
        return $this->color;
    }

    /**
     * @param Color $color
     * @return Tag
     */
    public function setColor(?Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
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
     * @return Tag
     */
    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}
