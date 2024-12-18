<?php
namespace Models\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="member")
 */
class Member
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
    private $userName;

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $password;

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $nickName;

    public function getNickName()
    {
        return $this->nickName;
    }

    public function setNickName($nickName)
    {
        $this->nickName = $nickName;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $firstName;

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $lastName;

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birth;

    public function getBirth()
    {
        return $this->birth;
    }

    public function setBirth($birth)
    {
        $this->birth = $birth;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $detailAddress;

    public function getDetailAddress()
    {
        return $this->detailAddress;
    }

    public function setDetailAddress($detailAddress)
    {
        $this->detailAddress = $detailAddress;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extraAddress;

    public function getExtraAddress()
    {
        return $this->extraAddress;
    }

    public function setExtraAddress($extraAddress)
    {
        $this->extraAddress = $extraAddress;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $jibunAddress;

    public function getJibunAddress()
    {
        return $this->jibunAddress;
    }

    public function setJibunAddress($jibunAddress)
    {
        $this->jibunAddress = $jibunAddress;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roadAddress;

    public function getRoadAddress()
    {
        return $this->roadAddress;
    }

    public function setRoadAddress($roadAddress)
    {
        $this->roadAddress = $roadAddress;
    }

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $postalNum;

    public function getPostalNum()
    {
        return $this->postalNum;
    }

    public function setPostalNum($postalNum)
    {
        $this->postalNum = $postalNum;
    }

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $gender;

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":true})
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
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $blacklist;

    public function getBlacklist()
    {
        return $this->blacklist;
    }

    public function setBlacklist($blacklist)
    {
        $this->blacklist = $blacklist;
    }

    /**
     * @ORM\Column(type="datetime", columnDefinition="DATETIME(6) DEFAULT NOW()", nullable=true)
     */
    private $lastLogin;

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }


    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $phone;

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @ORM\Column(type="string")
     */
    private $role;

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        if (!in_array($role, ['ROLE_MEMBER', 'ROLE_ADMIN', 'ROLE_MASTER'])) {
            throw new \InvalidArgumentException("접근 권한이 없습니다.");
        }
        $this->role = $role;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tmpPassword;

    public function getTmpPassword()
    {
        return $this->tmpPassword;
    }

    public function setTmpPassword($tmpPassword)
    {
        $this->tmpPassword = $tmpPassword;
    }

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default":1, "unsigned":true})
     */
    private $visit;

    public function getVisit()
    {
        return $this->visit;
    }

    public function setVisit($visit)
    {
        $this->visit = $visit;
    }

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $introduce;

    public function getIntroduce()
    {
        return $this->introduce;
    }

    public function setIntroduce($introduce)
    {
        $this->introduce = $introduce;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $memberFilePath;

    public function getMemberFilePath()
    {
        return $this->memberFilePath;
    }

    public function setMemberFilePath($memberFilePath)
    {
        $this->memberFilePath = $memberFilePath;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $memberFileName;

    public function getMemberFileName()
    {
        return $this->memberFileName;
    }

    public function setMemberFileName($memberFileName)
    {
        $this->memberFileName = $memberFileName;
    }

}
