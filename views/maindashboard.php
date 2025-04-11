<!-- Header Section -->
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4 shadow border-0">
            <div class="card-body bg-dark text-white rounded">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2 fw-bold">
                            <?php
                                if (isset($_GET["mun"])) {
                                    if (isset($_GET["brgy"])) {
                                        echo $brgyName . ', ' . $mun;
                                    } else {
                                        echo $mun;
                                    }
                                } else {
                                    echo "Household Survey Province-wide";
                                }
                                ?>
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

<!-- Statistics Cards Row -->
<div class="row mb-4">
    <!-- Total Voters Card -->
    <div class="col-md-4 col-lg-4 mb-3">
        <div class="card h-100 " id="voterCard" style="cursor: pointer;" data-bs-toggle="modal"
            data-bs-target="#municipalityModal">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-xs text-uppercase text-primary fw-bold">
                        Voters
                    </div>
                    <div><i class="fas fa-people voter-icon"></i></div>
                </div>
                <div class="text-center fw-bold" style="font-size: 3rem;">
                    <?php
// Get the total number of voters for this municipality/barangay
$total_voters = get_value("SELECT COUNT(*) from v_info INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE v_info.record_type = 1 $munquery $brgyquery");

// Get the total number of voters province-wide
$province_total = get_value("SELECT COUNT(*) from v_info INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE v_info.record_type = 1");

// Calculate percentage
$percentage = ($total_voters[0] / $province_total[0]) * 100;

echo number_format($total_voters[0]);
?>
                </div>
                <div class=" text-primary text-center" style="font-size: 0.9rem;">
                    <?php echo number_format($percentage, 2) . '% of voters province-wide.'; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 col-lg-8 mb-3">
        <div class="card h-100">
            <!-- <div class="card-header bg-secondary">
                <h6 class="mb-0 fw-bold">Leaders Summary</h6>
            </div> -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <div class="text-xs text-uppercase text-primary fw-bold">Municipal Coordinators</div>
                                <div><i class="fas fa-user-tie text-gray-300"></i></div>
                            </div>
                            <div class="h5 mb-0 fw-bold" id="mcCount">
                                <?php echo number_format($total_mc); ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <div class="text-xs text-uppercase text-danger fw-bold">District Coordinators</div>
                                <div><i class="fas fa-user-friends text-gray-300"></i></div>
                            </div>
                            <div class="h5 mb-0 fw-bold" id="dcCount">
                                <?php echo number_format($total_dc); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <div class="text-xs text-uppercase text-success fw-bold">Barangay Coordinators</div>
                                <div><i class="fas fa-users-cog text-gray-300"></i></div>
                            </div>
                            <div class="h5 mb-0 fw-bold" id="bcCount">
                                <?php echo number_format($total_bc); ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <div class="text-xs text-uppercase text-warning fw-bold">Ward Leaders</div>
                                <div><i class="fas fa-user-shield text-gray-300"></i></div>
                            </div>
                            <div class="h5 mb-0 fw-bold" id="wlCount">
                                <?php echo number_format($total_wl); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Households Warded Card -->
    <!-- Households Warded Card -->
    <div class="col-md-6 col-lg-6 mb-3">
        <div class="card h-100 shadow border-0 " data-bs-toggle="modal" data-bs-target="#householdModal"
            style="cursor: pointer;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-uppercase text-success fw-bold fs-6">
                        Households Warded
                    </div>
                    <div><i class="fas fa-home text-success fs-4"></i></div>
                </div>

                <?php
            $total_households = count_household($c, $munquery, $brgyquery2);
            $warded_percent = $total_households > 0 ? ($head_household / $total_households) * 100 : 0;
            ?>

                <div class="text-center mb-2">
                    <span class="fw-bold display-6 text-success"><?php echo number_format($head_household); ?></span>
                    <span class="fw-bold text-muted fs-5">/ <?php echo number_format($total_households); ?></span>
                </div>

                <div class="text-center mb-2">
                    <span class="badge bg-success fs-6 px-3 py-2"><?php echo round($warded_percent, 1); ?>%
                        Warded</span>
                </div>

                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: <?php echo $warded_percent; ?>%;" aria-valuenow="<?php echo $warded_percent; ?>"
                        aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warded Card -->
    <div class="col-md-6 col-lg-6 mb-3">
        <div class="card h-100 shadow border-0 " style="cursor: default;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-uppercase text-warning fw-bold fs-6">
                        Total Warded Voters
                    </div>
                    <div><i class="fas fa-user-friends text-warning fs-4"></i></div>
                </div>

                <div class="text-center mb-3">
                    <span class="fw-bold display-6 text-warning">
                        <?php echo number_format($household_total); ?>
                    </span>
                </div>

                <div class="text-center">
                    <span class="badge bg-warning fs-6 px-3 py-2">
                        Warded Voters
                    </span>
                </div>
            </div>
        </div>
    </div>



    <!-- Leaders Summary Card -->

