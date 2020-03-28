<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkLogRepository")
 */
class WorkLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $relative_id;

    /**
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="workLogs")
     * @var Task
     */
    private $task;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="workLogs")
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $time_start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $time_end;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $comment;

    public static function create(Task $task, User $user, ?int $start = null, ?int $end = null, ?string $comment = null) {
        return (new WorkLog())
            ->setTask($task)
            ->setUser($user)
            ->setTimeStart($start)
            ->setTimeEnd($end)
            ->setComment($comment);
    }

    private function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getRelativeId()
    {
        return $this->relative_id;
    }

    /**
     * @param integer $relativeId
     * @return WorkLog
     */
    public function setRelativeId(int $relativeId): self
    {
        $this->relative_id = $relativeId;

        return $this;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @param Task $task
     * @return WorkLog
     */
    public function setTask(Task $task): self
    {
        $this->task = $task;

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
     * @return WorkLog
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimeStart(): ?DateTime
    {
        return $this->time_start;
    }

    /**
     * @param int|null $timeStart
     * @return WorkLog
     */
    public function setTimeStart(?int $timeStart): self
    {
        $start = new DateTime();
        if (!empty($timeStart)) {
            $start->setTimestamp($timeStart);
        }
        $this->time_start = $start;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTimeEnd(): ?DateTime
    {
        return $this->time_end;
    }

    /**
     * @param int|null $timeEnd
     * @return WorkLog
     */
    public function setTimeEnd(?int $timeEnd): self
    {
        if (!empty($timeEnd)) {
            $end = new DateTime();
            $end->setTimestamp($timeEnd);
            $this->time_end = $end;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return WorkLog
     */
    public function setComment(?string $comment): self
    {
        if ($comment != null) {
            $this->comment = $comment;
        }
        return $this;
    }
}
