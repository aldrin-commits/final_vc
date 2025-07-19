<?php
session_start();

// If not logged in, redirect
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// OPTIONAL: Restrict access by role
function require_role($role) {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== $role) {
        header("Location: ../login.php");
        exit();
    }
}
