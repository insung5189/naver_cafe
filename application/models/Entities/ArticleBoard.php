<?php
namespace Models\Entities;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="article_board")
 */
class ArticleBoard
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
     * @ORM\Column(type="datetime", columnDefinition="DATETIME(6) DEFAULT NOW()", nullable=true)
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
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $boardName;

    public function getBoardName()
    {
        return $this->boardName;
    }

    public function setBoardName($boardName)
    {
        $this->boardName = $boardName;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $boardType;

    public function getBoardType()
    {
        return $this->boardType;
    }

    public function setBoardType($boardType)
    {
        $this->boardType = $boardType;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $writingPermission;
    
    public function getWritingPermission()
    {
        return $this->writingPermission;
    }

    public function setWritingPermission($writingPermission)
    {
        if (!in_array($writingPermission, ['ROLE_MEMBER', 'ROLE_ADMIN'])) {
            throw new \InvalidArgumentException("쓰기 권한이 없습니다");
        }
        $this->writingPermission = $writingPermission;
    }
    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     */
    private $mainDashboard;

    public function getMainDashboard()
    {
        return $this->mainDashboard;
    }

    public function setMainDashboard($mainDashboard)
    {
        $this->mainDashboard = $mainDashboard;
    }

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isDeleted;

    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

}
