<?php
// /application/config/doctirne.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

if (!defined('APPPATH')) {
    define('APPPATH', realpath(__DIR__ . '/../') . '/');
}

 require_once APPPATH . '../vendor/autoload.php'; // 보통 vendor 폴더는 프로젝트 루트디렉토리에 있는 경우를 고려하여 application폴더 바깥의 vendor 폴더를 참조

$isDevMode = true;
$entitiesPath = array(APPPATH . 'models/Entities');
$config = Setup::createAnnotationMetadataConfiguration($entitiesPath, $isDevMode, null, null, false);
$config->setAutoGenerateProxyClasses(true);

// 데이터베이스 설정
$conn = array(
    'driver' => 'pdo_mysql', 
    'user' => 'root',
    'password' => '',
    'dbname' => 'cafe',
);

$entityManager = EntityManager::create($conn, $config);

$GLOBALS['entityManager'] = $entityManager;
