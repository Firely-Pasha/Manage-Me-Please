<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskSectionRepository")
 */
class TaskSection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="taskLists")
     * @ORM\JoinColumn()
     * @var Project
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="taskList")
     * @var PersistentCollection
     */
    private $tasks;

    public function getId(): ?int
    {
        return $this->id;
    }
}
