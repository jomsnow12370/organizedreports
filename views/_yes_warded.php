<?php
// Example variable initialization if not yet set
$mun = isset($_GET['mun']) ? $_GET['mun'] : null;
$brgy = isset($_GET['brgy']) ? $_GET['brgy'] : null;


// Get total counts for summary cards
$total_barangays = get_value("SELECT COUNT(*) FROM barangays WHERE id IS NOT NULL $munquery $brgyquery")[0];

$total_individuals = get_value("SELECT COUNT(*) 
    FROM v_info 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 $munquery $brgyquery")[0];

   
$family_members = get_array("SELECT
    v_lname,
    COUNT(*) + 
    (
        SELECT COUNT(*)
        FROM v_info vi
        INNER JOIN barangays b ON b.id = vi.barangayId
        INNER JOIN household_warding hw ON hw.mem_v_id = vi.v_id
        WHERE vi.record_type = 1
        AND vi.v_mname = t1.v_lname
        $munquery $brgyquery
    ) AS total
FROM
    v_info AS t1
    INNER JOIN household_warding ON household_warding.mem_v_id = t1.v_id
    INNER JOIN barangays ON barangays.id = t1.barangayId
WHERE
    t1.record_type = 1
    $munquery $brgyquery
GROUP BY
    v_lname
HAVING
    COUNT(*) > 10
ORDER BY
    total DESC LIMIT 50");

    $families = get_array("SELECT
    v_lname,
    COUNT(*) + 
    (
        SELECT COUNT(*)
        FROM v_info vi
        INNER JOIN barangays b ON b.id = vi.barangayId
        INNER JOIN household_warding hw ON hw.mem_v_id = vi.v_id
        WHERE vi.record_type = 1
        AND vi.v_mname = t1.v_lname
        $munquery $brgyquery
    ) AS total
FROM
    v_info AS t1
    INNER JOIN household_warding ON household_warding.mem_v_id = t1.v_id
    INNER JOIN barangays ON barangays.id = t1.barangayId
WHERE
    t1.record_type = 1
    $munquery $brgyquery
GROUP BY
    v_lname
HAVING
    COUNT(*) > 10
ORDER BY
    total DESC LIMIT 50");


$total_warded = get_value("SELECT COUNT(*) 
    FROM v_info 
    INNER JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 $munquery $brgyquery")[0];

$male_warded = get_value("SELECT COUNT(*) 
    FROM v_info 
    INNER JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 
    AND v_gender = 'M' $munquery $brgyquery")[0];

$female_warded = get_value("SELECT COUNT(*) 
    FROM v_info 
    INNER JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 
    AND v_gender = 'F' $munquery $brgyquery")[0];

// Calculate percentages
$percent_warded = $total_individuals > 0 ? ($total_warded / $total_individuals) * 100 : 0;
$percent_male = $total_warded > 0 ? ($male_warded / $total_warded) * 100 : 0;
$percent_female = $total_warded > 0 ? ($female_warded / $total_warded) * 100 : 0;

?>

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
                            - Warded Voters
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

<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-success fw-bold">Total Warded</div>
                    <div><i class="fa fa-user-check text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo $total_warded?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: <?php echo round($percent_warded, 1); ?>%"
                        aria-valuenow="<?php echo $total_warded; ?>" aria-valuemin="0"
                        aria-valuemax="<?php echo $total_individuals; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($percent_warded, 1); ?>% voters assigned out of
                    <?php echo $total_individuals; ?> total individuals
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-primary fw-bold">Male</div>
                    <div><i class="fa fa-male text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo $male_warded; ?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar"
                        style="width: <?php echo round($percent_male, 1); ?>%"
                        aria-valuenow="<?php echo $male_warded; ?>" aria-valuemin="0"
                        aria-valuemax="<?php echo $total_warded; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($percent_male, 1); ?>% of warded voters
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-info fw-bold">Female</div>
                    <div><i class="fa fa-female text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php echo $female_warded; ?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-info" role="progressbar"
                        style="width: <?php echo round($percent_female, 1); ?>%"
                        aria-valuenow="<?php echo $female_warded; ?>" aria-valuemin="0"
                        aria-valuemax="<?php echo $total_warded; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($percent_female, 1); ?>% of warded
                </div>
            </div>
        </div>
    </div>

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
                        WHERE record_type = 1 $munquery $brgyquery")[0];
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

<?php
$status_class = $percent_warded >= 80 ? 'success' :
               ($percent_warded >= 50 ? 'primary' :
               ($percent_warded >= 30 ? 'info' :
               ($percent_warded > 0 ? 'warning' : 'danger')));

$status_icon = $percent_warded >= 80 ? 'check-circle' :
              ($percent_warded >= 50 ? 'thumbs-up' :
              ($percent_warded >= 30 ? 'info-circle' :
              ($percent_warded > 0 ? 'exclamation-triangle' : 'exclamation-circle')));

$status_message = $percent_warded >= 80 ? 'Excellent! Most voters have been assigned to households.' :
                ($percent_warded >= 50 ? 'Good progress. More than half of voters are assigned.' :
                ($percent_warded >= 30 ? 'Progress is being made, but many voters are still unassigned.' :
                ($percent_warded > 0 ? 'Significant work needed. Only a small percentage of voters are assigned.' :
                'Critical situation! No voters are assigned to households.')));
?>
<?php
// Previous code remains unchanged

// Add this function to calculate age from birthda
// Get age groups for warded voters
$age_groups = [
    '18-24' => 0,
    '25-34' => 0,
    '35-44' => 0,
    '45-54' => 0,
    '55-64' => 0,
    '65+' => 0
];

// Get birthdays of warded voters
$birthdays = get_array("SELECT v_birthday 
    FROM v_info 
    INNER JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 
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

<div class="row mb-4">
    <div class="col-lg-8 col-md-12">
        <div class="card shadow border-0 mb-4">
            <div class="card-body">
                <canvas id="ageDistributionChartWarded" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12">
        <div class="card shadow border-0 mb-4">
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
<div class="row mb-4">
    <div class="col-md-12">
        <h5 class="mb-3">Top Families Assigned to Households</h5>
    </div>

    <?php
    // Display the top 4 families as cards
    for ($i = 0; $i < min(8, count($families)); $i++):
        $family = $families[$i];
        $card_colors = ['success', 'primary', 'info', 'warning']; // Colors for each card
        $card_color = $card_colors[$i % count($card_colors)];
        $percentage = round(($family['total'] / $total_warded) * 100, 1);
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
                        aria-valuemin="0" aria-valuemax="<?php echo $total_warded; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo $percentage; ?>% of warded voters
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
                <?php echo $total_warded; ?> Voters Assigned to Households
            </h5>
            <div><?php echo $status_message; ?></div>
        </div>
    </div>
</div>
<footer></footer>
<div class="card mb-4">
    <div class="card-header bg-secondary text-white fw-bold">
        Summary of Warded Voters per Barangay
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="barangaySummaryTable" class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Barangay</th>
                        <th>Total Voters</th>
                        <th>Warded</th>
                        <th>Percent Warded</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $barangays = get_array("SELECT barangay, id FROM barangays WHERE id IS NOT NULL $munquery $brgyquery");
                    foreach ($barangays as $barangay) {
                        $brgyName = $barangay['barangay'];
                        $brgyId = $barangay['id'];

                        $total_individuals_brgy = get_value("SELECT COUNT(*) 
                            FROM v_info 
                            WHERE record_type = 1 AND barangayId = $brgyId")[0];

                        $warded_brgy = get_value("SELECT COUNT(*) 
                            FROM v_info 
                            INNER JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
                    
                            WHERE record_type = 1 
                            AND barangayId = $brgyId")[0];

                        $percent = $total_individuals_brgy > 0 ? round(($warded_brgy / $total_individuals_brgy) * 100, 1) : 0;

                        // Determine status and color
                        $status = '';
                        $status_class = '';

                        if ($percent >= 80) {
                            $status = 'Excellent';
                            $status_class = 'bg-success text-white';
                        } elseif ($percent >= 50) {
                            $status = 'Good';
                            $status_class = 'bg-primary text-white';
                        } elseif ($percent >= 30) {
                            $status = 'Moderate';
                            $status_class = 'bg-warning text-white';
                        } elseif ($percent > 0) {
                            $status = 'Low';
                            $status_class = 'bg-danger text-white';
                        } else {
                            $status = 'None';
                            $status_class = 'bg-danger text-white';
                        }
                        ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $brgyName; ?></td>
                        <td data-order="<?php echo $total_individuals_brgy; ?>"><?php echo $total_individuals_brgy; ?>
                        </td>
                        <td data-order="<?php echo $warded_brgy; ?>"><?php echo $warded_brgy; ?></td>
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