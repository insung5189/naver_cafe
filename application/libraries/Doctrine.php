<?php
// /application/libraries/Doctrine.php

defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Doctrine {
    public $em = null;

    public function __construct() {
        // Doctrine 설정 파일 로드
        require_once APPPATH . 'config/doctrine.php';
        
        // 설정 파일에서 EntityManager 인스턴스를 가져옴
        $this->em = $GLOBALS['entityManager'];
    }
}
