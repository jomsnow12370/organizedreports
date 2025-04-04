<h3 class="text-light text-center">
    2025 Warding Dashboard
    <br>
    <small>
        <i>
            <?php
            if($mun != "") {
                if(isset($_GET["brgy"]) != "") {
                    echo $brgyName . ', ';
                }
                echo $mun;
            }
            ?>
        </i>
    </small>
</h3>

<!-- Total Voters Card -->
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card card-voters" id="voterCard" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#municipalityModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Voters
                            <?php 
                            // if($mun != "") {
                            //     if($brgyId != "") {
                            //         echo " of $brgyName, $mun <br><div class='text-muted' style='font-size:16px;font-weight:600'> <i>" . count($precinct_totals) . ' Precincts</i> </div>';
                            //     } else {
                            //         echo " of $mun <br><div class='text-muted' style='font-size:16px;font-weight:600'> <i>" . count($barangay_totals) . ' Barangays</i> </div>';
                            //     }
                            // }
                            ?>
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php 
                            // Get the total number of voters
                            $total_voters = get_value("SELECT COUNT(*) from v_info INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE v_info.record_type = 1 $munquery $brgyquery");              
                            echo number_format($total_voters[0]);
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users voter-icon"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer removeonprint">
                <small>
                    <i>
                        Click to select 
                        <?php 
                        if($mun != "") {
                            echo "barangay";
                        } else {
                            echo "municipality";
                        }
                        ?>
                    </i>
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card card-voters" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Households Warded
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($head_household); ?> / 
                            <b class="text-danger">
                                <?php echo number_format(count_household($c, $munquery, $brgyquery2)); ?>
                            </b>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-home voter-icon"></i>
                    </div>
                </div>
                <!-- Category Breakdown -->
                <hr class="category-divider">
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="d-flex flex-wrap mb-2">
                            <div class="category-pill dc-pill me-2 mb-1">
                                Household Members: <?php echo number_format($household_member); ?>
                            </div>
                            <div class="category-pill mc-pill me-2 mb-1">
                                Total Warded Voters: <?php echo number_format($household_total); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">Leaders Summary</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <div class="stats-label">Municipal Coordinators</div>
                            <div class="stats-value" id="mcCount">
                                <?php echo number_format($total_mc); ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="stats-label">District Coordinators</div>
                            <div class="stats-value" id="dcCount">
                                <?php echo number_format($total_dc); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <div class="stats-label">Barangay Coordinators</div>
                            <div class="stats-value" id="bcCount">
                                <?php echo number_format($total_bc); ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="stats-label">Ward Leaders</div>
                            <div class="stats-value" id="wlCount">
                                <?php echo number_format($total_wl); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h5 class="text-light mt-4"><strong>Congressman</strong></h5>
<div class="row">
    <!-- Laynes -->
    <div class="col-lg-3">
        <div class="card card-voters mb-2" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Laynes
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($cong_totals['Laynes']['total']); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <img src="assets/images/sammy.jpg" alt="Profile" class="profile-img">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rodriguez -->
    <div class="col-lg-3">
        <div class="card card-voters mb-2" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Rodriguez
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($cong_totals['Rodriguez']['total']); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <img src="assets/images/leo.jpg" alt="Profile" class="profile-img">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alberto -->
    <div class="col-lg-3">
        <div class="card card-voters mb-2" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Alberto
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($cong_totals['Alberto']['total']); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <img src="assets/images/alberto.jpg" alt="Profile" class="profile-img">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Undecided -->
    <div class="col-lg-3">
        <div class="card card-voters mb-2" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Undecided
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($cong_totals['UndecidedCong']['total'] + $cong_blanks); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-question-circle" style="font-size:50px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h5 class="text-light mt-4"><strong>Governor</strong></h5>
<div class="row">
    <!-- Boss Te -->
    <div class="col-lg-4">
        <div class="card card-voters mb-2" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Boss Te
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($gov_totals['Bosste']['total']); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <img src="assets/images/bosste.jpg" alt="Profile" class="profile-img">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asanza -->
    <div class="col-lg-4">
        <div class="card card-voters mb-2" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Asanza
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($gov_totals['Asanza']['total']); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <img src="assets/images/asanza.jpg" alt="Profile" class="profile-img">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Undecided -->
    <div class="col-lg-4">
        <div class="card card-voters mb-2" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Undecided
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($gov_totals['UndecidedGov']['total'] + $gov_blanks); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-question-circle" style="font-size:50px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h5 class="text-light mt-4"><strong>Vice Governor</strong></h5>
<div class="row">
    <!-- Fernandez -->
    <div class="col-lg-4">
        <div class="card card-voters mb-2" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Fernandez
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($vgov_totals['Fernandez']['total']); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <img src="assets/images/obet.jpg" alt="Profile" class="profile-img">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Abundo -->
    <div class="col-lg-4">
        <div class="card card-voters mb-2" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Abundo
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($vgov_totals['Abundo']['total']); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <img src="assets/images/abundo.jpg" alt="Profile" class="profile-img">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Undecided -->
    <div class="col-lg-4">
        <div class="card card-voters mb-2" data-bs-toggle="modal" data-bs-target="#householdModal">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Undecided
                        </div>
                        <div class="h5 mb-0 fw-bold">
                            <?php echo number_format($vgov_totals['UndecidedVGov']['total'] + $vgov_blanks); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-question-circle" style="font-size:50px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>