<?php
// MVC User Management System
require_once __DIR__ . '/controllers/UserController.php';

// Initialize controller and handle request
$controller = new UserController();
$controller->index();