<?php
require_once __DIR__ . '/config.php';
unset($_SESSION['student_id'], $_SESSION['student_name']);
redirect('student_login.php');
