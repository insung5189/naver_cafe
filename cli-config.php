<?php
// /cli-config.php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

// Doctrine.php 라이브러리 파일을 직접 요구합니다.
require_once "application/libraries/Doctrine.php";

// Doctrine 라이브러리의 인스턴스를 생성합니다.
$doctrine = new Doctrine();

// EntityManager 인스턴스를 가져옵니다.
$entityManager = $doctrine->em;

// ConsoleRunner를 사용하여 Doctrine CLI 설정을 반환합니다.
return ConsoleRunner::createHelperSet($entityManager);
