<?php
namespace Models\Entities;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="board_bookmark")
 */
class BoardBookmark
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint", options={"unsigned":true})
     */
    private $boardBookmarkId;

    public function getBoardBookmarkId()
    {
        return $this->boardBookmarkId;
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
     * @ORM\ManyToOne(targetEntity="ArticleBoard")
     * @ORM\JoinColumn(name="article_board_id", referencedColumnName="id")
     */
    private $articleBoard;

    public function getArticleBoard()
    {
        return $this->articleBoard;
    }
    
    public function setArticleBoard(?ArticleBoard $articleBoard)
    {
        $this->articleBoard = $articleBoard;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
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

    public function __construct()
    {
        $this->createDate = new DateTime();
    }
}
