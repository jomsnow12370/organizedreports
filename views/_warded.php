<?php
// Example variable initialization if not yet set
$mun = isset($_GET['mun']) ? $_GET['mun'] : null;
$brgy = isset($_GET['brgy']) ? $_GET['brgy'] : null;
// These must be defined properly elsewhere in your code
// $brgyName = ...
// $munquery = ...
// $brgyquery = ...

// Get total counts for summary cards
$total_barangays = get_value("SELECT COUNT(*) FROM barangays WHERE id IS NOT NULL $munquery $brgyquery")[0];

$total_individuals = get_value("SELECT COUNT(*) 
    FROM v_info 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 $munquery $brgyquery")[0];

$total_warded = $total_individuals - get_value("SELECT COUNT(*) 
    FROM v_info 
    LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 
    AND (household_warding.mem_v_id IS NOT NULL OR head_household.fh_v_id IS NOT NULL) $munquery $brgyquery")[0];

$male_warded = get_value("SELECT COUNT(*) 
    FROM v_info 
    LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 
    AND (household_warding.mem_v_id IS NOT NULL OR head_household.fh_v_id IS NOT NULL) 
    AND v_gender = 'M' $munquery $brgyquery")[0];

$female_warded = get_value("SELECT COUNT(*) 
    FROM v_info 
    LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
    LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
    INNER JOIN barangays ON barangays.id = v_info.barangayId 
    WHERE record_type = 1 
    AND (household_warding.mem_v_id IS NOT NULL OR head_household.fh_v_id IS NOT NULL) 
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
                            <pre class="text-danger">this page is under construction</pre>
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
                    <?php echo $total_warded . '/' . $total_individuals; ?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: <?php echo round($percent_warded, 1); ?>%"
                        aria-valuenow="<?php echo $total_warded; ?>" aria-valuemin="0"
                        aria-valuemax="<?php echo $total_individuals; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($percent_warded, 1); ?>% voters assigned
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
                    <?php echo round($percent_female, 1); ?>% of warded voters
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100 border-left-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-warning fw-bold">Warded Barangays</div>
                    <div><i class="fa fa-map-marker text-gray-300"></i></div>
                </div>
                <div class="h4 mb-1 fw-bold">
                    <?php 
                    $warded_barangays = get_value("SELECT COUNT(DISTINCT barangayId) 
                        FROM v_info 
                        LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
                        LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
                        INNER JOIN barangays ON barangays.id = v_info.barangayId 
                        WHERE record_type = 1 
                        AND (household_warding.mem_v_id IS NOT NULL OR head_household.fh_v_id IS NOT NULL) $munquery $brgyquery")[0];
                    echo $warded_barangays . '/' . $total_barangays;
                    $barangay_percent = $total_barangays > 0 ? ($warded_barangays / $total_barangays) * 100 : 0;
                    ?>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-warning" role="progressbar"
                        style="width: <?php echo round($barangay_percent, 1); ?>%"
                        aria-valuenow="<?php echo $warded_barangays; ?>" aria-valuemin="0"
                        aria-valuemax="<?php echo $total_barangays; ?>">
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo round($barangay_percent, 1); ?>% of barangays warded
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$status_class = $percent_warded >= 80 ? 'danger' :
               ($percent_warded >= 50 ? 'warning' :
               ($percent_warded >= 30 ? 'info' :
               ($percent_warded > 0 ? 'primary' : 'success')));

$status_icon = $percent_warded >= 80 ? 'exclamation-circle' :
              ($percent_warded >= 50 ? 'exclamation-triangle' :
              ($percent_warded >= 30 ? 'info-circle' :
              ($percent_warded > 0 ? 'thumbs-up' : 'check-circle')));

$status_message = $percent_warded >= 80 ? 'Critical situation! Most voters have not been assigned to households.' :
                ($percent_warded >= 50 ? 'Significant work needed. More than half of voters still need to be assigned.' :
                ($percent_warded >= 30 ? 'Progress is being made, but many voters still need to be assigned to households.' :
                ($percent_warded > 0 ? 'Good progress. Only a small percentage of voters remain to be assigned.' :
                'Excellent! All voters have been assigned to households.')));
?>

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
<div class="card mb-4">
    <div class="card-header bg-secondary text-white fw-bold">
        Summary of Warded Individuals per Barangay
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Barangay</th>
                        <th>Total Individuals</th>
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
                            LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
                            LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
                            WHERE record_type = 1 
                            AND barangayId = $brgyId 
                            AND (household_warding.mem_v_id IS NOT NULL OR head_household.fh_v_id IS NOT NULL)")[0];

                        $percent = $total_individuals_brgy > 0 ? round(($warded_brgy / $total_individuals_brgy) * 100, 1) : 0;

                        // Determine status and color
                      $status = '';
                        $status_class = '';

                        if ($percent >= 80) {
                            $status = 'Excellent';
                            $status_class = 'bg-success';
                        } elseif ($percent >= 50) {
                            $status = 'Good';
                            $status_class = 'bg-primary';
                        } elseif ($percent >= 30) {
                            $status = 'Moderate';
                            $status_class = 'bg-info';
                        } elseif ($percent > 0) {
                            $status = 'Low';
                            $status_class = 'bg-warning';
                        } else {
                            $status = 'Critical';
                            $status_class = 'bg-danger';
                        }
                        ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $brgyName; ?></td>
                        <td><?php echo $total_individuals_brgy; ?></td>
                        <td><?php echo $warded_brgy; ?></td>
                        <td><?php echo $percent; ?>%</td>
                        <td><span class="badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>

                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <?php
    $barangays = get_array("SELECT barangay, id FROM barangays WHERE id IS NOT NULL $munquery $brgyquery");
    foreach ($barangays as $bkey => $bvalue) {
        $barangay = $bvalue["barangay"];
        $barangayid = $bvalue["id"];
        
        // Get count of warded individuals in this barangay
        $brgy_warded_count = get_value("SELECT COUNT(*) 
            FROM v_info 
            LEFT JOIN household_warding ON household_warding.mem_v_id = v_info.v_id 
            LEFT JOIN head_household ON head_household.fh_v_id = v_info.v_id 
            INNER JOIN barangays ON barangays.id = v_info.barangayId 
            WHERE record_type = 1 
            AND (household_warding.mem_v_id IS NOT NULL OR head_household.fh_v_id IS NOT NULL) 
            AND barangays.id = '$barangayid'")[0];
            ?>

    <?php
        // Only show barangays with warded individuals
        if ($brgy_warded_count > 0) {
    ?>

    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><?php echo $barangay; ?></h5>
                    <span class="badge bg-success"><?php echo $brgy_warded_count; ?> Warded</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-secondary">
                            <tr>
                                <th>#</th>
                                <th>Fullname</th>
                                <th>Birthday</th>
                                <th>Gender</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $warded = get_array("SELECT
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
                            AND (household_warding.mem_v_id IS NOT NULL OR head_household.fh_v_id IS NOT NULL)
                            AND barangays.id = '$barangayid'
                            ORDER BY v_lname, v_mname ASC");
                            foreach ($warded as $key => $value) {
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
                                <td><?php echo $value["barangay"] . ', ' . $value["municipality"]; ?></td>
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
        } // End if brgy_warded_count > 0
    } // End foreach barangays
    ?>
</div>