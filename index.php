<?php
require_once 'config/conn.php';
require_once 'config/f.php';
require_once 'controller/DashboardController.php';
require_once 'models/leaders.php';
require_once 'models/household.php';

require_once 'views/header.php';
?>

<body>
    <div class="container-fluid mt-4">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="maindashboard-tab" data-bs-toggle="tab"
                    data-bs-target="#maindashboard" type="button" role="tab" aria-controls="maindashboard"
                    aria-selected="true">Dashboard</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard"
                    type="button" role="tab" aria-controls="dashboard" aria-selected="false">Families</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="families-tab" data-bs-toggle="tab" data-bs-target="#families" type="button"
                    role="tab" aria-controls="families" aria-selected="false">Turnouts</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="households-tab" data-bs-toggle="tab" data-bs-target="#households"
                    type="button" role="tab" aria-controls="households" aria-selected="false">Households</button>
            </li>

        </ul>
        <!-- Tab content -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="maindashboard" role="tabpanel"
                aria-labelledby="maindashboard-tab">
                <!-- Dashboard content -->
                <?php require_once 'views/maindashboard.php'; ?>
            </div>

            <!-- Tab content -->
            <div class="tab-pane fade" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                <!-- Families content -->
                <?php require_once 'views/families.php'; ?>
            </div>

            <div class="tab-pane fade" id="families" role="tabpanel" aria-labelledby="families-tab">
                <!-- Turnouts content -->
                <?php require_once 'views/turnouts.php'; ?>
            </div>
            <div class="tab-pane fade" id="households" role="tabpanel" aria-labelledby="households-tab">
                <!-- Turnouts content -->
                <?php require_once 'views/households.php'; ?>
            </div>
        </div>
    </div>
</body>

<?php
// Include footer
require_once 'views/modals.php';
require_once 'views/footer.php';
?>