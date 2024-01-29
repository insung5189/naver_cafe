<?php
namespace Models\Entities;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="article")
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint", options={"unsigned":true})
     */
    private $id;

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
     * @ORM\Column(type="string", length=255)
     */
    private $publicScope;

    public function getPublicScope()
    {
        return $this->publicScope;
    }

    public function setPublicScope($publicScope)
    {
        if (!in_array($publicScope, ['ROLE_MEMBER', 'ROLE_ADMIN'])) {
            throw new \InvalidArgumentException("접근 권한이 없습니다");
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

    // Foreign key references
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
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable=false)
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
     * @ORM\ManyToOne(targetEntity="Article")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
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
        $this->hit = 1;
    }
}
