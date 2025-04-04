<?php
/**
 * Barangay Survey Dashboard
 * 
 * This file displays statistics and reports about barangay survey submissions
 * including submission rates, household coverage, and detailed breakdowns.
 */

// Fetch all barangays data
$r = get_array("SELECT barangay, households, id FROM barangays WHERE id IS NOT NULL $munquery $brgyquery2");

// Initialize counters for turnouts
$total_barangays = count($r);
$submitted_barangays = 0;
$total_households = 0;
$submitted_households = 0;

// Count barangays with submitted households and calculate totals
foreach ($r as $value) {
    $brgyid = $value[2];
    $total_households += intval($value[1]);
    
    // Get households count for this specific barangay
    $wardedhouseholds = get_value("SELECT COUNT(*) FROM head_household
        INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id
        INNER JOIN barangays ON barangays.id = v_info.barangayId
        WHERE v_info.record_type = 1 AND barangayId = '$brgyid'")[0];
    
    $submitted_households += intval($wardedhouseholds);
    
    if ($wardedhouseholds > 0) {
        $submitted_barangays++;
    }
}

// Calculate percentages
$barangay_submission_percent = $total_barangays > 0 ? ($submitted_barangays / $total_barangays) * 100 : 0;
$household_submission_percent = $total_households > 0 ? ($submitted_households / $total_households) * 100 : 0;
?>

<!-- Header Section -->
<div class="card mb-4">
    <div class="card-body py-3 bg-dark">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="mb-1">
                            <?php
                            if (isset($_GET["mun"])) {
                                if (isset($_GET["brgy"])) {
                                    echo $brgyName . ', ' . $mun;
                                } else {
                                    echo $mun;
                                }
                            } else {
                                echo "Barangay Survey Dashboard";
                            }
                            ?>
                        </h2>
                        <button class="btn btn-sm btn-outline-success removeonprint" 
                                style="cursor: pointer;" 
                                data-bs-toggle="modal"
                                data-bs-target="#municipalityModal">
                            <i class="fa fa-repeat"></i> Select Address
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <!-- Button placeholder -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex justify-content-md-end">
                    <button class="btn btn-sm btn-outline-primary" id="printBtn" onclick="window.print()">
                        <i class="fa fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Barangay Submission Card -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-success fw-bold">Barangay Submission</div>
                    <div><i class="fa fa-map-marker text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo $submitted_barangays . '/' . $total_barangays; ?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-success" 
                         role="progressbar"
                         style="width: <?php echo round($barangay_submission_percent, 1); ?>%"
                         aria-valuenow="<?php echo $submitted_barangays; ?>" 
                         aria-valuemin="0"
                         aria-valuemax="<?php echo $total_barangays; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($barangay_submission_percent, 1); ?>% submitted
                </div>
            </div>
        </div>
    </div>

    <!-- Household Submission Card -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-primary fw-bold">Household Submission</div>
                    <div><i class="fa fa-home text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo $submitted_households . '/' . $total_households; ?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-primary" 
                         role="progressbar"
                         style="width: <?php echo round($household_submission_percent, 1); ?>%"
                         aria-valuenow="<?php echo $submitted_households; ?>" 
                         aria-valuemin="0"
                         aria-valuemax="<?php echo $total_households; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($household_submission_percent, 1); ?>% of households covered
                </div>
            </div>
        </div>
    </div>

    <!-- Average Households Card -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-info fw-bold">Avg. Households/Brgy</div>
                    <div><i class="fa fa-calculator text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo round($total_households / ($total_barangays > 0 ? $total_barangays : 1), 1); ?>
                </div>
                <div class="small text-muted">
                    Average number of households per barangay
                </div>
            </div>
        </div>
    </div>

    <!-- Last Updated Card -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-warning fw-bold">Last Updated</div>
                    <div><i class="fa fa-calendar text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo date('M d, Y'); ?>
                </div>
                <div class="small text-muted">
                    As of today
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Message -->
<?php
$status_class = $barangay_submission_percent > 75 ? 'success' : 
               ($barangay_submission_percent > 50 ? 'info' : 
               ($barangay_submission_percent > 25 ? 'warning' : 'danger'));
$status_icon = $barangay_submission_percent > 75 ? 'check-circle' : 
              ($barangay_submission_percent > 50 ? 'info-circle' : 
              ($barangay_submission_percent > 25 ? 'exclamation-triangle' : 'exclamation-circle'));
$status_message = $barangay_submission_percent > 75 ? 'Great progress! Most barangays have submitted their data.' :
                ($barangay_submission_percent > 50 ? 'Good progress. More than half of barangays have submitted data.' :
                ($barangay_submission_percent > 25 ? 'Progress is ongoing. Follow up with remaining barangays.' :
                'Submission rate is low. Immediate follow-up recommended.'));
?>
<div class="alert alert-<?php echo $status_class; ?> mb-4">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="fa fa-<?php echo $status_icon; ?> fa-2x"></i>
        </div>
        <div>
            <h5 class="fw-bold text-<?php echo $status_class; ?> mb-1">
                <?php echo $submitted_barangays . '/' . $total_barangays; ?> Barangays submitted
            </h5>
            <div><?php echo $status_message; ?></div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card mb-4">
    <div class="card-header bg-dark py-3">
        <h5 class="mb-0 fw-bold">Barangay Submission Report</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="turnoutTable">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Municipality</th>
                        <th>Barangay</th>
                        <th>Voters</th>
                        <th>Households</th>
                        <th>Completion</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $table_total_households = 0;
                    $table_submitted_households = 0;
                    
                    foreach ($r as $key => $value) {
                        $brgyid = $value[2];
                        $brgy_total_households = intval($value[1]);
                        $table_total_households += $brgy_total_households;
                        
                        // Get submitted households for this barangay
                        $wardedhouseholds = get_value("SELECT COUNT(*) FROM head_household
                            INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id
                            INNER JOIN barangays ON barangays.id = v_info.barangayId
                            WHERE v_info.record_type = 1 AND barangayId = '$brgyid'")[0];
                        
                        $brgy_submitted_households = intval($wardedhouseholds);
                        $table_submitted_households += $brgy_submitted_households;
                        
                        // Get voter information
                        $voters = get_value("SELECT COUNT(*),municipality FROM v_info
                            INNER JOIN barangays ON barangays.id = v_info.barangayId
                            WHERE record_type = 1 AND barangayId = '$brgyid'");
                        
                        // Calculate completion percentage
                        $completion_percent = $brgy_total_households > 0 ? 
                            round(($brgy_submitted_households / $brgy_total_households) * 100, 1) : 0;
                        
                        // Determine status
                        $status_class = "";
                        $status_text = "";
                        if ($brgy_submitted_households == 0) {
                            $status_class = "danger";
                            $status_text = "Not Started";
                        } elseif ($completion_percent < 30) {
                            $status_class = "warning";
                            $status_text = "Just Started";
                        } elseif ($completion_percent < 70) {
                            $status_class = "info";
                            $status_text = "In Progress";
                        } elseif ($completion_percent < 100) {
                            $status_class = "primary";
                            $status_text = "Almost Complete";
                        } else {
                            $status_class = "success";
                            $status_text = "Complete";
                        }
                    ?>
                    <tr class="<?php echo $brgy_submitted_households == 0 ? "table-danger" : ""; ?>">
                        <td><?php echo $key + 1; ?></td>
                        <td><?php echo $voters[1]; ?></td>
                        <td><?php echo $value[0]; ?></td>
                        <td><?php echo $voters[0]; ?></td>
                        <td><?php echo $brgy_submitted_households . '/' . $brgy_total_households; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                    <div class="progress-bar bg-<?php echo $status_class; ?>"
                                         role="progressbar"
                                         style="width: <?php echo $completion_percent; ?>%"
                                         aria-valuenow="<?php echo $completion_percent; ?>"
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="text-muted small"><?php echo $completion_percent; ?>%</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                    </tr>
                    <?php
                    }
                    
                    // Update totals if needed
                    if ($table_total_households != $total_households || $table_submitted_households != $submitted_households) {
                        $total_households = $table_total_households;
                        $submitted_households = $table_submitted_households;
                        $household_submission_percent = $total_households > 0 ? 
                            ($submitted_households / $total_households) * 100 : 0;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="4" class="text-end">Overall:</td>
                        <td><?php echo $submitted_households . '/' . $total_households; ?></td>
                        <td colspan="2">
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" 
                                         role="progressbar"
                                         style="width: <?php echo round($household_submission_percent, 1); ?>%"
                                         aria-valuenow="<?php echo round($household_submission_percent, 1); ?>"
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="text-muted small">
                                    <?php echo round($household_submission_percent, 1); ?>%
                                </span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>