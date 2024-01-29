<?php
require_once "application/config/doctrine.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;

// EntityManager 인스턴스를 가져옵니다.
$entityManager = $GLOBALS['entityManager'];

// ConsoleRunner를 사용하여 Doctrine CLI 설정을 반환합니다.
return ConsoleRunner::createHelperSet($entityManager);
