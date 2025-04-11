<?php
// Example variable initialization if not yet set
$mun = isset($_GET['mun']) ? $_GET['mun'] : null;
$brgy = isset($_GET['brgy']) ? $_GET['brgy'] : null;
// These must be defined properly elsewhere in your code
// $brgyName = ...
// $munquery = ...
// $brgyquery = ...

// Get total counts for summary cards
$total_barangays = get_value("SELECT COUNT(*) FROM barangays WHERE id IS NOT NULL $munquery $brgyquery2")[0];

$total_individuals = get_value("SELECT COUNT(*) 
    FROM v_info 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 $munquery $brgyquery")[0];

   
$families = get_array("SELECT
    v_lname,
    COUNT(*) + 
    (
        SELECT COUNT(*)
        FROM v_info vi
        INNER JOIN barangays b ON b.id = vi.barangayId
        LEFT JOIN household_warding hw ON hw.mem_v_id = vi.v_id
        LEFT JOIN head_household hh ON hh.fh_v_id = vi.v_id
        WHERE vi.record_type = 1
        AND vi.v_mname = t1.v_lname
        AND hw.mem_v_id IS NULL
        AND hh.fh_v_id IS NULL
        $munquery $brgyquery
    ) AS total
FROM
    v_info AS t1
    LEFT JOIN household_warding ON household_warding.mem_v_id = t1.v_id
    LEFT JOIN head_household ON head_household.fh_v_id = t1.v_id
    INNER JOIN barangays ON barangays.id = t1.barangayId
WHERE
    t1.record_type = 1
    AND household_warding.mem_v_id IS NULL
    AND head_household.fh_v_id IS NULL
    $munquery $brgyquery
GROUP BY
    v_lname
HAVING
    COUNT(*) > 10
ORDER BY
    total DESC LIMIT 50");


$total_not_warded = get_value("SELECT COUNT(*) 
    FROM v_info 
    LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 
    AND household_warding.mem_v_id IS NULL 
    AND head_household.fh_v_id IS NULL $munquery $brgyquery")[0];

$male_not_warded = get_value("SELECT COUNT(*) 
    FROM v_info 
    LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 
    AND household_warding.mem_v_id IS NULL 
    AND head_household.fh_v_id IS NULL 
    AND v_gender = 'M' $munquery $brgyquery")[0];

$female_not_warded = get_value("SELECT COUNT(*) 
    FROM v_info 
    LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 
    AND household_warding.mem_v_id IS NULL 
    AND head_household.fh_v_id IS NULL 
    AND v_gender = 'F' $munquery $brgyquery")[0];

// Calculate percentages
$percent_not_warded = $total_individuals > 0 ? ($total_not_warded / $total_individuals) * 100 : 0;
$percent_male = $total_not_warded > 0 ? ($male_not_warded / $total_not_warded) * 100 : 0;
$percent_female = $total_not_warded > 0 ? ($female_not_warded / $total_not_warded) * 100 : 0;

?>

<!-- Header Section -->
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4 shadow border-0">
            <div class="card-body bg-dark text-white rounded">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2 fw-bold">
                            <?php
                            if ($mun) {
                                if ($brgy) {
                                    echo $brgyName . ', ' . $mun;
                                } else {
                                    echo $mun;
                                }
                            } else {
                                echo "Household Survey Province-wide";
                            }
                            ?>
                            - Non-Warded Voters
                        </h2>
                        <button class="btn btn-sm btn-outline-success mt-2 removeonprint" data-bs-toggle="modal"
                            data-bs-target="#municipalityModal">
                            <i class="fa fa-repeat"></i> Select Address
                        </button>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-sm btn-outline-primary removeonprint" id="printBtn"
                            onclick="window.print()">
                            <i class="fa fa-print"></i> Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <!-- Total Non-Warded Card -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-danger fw-bold">Total Non-Warded</div>
                    <div><i class="fa fa-user-times text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo $total_not_warded?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-danger" role="progressbar"
                        style="width: <?php echo round($percent_not_warded, 1); ?>%"
                        aria-valuenow="<?php echo $total_not_warded; ?>" aria-valuemin="0"
                        aria-valuemax="<?php echo $total_individuals; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($percent_not_warded, 1); ?>% voters not assigned out of
                    <?php echo $total_individuals; ?> total individuals
                </div>
            </div>
        </div>
    </div>

    <!-- Male Non-Warded Card -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-primary fw-bold">Male</div>
                    <div><i class="fa fa-male text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo $male_not_warded; ?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar"
                        style="width: <?php echo round($percent_male, 1); ?>%"
                        aria-valuenow="<?php echo $male_not_warded; ?>" aria-valuemin="0"
                        aria-valuemax="<?php echo $total_not_warded; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($percent_male, 1); ?>% of non-warded voters
                </div>
            </div>
        </div>
    </div>

    <!-- Female Non-Warded Card -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-info fw-bold">Female</div>
                    <div><i class="fa fa-female text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo $female_not_warded; ?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-info" role="progressbar"
                        style="width: <?php echo round($percent_female, 1); ?>%"
                        aria-valuenow="<?php echo $female_not_warded; ?>" aria-valuemin="0"
                        aria-valuemax="<?php echo $total_not_warded; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($percent_female, 1); ?>% of non-warded
                </div>
            </div>
        </div>
    </div>

    <!-- Barangay Coverage Card -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-warning fw-bold">Affected Barangays</div>
                    <div><i class="fa fa-map-marker text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php 
                    $affected_barangays = get_value("SELECT COUNT(DISTINCT barangayId) 
                        FROM v_info 
                        LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
                        LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
                        INNER JOIN barangays ON barangays.id = v_info.barangayId 
                        WHERE record_type = 1 
                        AND household_warding.mem_v_id IS NULL 
                        AND head_household.fh_v_id IS NULL $munquery $brgyquery")[0];
                    echo $affected_barangays . '/' . $total_barangays;
                    $barangay_percent = $total_barangays > 0 ? ($affected_barangays / $total_barangays) * 100 : 0;
                    ?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-warning" role="progressbar"
                        style="width: <?php echo round($barangay_percent, 1); ?>%"
                        aria-valuenow="<?php echo $affected_barangays; ?>" aria-valuemin="0"
                        aria-valuemax="<?php echo $total_barangays; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($barangay_percent, 1); ?>% of barangays affected
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Summary Alert -->
<?php
$status_class = $percent_not_warded >= 80 ? 'danger' :
               ($percent_not_warded >= 50 ? 'warning' :
               ($percent_not_warded >= 30 ? 'info' :
               ($percent_not_warded > 0 ? 'primary' : 'success')));

$status_icon = $percent_not_warded >= 80 ? 'punctuation-circle' :
              ($percent_not_warded >= 50 ? 'thumbs-up' :
              ($percent_not_warded >= 30 ? 'info-circle' :
              ($percent_not_warded > 0 ? 'exclamation-triangle' : 'exclamation-circle')));

$status_message = $percent_not_warded >= 80 ? 'Oh Sheesh! Most voters have not been assigned to households.' :
                ($percent_not_warded >= 50 ? 'Deymm!. More than half of voters are not assigned.' :
                ($percent_not_warded >= 30 ? 'This is bad, many voters are still unwarded.' :
                ($percent_not_warded > 0 ? 'Significant work needed. Only a small percentage of voters are warded.' :
                'Critical situation! No voters are assigned to households.')));
?>
<?php
// Previous code remains unchanged

// Add this function to calculate age from birthday
function calculate_age($birthday) {
    return floor((time() - strtotime($birthday)) / 31556926); // 31556926 is seconds in a year
}

// Get age groups for non-warded voters
$age_groups = [
    '18-24' => 0,
    '25-34' => 0,
    '35-44' => 0,
    '45-54' => 0,
    '55-64' => 0,
    '65+' => 0
];

// Get birthdays of non-warded voters
$birthdays = get_array("SELECT v_birthday 
    FROM v_info 
    LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 
    AND household_warding.mem_v_id IS NULL 
    AND head_household.fh_v_id IS NULL 
    AND v_birthday IS NOT NULL $munquery $brgyquery");

// Calculate age groups
foreach ($birthdays as $record) {
    $age = calculate_age($record['v_birthday']);
    
    if ($age >= 0 && $age <= 24) {
        $age_groups['18-24']++;
    } elseif ($age >= 25 && $age <= 34) {
        $age_groups['25-34']++;
    } elseif ($age >= 35 && $age <= 44) {
        $age_groups['35-44']++;
    } elseif ($age >= 45 && $age <= 54) {
        $age_groups['45-54']++;
    } elseif ($age >= 55 && $age <= 64) {
        $age_groups['55-64']++;
    } elseif ($age >= 65) {
        $age_groups['65+']++;
    }
}

// Calculate percentages
$total_with_age = array_sum($age_groups);
$age_groups_percentage = [];
foreach ($age_groups as $group => $count) {
    $age_groups_percentage[$group] = $total_with_age > 0 ? round(($count / $total_with_age) * 100, 1) : 0;
}

// Prepare data for Chart.js
$age_labels = json_encode(array_keys($age_groups));
$age_counts = json_encode(array_values($age_groups));
$age_percentages = json_encode(array_values($age_groups_percentage));
?>

<!-- After the Status Summary Alert and before the Barangay summary table -->
<div class="row mb-4">
    <div class="col-lg-8 col-md-12">
        <div class="card shadow border-0 mb-4">
            <!-- <div class="card-header bg-secondary text-white fw-bold">
                Age Distribution of Non-Warded Voters
            </div> -->
            <div class="card-body">
                <canvas id="ageDistributionChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12">
        <div class="card shadow border-0 mb-4">
            <!-- <div class="card-header bg-secondary text-white fw-bold">
                Age Groups Breakdown
            </div> -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-primary-outline">
                            <tr>
                                <th>Age Group</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($age_groups as $group => $count): ?>
                            <tr>
                                <td><?php echo $group; ?></td>
                                <td><?php echo $count; ?></td>
                                <td><?php echo $age_groups_percentage[$group]; ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>Total</th>
                                <th><?php echo $total_with_age; ?></th>
                                <th>100%</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Family Cards Row - Add this after the Summary Cards section -->
<div class="row mb-4">
    <div class="col-md-12">
        <h5 class="mb-3">Top Families Not Assigned to Households</h5>
    </div>

    <?php
    // Display the top 4 families as cards
    for ($i = 0; $i < min(8, count($families)); $i++):
        $family = $families[$i];
        $card_colors = ['danger', 'primary', 'info', 'warning']; // Colors for each card
        $card_color = $card_colors[$i % count($card_colors)];
        $percentage = round(($family['total'] / $total_not_warded) * 100, 1);
    ?>
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-<?php echo $card_color; ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-<?php echo $card_color; ?> fw-bold">
                        <?php echo $family['v_lname']; ?></div>
                    <div><i class="fa fa-users text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo $family['total']; ?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-<?php echo $card_color; ?>" role="progressbar"
                        style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $family['total']; ?>"
                        aria-valuemin="0" aria-valuemax="<?php echo $total_not_warded; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo $percentage; ?>% of non-warded voters
                </div>
            </div>
        </div>
    </div>
    <?php endfor; ?>
</div>

<div class="alert alert-<?php echo $status_class; ?> mb-4">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="fa fa-<?php echo $status_icon; ?> fa-2x"></i>
        </div>
        <div>
            <h5 class="fw-bold text-<?php echo $status_class; ?> mb-1">
                <?php echo $total_not_warded; ?> Voters Not Assigned to Households
            </h5>
            <div><?php echo $status_message; ?></div>
        </div>
    </div>
</div>
<footer></footer>
<div class="card mb-4">
    <div class="card-header bg-secondary text-white fw-bold">
        Summary of Non-Warded Voters per Barangay
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="barangaySummaryTable" class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Barangay</th>
                        <th>Total Voters</th>
                        <th>Non-Warded</th>
                        <th>Percent Not Warded</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $barangays = get_array("SELECT barangay, id FROM barangays WHERE id IS NOT NULL $munquery $brgyquery2");
                    foreach ($barangays as $barangay) {
                        $brgyName = $barangay['barangay'];
                        $brgyId = $barangay['id'];

                        $total_individuals_brgy = get_value("SELECT COUNT(*) 
                            FROM v_info 
                            WHERE record_type = 1 AND barangayId = $brgyId")[0];

                        $not_warded_brgy = get_value("SELECT COUNT(*) 
                            FROM v_info 
                            LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
                            LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
                            WHERE record_type = 1 
                            AND barangayId = $brgyId 
                            AND household_warding.mem_v_id IS NULL 
                            AND head_household.fh_v_id IS NULL")[0];

                        $percent = $total_individuals_brgy > 0 ? round(($not_warded_brgy / $total_individuals_brgy) * 100, 1) : 0;

                        // Determine status and color
                        $status = '';
                        $status_class = '';

                        if ($percent >= 80) {
                            $status = 'Very Low Warding Rate';
                            $status_class = 'bg-danger text-white';
                        } elseif ($percent >= 50) {
                            $status = 'Low Warding Rate';
                            $status_class = 'bg-danger text-white';
                        } elseif ($percent >= 30) {
                            $status = 'Medium Warding Rate';
                            $status_class = 'bg-warning text-white';
                        } elseif ($percent > 0) {
                            $status = 'High Warding Rate';
                            $status_class = 'bg-primary text-white';
                        } else {
                            $status = 'Excellent';
                            $status_class = 'bg-success text-white';
                        }
                        ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $brgyName; ?></td>
                        <td data-order="<?php echo $total_individuals_brgy; ?>"><?php echo $total_individuals_brgy; ?>
                        </td>
                        <td data-order="<?php echo $not_warded_brgy; ?>"><?php echo $not_warded_brgy; ?></td>
                        <td data-order="<?php echo $percent; ?>"><?php echo $percent; ?>%</td>
                        <td data-order="<?php echo $percent; ?>"><span
                                class="badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php 
// if (isset($_GET["mun"]) != "" && isset($_GET["brgy"]) != "") {
  ?>
<div class="row">
    <?php
    $barangays = get_array("SELECT barangay, id FROM barangays WHERE id IS NOT NULL $munquery $brgyquery2");
    foreach ($barangays as $bkey => $bvalue) {
        $barangay = $bvalue["barangay"];
        $barangayid = $bvalue["id"];
        
        // Get count of non-warded individuals in this barangay
        $brgy_not_warded_count = get_value("SELECT COUNT(*) 
            FROM v_info 
            LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
            LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
            INNER JOIN barangays ON barangays.id = v_info.barangayId 
            WHERE record_type = 1 
            AND household_warding.mem_v_id IS NULL 
            AND head_household.fh_v_id IS NULL 
            AND barangays.id = '$barangayid'")[0];
            ?>

    <?php
        // Only show barangays with non-warded individuals
        if ($brgy_not_warded_count > 0) {
    ?>
    <footer> </footer>
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><?php echo $barangay; ?></h5>
                    <span class="badge bg-danger"><?php echo $brgy_not_warded_count; ?> Non-Warded</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-secondary">
                            <tr>
                                <th>#</th>
                                <th>Lastname</th>
                                <th>Name</th>
                                <th>Birthday</th>
                                <th>Gender</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $not_warded = get_array("SELECT
                                v_lname, v_fname, v_mname,
                                municipality,
                                barangay,
                                v_birthday,
                                v_gender, v_info.v_id
                            FROM
                                v_info
                            LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id
                            LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id
                            INNER JOIN barangays ON barangays.id = v_info.barangayId
                            WHERE
                                record_type = 1
                            AND household_warding.mem_v_id IS NULL
                            AND head_household.fh_v_id IS NULL
                            AND barangays.id = '$barangayid'
                            ORDER BY v_lname, v_mname ASC");
                            foreach ($not_warded as $key => $value) {
                            ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td><?php echo $value["v_lname"]; ?></td>
                                <td><?php echo $value["v_fname"] . ' ' . $value["v_mname"]; ?></td>
                                <td><?php echo $value["v_birthday"]; ?></td>
                                <td><?php
                                if($value["v_gender"] == "" || $value["v_gender"] == null){
                                    echo "N/A";
                                }
                                else{
                                    echo $value["v_gender"];
                                }
                                ?></td>
                                <td style="font-size:small" width="150px">
                                    <?php echo $value["barangay"] . ', ' . $value["municipality"]; ?></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php 
        } // End if brgy_not_warded_count > 0
    } // End foreach barangays
    ?>
</div>
<?php

//}
?>