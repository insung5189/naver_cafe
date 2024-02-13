<?php
namespace Models\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="login_attempts")
 */
class LoginAttempt
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="loginAttempts", cascade={"remove"})
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $member;

    public function getMember()
    {
        return $this->member;
    }

    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * @ORM\Column(type="integer")
     */
    private $attempt_count;

    public function getAttemptCount()
    {
        return $this->attempt_count;
    }

    public function setAttemptCount($attempt_count)
    {
        $this->attempt_count = $attempt_count;
    }

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_attempt_at;

    public function getLastAttemptAt()
    {
        return $this->last_attempt_at;
    }

    public function setLastAttemptAt($last_attempt_at)
    {
        $this->last_attempt_at = $last_attempt_at;
    }
}
