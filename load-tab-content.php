<?php
// Security check - validate the requested tab
$allowedTabs = [
    'not-warded.php',
    'yes-warded.php',
    'families.php',
    'turnouts.php',
    'warded.php'
];

if (isset($_GET['tab']) && in_array($_GET['tab'], $allowedTabs)) {
    // Include necessary files
    require_once 'config/conn.php';
    require_once 'config/f.php';
    require_once 'controller/DashboardController.php';
    require_once 'models/leaders.php';
    require_once 'models/household.php';
    
    // Load and output the requested view
    require_once 'views/' . $_GET['tab'];
} else {
    // Invalid tab requested
    echo '<div class="alert alert-danger">Invalid tab requested</div>';
}
?>