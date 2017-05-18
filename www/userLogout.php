<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

session_destroy();

header('Location: /');