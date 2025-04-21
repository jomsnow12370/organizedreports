<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');
require_once 'config/conn.php';
require_once 'config/f.php';
require_once 'controller/DashboardController.php';
require_once 'models/leaders.php';
require_once 'models/household.php';

require_once 'views/header.php';
?>

<!-- Loading Screen -->
<div id="loading-screen">
    <div class="loading-container">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Loading Dashboard...</p>
    </div>
</div>

<!-- Add custom CSS for the loading screen -->
<style>
#loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    transition: opacity 0.5s;
}

.loading-container {
    text-align: center;
}

body.loaded #loading-screen {
    opacity: 0;
    pointer-events: none;
}

.content-wrapper {
    opacity: 0;
    transition: opacity 0.5s;
}

body.loaded .content-wrapper {
    opacity: 1;
}
</style>

<body>
    <div class="content-wrapper">
        <div class="container-fluid mt-4">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="maindashboard-tab" data-bs-toggle="tab"
                        data-bs-target="#maindashboard" type="button" role="tab" aria-controls="maindashboard"
                        aria-selected="true">Dashboard</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="notwarded-tab" data-bs-toggle="tab" data-bs-target="#notwarded"
                        type="button" role="tab" aria-controls="notwarded" aria-selected="false">Not-Warded</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="yeswarded-tab" data-bs-toggle="tab" data-bs-target="#yeswarded"
                        type="button" role="tab" aria-controls="yeswarded" aria-selected="false">Warded</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard"
                        type="button" role="tab" aria-controls="dashboard" aria-selected="false">Families</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="families-tab" data-bs-toggle="tab" data-bs-target="#families"
                        type="button" role="tab" aria-controls="families" aria-selected="false">Turnouts</button>
                </li>
                <!-- <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rodriguez-tab" data-bs-toggle="tab" data-bs-target="#rodriguez"
                        type="button" role="tab" aria-controls="rodriguez" aria-selected="false">Rodriguez</button>
                </li>-->
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="warded-tab" data-bs-toggle="tab" data-bs-target="#warded" type="button"
                        role="tab" aria-controls="warded" aria-selected="false">Undecided Families</button>
                </li>
            </ul>
            <!-- Tab content -->
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="maindashboard" role="tabpanel"
                    aria-labelledby="maindashboard-tab">
                    <!-- Dashboard content -->
                    <?php require_once 'views/maindashboard.php'; ?>
                </div>
                <div class="tab-pane fade" id="notwarded" role="tabpanel" aria-labelledby="notwarded-tab">
                    <!-- Turnouts content -->
                    <?php require_once 'views/not-warded.php'; ?>
                </div>
                <div class="tab-pane fade" id="yeswarded" role="tabpanel" aria-labelledby="yeswarded-tab">
                    <!-- Turnouts content -->
                    <?php //require_once 'views/yes-warded.php'; ?>
                </div>
                <!-- Tab content -->
                <div class="tab-pane fade" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                    <!-- Families content -->
                    <?php //require_once 'views/families.php'; ?>
                </div>

                <div class="tab-pane fade" id="families" role="tabpanel" aria-labelledby="families-tab">
                    <!-- Turnouts content -->
                    <?php require_once 'views/turnouts.php'; ?>
                </div>
                <div class="tab-pane fade" id="rodriguez" role="tabpanel" aria-labelledby="rodriguez-tab">
                    <!-- Turnouts content -->
                    <?php//\\require_once 'views/rodriguez.php'; ?>
                </div>

                <div class="tab-pane fade" id="warded" role="tabpanel" aria-labelledby="warded-tab">
                    <!-- Turnouts content -->
                    <?php 
                     
                     ?>
                    <?php //require_once 'views/households.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to hide loading screen once page is fully loaded -->
    <script>
    // Hide loading screen when page is fully loaded
    window.addEventListener('load', function() {
        // Small delay to ensure everything is rendered
        setTimeout(function() {
            document.body.classList.add('loaded');
        }, 500);
    });
    </script>
</body>

<?php
// Include footer
require_once 'views/modals.php';
require_once 'views/footer.php';
?>