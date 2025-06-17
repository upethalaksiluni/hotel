<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle DELETE requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    if (isset($_GET['id'])) {
        $facility_id = (int)$_GET['id'];
        
        $stmt = $conn->prepare("DELETE FROM room_facilities WHERE id = ?");
        $stmt->bind_param("i", $facility_id);
        
        if ($stmt->execute()) {
            header('Location: facilities.php?success=3');
        } else {
            header('Location: facilities.php?error=Delete failed');
        }
    }
    exit();
}

// Handle POST requests (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $icon = trim($_POST['icon']);
    $description = trim($_POST['description']);
    
    if (empty($name) || empty($icon)) {
        header('Location: facilities.php?error=Name and icon are required');
        exit();
    }
    
    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO room_facilities (name, icon, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $icon, $description);
        
        if ($stmt->execute()) {
            header('Location: facilities.php?success=1');
        } else {
            header('Location: facilities.php?error=Add failed');
        }
    }
    elseif ($_POST['action'] === 'edit') {
        $facility_id = (int)$_POST['facility_id'];
        
        $stmt = $conn->prepare("UPDATE room_facilities SET name = ?, icon = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $icon, $description, $facility_id);
        
        if ($stmt->execute()) {
            header('Location: facilities.php?success=2');
        } else {
            header('Location: facilities.php?error=Update failed');
        }
    }
}

exit();
?>