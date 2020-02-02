<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagGroupRepository")
 */
class TagGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tagGroups")
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $relativeId;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="tagGroup")
     * @var Collection
     */
    private $tags;

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
     * @return TagGroup
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
     * @return TagGroup
     */
    public function setRelativeId(int $relativeId): self
    {
        $this->relativeId = $relativeId;

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
     * @return TagGroup
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }
}