</div>

<!-- Congressman Section -->
<div class="card mb-4">
    <div class="card-header bg-dark py-3">
        <h5 class="mb-0 fw-bold text-light">Congressman</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Laynes -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                    Laynes
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                    $total = $cong_totals['Laynes']['total'];
                                    $forty_percent_laynes = round($total * 0.4);
                                    $sixty_percent_laynes = round($total * 0.6);
                                      echo number_format($cong_totals['Laynes']['total']); 
                                    ?>
                                </div>
                                <div style="font-size: smaller;" class="text-muted" data-toggle="tooltip"
                                    title="Calculation: 60% of total Laynes supporters">
                                    <i>
                                        <?php echo number_format($sixty_percent_laynes); ?> Predicted votes
                                    </i>
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
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100  ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                    Rodriguez
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                       $total_rodriguez = $cong_totals['Rodriguez']['total'];
                                     
                                    echo number_format($cong_totals['Rodriguez']['total']); 
                                    ?>
                                </div>
                                <div style="font-size: smaller;" class="text-muted" data-toggle="tooltip"
                                    title="Calculation: (40% of the total Laynes Supporters) + total Rodriguez + undecided">
                                    <i>
                                        <?php 
                                        echo number_format($forty_percent_laynes + $total_rodriguez + $cong_totals['UndecidedCong']['total'] + $cong_blanks); 
                                        ?>
                                        Predicted votes
                                    </i>
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
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                    Alberto
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                    echo number_format($cong_totals['Alberto']['total']); 
                                    ?>
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
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100  ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                    Undecided
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php echo number_format($cong_totals['UndecidedCong']['total'] + $cong_blanks); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-question-circle voter-icon text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Governor Section -->
<div class="card mb-4">
    <div class="card-header bg-dark py-3">
        <h5 class="mb-0 fw-bold text-light">Governor</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Boss Te -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 border-left-success ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                    Boss Te
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                        $forty_percent_bosste = round($gov_totals['Bosste']['total'] * 0.4);
                                    $sixty_percent_bosste = round($gov_totals['Bosste']['total'] * 0.6);
                                    echo number_format($gov_totals['Bosste']['total']);
                                     ?>
                                </div>
                                <div style="font-size: smaller;" class="text-muted" data-toggle="tooltip"
                                    title="Calculation: 60% of total Boss Te supporters">
                                    <i>
                                        <?php echo number_format($sixty_percent_bosste); ?> Predicted votes
                                    </i>
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
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                    Asanza
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                    $total_asanza = $gov_totals['Asanza']['total'];
                                    echo number_format($gov_totals['Asanza']['total']); ?>
                                </div>
                                <div style="font-size: smaller;" class="text-muted" data-toggle="tooltip"
                                    title="Calculation: 40% from total of Boss Te + total Asanza + undecided">
                                    <i>
                                        <?php echo number_format($forty_percent_bosste + $total_asanza + $gov_totals['UndecidedGov']['total'] + $gov_blanks); ?>
                                        Predicted votes
                                    </i>
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
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                    Undecided
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php echo number_format($gov_totals['UndecidedGov']['total'] + $gov_blanks); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-question-circle voter-icon text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vice Governor Section -->
<div class="card mb-4">
    <div class="card-header bg-dark py-3">
        <h5 class="mb-0 fw-bold text-light">Vice Governor</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Fernandez -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                    Fernandez
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                    $total_fernandez = $vgov_totals['Fernandez']['total'];
                                    $forty_percent_fernandez = round($total_fernandez * 0.4);
                                    $sixty_percent_fernandez = round($total_fernandez * 0.6);
                                    echo number_format($vgov_totals['Fernandez']['total']); ?>
                                </div>
                                <div style="font-size: smaller;" class="text-muted" data-toggle="tooltip"
                                    title="Calculation: 60% of total Fernandez supporters">
                                    <i>
                                        <?php echo number_format($sixty_percent_fernandez); ?> Predicted votes
                                    </i>
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
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100  ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                    Abundo
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                     $total_abundo = $vgov_totals['Abundo']['total'];
                                    echo number_format($vgov_totals['Abundo']['total']); ?>
                                </div>
                                <div style="font-size: smaller;" class="text-muted" data-toggle="tooltip"
                                    title="Calculation: 40% from total of Fernandez + total Abundo + undecided">
                                    <i>
                                        <?php echo number_format($forty_percent_fernandez + $total_abundo + $vgov_totals['UndecidedVGov']['total'] + $vgov_blanks); ?>
                                        Predicted votes
                                    </i>
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
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                    Undecided
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php echo number_format($vgov_totals['UndecidedVGov']['total'] + $vgov_blanks); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-question-circle voter-icon text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php 
    if($mun == "VIRAC" || $mun == ""){
        ?>
<div class="card mb-4">
    <div class="card-header bg-dark py-3">
        <h5 class="mb-0 fw-bold text-light">Virac Mayor</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Fernandez -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                    Boboy Cua
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                     $total_boboy = $mayor_totals['BossGov']['total'];
                                    $forty_percent_boboy = round($total_boboy * 0.4);
                                    $sixty_percent_boboy = round($total_boboy * 0.6);
                                    echo number_format($mayor_totals['BossGov']['total']); ?>
                                </div>
                                <div style="font-size: smaller;" class="text-muted" data-toggle="tooltip"
                                    title="Calculation: 60% of total Cua supporters">
                                    <i>
                                        <?php echo number_format($sixty_percent_boboy); ?> Predicted votes
                                    </i>
                                </div>
                            </div>
                            <div class="col-auto">
                                <img src="assets/images/bossgov.jpg" alt="Profile" class="profile-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Abundo -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100  ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                    Posoy
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                     $total_posoy = $mayor_totals['Posoy']['total'];
                                    echo number_format($mayor_totals['Posoy']['total']); ?>
                                </div>
                                <div style="font-size: smaller;" class="text-muted" data-toggle="tooltip"
                                    title="Calculation: 40% from total of Cua + total Posoy + undecided">
                                    <i>
                                        <?php echo number_format($forty_percent_boboy + $total_posoy + $mayor_totals['UndecidedMayor']['total'] + $vgov_blanks); ?>
                                        Predicted votes
                                    </i>
                                </div>
                            </div>
                            <div class="col-auto">
                                <img src="assets/images/posoyhaha.jpg" alt="Profile" class="profile-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100  ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                    Arcilla
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                     $total_arcilla = $mayor_totals['Arcilla']['total'];
                                    echo number_format($mayor_totals['Arcilla']['total']); ?>
                                </div>

                            </div>
                            <div class="col-auto">
                                <i class="fa fa-user-circle voter-icon text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Undecided -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 ">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                    Undecided
                                </div>
                                <div class="h4 mb-0 fw-bold">
                                    <?php 
                                  // echo $mayor_blanks;
                                    echo number_format($mayor_totals['UndecidedMayor']['total'] + $mayor_blanks); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-question-circle voter-icon text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php
    }
?>