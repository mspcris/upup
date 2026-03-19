<?php
require_once dirname(__DIR__) . '/auth.php';
doLogout();
header('Location: ../index.php');
exit;
