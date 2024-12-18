<?php

namespace Models\Entities;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="article", indexes={
 *     @ORM\Index(name="idx_ordergroup_depth_createdate", columns={"orderGroup", "depth", "createDate"})
 * })
 */
class Article
{
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

    public function setCreateDate($createDate)
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

    public function setModifyDate($modifyDate)
    {
        $this->modifyDate = $modifyDate;
    }

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $ip;

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $ip =  $_SERVER['REMOTE_ADDR'];
        $this->ip = $ip;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
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
     * @ORM\Column(type="bigint", nullable=false, options={"unsigned":true, "default":0})
     */
    private $hit;

    public function getHit()
    {
        return $this->hit;
    }

    public function setHit($hit)
    {
        $this->hit = $hit;
    }

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":1})
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
        $validScopes = ['public', 'members', 'admins'];
        if (!in_array($publicScope, $validScopes)) {
            throw new \InvalidArgumentException("유효하지 않은 공개 범위 값입니다.");
        }
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
     * @ORM\Column(type="string", nullable=true, options={"default":null})
     **/
    private $prefix;

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @ORM\ManyToOne(targetEntity="ArticleBoard")
     * @ORM\JoinColumn(name="article_board_id", referencedColumnName="id", nullable=true)
     */
    private $articleBoard;

    public function getArticleBoard()
    {
        return $this->articleBoard;
    }

    public function setArticleBoard($articleBoard)
    {
        $this->articleBoard = $articleBoard;
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

    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Article", cascade={"remove"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $parent;

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function __construct()
    {
        $this->createDate = new DateTime();
        $this->isActive = true;
        $this->hit = 0;
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
