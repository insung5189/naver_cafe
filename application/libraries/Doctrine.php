<?php
// /application/libraries/Doctrine.php

defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Doctrine {
    public $em = null;

    public function __construct() {
        $isDevMode = true;
        $entitiesPath = [APPPATH . 'models/Entities'];
        $proxyDir = APPPATH . 'cache/proxies';
        $cache = null;
        $useSimpleAnnotationReader = false;
        $config = Setup::createAnnotationMetadataConfiguration($entitiesPath, $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
        
        // 데이터베이스 설정
        $conn = [
            'driver' => 'pdo_mysql', 
            'user' => 'root',
            'password' => '',
            'dbname' => 'cafe',
        ];
        
        // EntityManager 생성
        $this->em = EntityManager::create($conn, $config);
    }
}
