<?php
// /application/config/doctirne.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

// APPPATH 상수가 이미 정의되었는지 확인
if (!defined('APPPATH')) {
    // 상수가 정의되지 않았다면, index.php의 로직을 참고하여 정의
    $application_folder = $_SERVER['DOCUMENT_ROOT']; // 실제 애플리케이션 폴더 경로로 설정
    define('APPPATH', realpath($application_folder) . DIRECTORY_SEPARATOR);
}

require_once APPPATH . 'vendor/autoload.php';

$isDevMode = true;
$entitiesPath = array(APPPATH . 'application/models/Entities');
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
