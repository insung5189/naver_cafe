<?php

namespace Models\Entities;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment", indexes={
 *     @ORM\Index(name="idx_ordergroup_depth_createdate", columns={"orderGroup", "depth", "createDate"})
 * })
 */
class Comment
{
    public function __construct()
    {
        $this->createDate = new DateTime();
        $this->isActive = true;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint", options={"unsigned":true})
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="datetime", columnDefinition="DATETIME(6) DEFAULT NOW()", nullable=false)
     */
    private $createDate;

    public function getCreateDate()
    {
        return $this->createDate;
    }

    public function setCreateDate(DateTime $createDate)
    {
        $this->createDate = $createDate;
    }

    /**
     * @ORM\Column(type="datetime", columnDefinition="DATETIME(6) DEFAULT NULL", nullable=true)
     */
    private $modifyDate;

    public function getModifyDate()
    {
        return $this->modifyDate;
    }

    public function setModifyDate(?DateTime $modifyDate)
    {
        $this->modifyDate = $modifyDate;
    }

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $content;

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @ORM\Column(type="boolean", options={"default":1})
     */
    private $isActive;

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedDate;

    public function getDeletedDate(): ?DateTime
    {
        return $this->deletedDate;
    }

    public function setDeletedDate(?DateTime $deletedDate): self
    {
        $this->deletedDate = $deletedDate;
        return $this;
    }


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $publicScope;

    public function getPublicScope()
    {
        return $this->publicScope;
    }

    public function setPublicScope($publicScope)
    {
        $this->publicScope = $publicScope;
    }

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $depth;

    public function getDepth()
    {
        return $this->depth;
    }

    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentFilePath;

    public function getCommentFilePath()
    {
        return $this->commentFilePath;
    }

    public function setCommentFilePath($commentFilePath)
    {
        $this->commentFilePath = $commentFilePath;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentFileName;

    public function getCommentFileName()
    {
        return $this->commentFileName;
    }

    public function setCommentFileName($commentFileName)
    {
        $this->commentFileName = $commentFileName;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uniqueIdentifier;

    public function getUniqueIdentifier()
    {
        return $this->uniqueIdentifier;
    }

    public function setUniqueIdentifier($uniqueIdentifier)
    {
        $this->uniqueIdentifier = $uniqueIdentifier;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $member;

    public function getMember()
    {
        return $this->member;
    }

    public function setMember(?Member $member)
    {
        $this->member = $member;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="comments")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $article;

    public function getArticle()
    {
        return $this->article;
    }

    public function setArticle(?Article $article)
    {
        $this->article = $article;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Comment", cascade={"remove"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $parent;

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(?Comment $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @ORM\Column(type="integer", options={"unsigned":true}, nullable=false)
     */
    private $orderGroup;

    public function getOrderGroup()
    {
        return $this->orderGroup;
    }

    public function setOrderGroup($orderGroup)
    {
        $this->orderGroup = $orderGroup;
    }
}
