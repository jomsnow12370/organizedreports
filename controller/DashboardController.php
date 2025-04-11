<?php
    /*     
    *THIS CONTROLLER IS CURRENTLY NOT USED, THE LOGIC IS CURRENTLY IN THE INDEX.PHP
    *
    *
    *
    *
    *
    *THIS IS FOR FUTURE REFERENCE ONLY, IF YOU WANT TO USE THIS , YOU CAN REQUIRE THIS FILE IN THE INDEX.PHP
    */

require_once 'models/leaders.php';
require_once 'models/household.php';

// Initialize variables
$mun = isset($_GET["mun"]) ? $_GET["mun"] : "";
$brgyId = isset($_GET["brgy"]) ? $_GET["brgy"] : "";
$brgyName = $brgyId ? get_value("SELECT barangay from barangays WHERE id = '$brgyId'")[0] : "";
$limit = 100;

// Set query parameters
$munquery = "";
if($mun != "") {
    $munquery = " AND municipality = '$mun'";
       $limit = 150;
}else{
 $munquery = "";
}

$brgyquery = "";
if($brgyId != "") {
    $brgyquery = " AND barangayId = '$brgyId'";
    $limit = 300;
}else{
 $brgyquery = "";
}

$brgyquery2 = "";
if($brgyId != "") {
    $brgyquery2 = " AND id = '$brgyId'";
}else{
 $brgyquery2 = "";
}           

// require_once 'controller/DashboardController.php';
// // Get leader counts
$total_mc = count_leaders($c, 4, $munquery, $brgyquery);
$total_dc = count_leaders($c, 3, $munquery, $brgyquery);
$total_bc = count_leaders($c, 2, $munquery, $brgyquery);
$total_wl = count_leaders($c, 1, $munquery, $brgyquery);

// // Get household counts
$res_head_household = mysqli_query($c, "SELECT COUNT(*) from head_household 
                                        INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id 
                                        INNER JOIN barangays ON barangays.id = v_info.barangayId 
                                        WHERE record_type = 1 $munquery $brgyquery
                                        GROUP BY fh_v_id");
                                        
$res_household_member = mysqli_query($c, "SELECT COUNT(*) from household_warding 
                                          INNER JOIN v_info ON v_info.v_id = household_warding.fh_v_id 
                                          INNER JOIN barangays ON barangays.id = v_info.barangayId 
                                          WHERE record_type = 1  $munquery $brgyquery
                                          GROUP BY mem_v_id");

                                          // // Get household counts
$res_head_household_virac = mysqli_query($c, "SELECT COUNT(*) from head_household 
                                        INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id 
                                        INNER JOIN barangays ON barangays.id = v_info.barangayId 
                                        WHERE record_type = 1 AND municipality = 'VIRAC'
                                        GROUP BY fh_v_id");
                                        
$res_household_member_virac = mysqli_query($c, "SELECT COUNT(*) from household_warding 
                                          INNER JOIN v_info ON v_info.v_id = household_warding.fh_v_id 
                                          INNER JOIN barangays ON barangays.id = v_info.barangayId 
                                          WHERE record_type = 1 AND municipality = 'VIRAC'
                                          GROUP BY mem_v_id");


$head_household = mysqli_num_rows($res_head_household);
$household_member = mysqli_num_rows($res_household_member);

$head_household_virac = mysqli_num_rows($res_head_household_virac);
$household_member_virac = mysqli_num_rows($res_household_member_virac);

$household_total = $head_household + $household_member;

$household_total_virac = $head_household_virac + $household_member_virac;

// // Get total voters
// // Survey data - Congressional candidates
$candidates = ['Laynes', 'Rodriguez', 'Alberto', 'UndecidedCong'];
$cong_totals = [];
$total_warding_cong = 0;

foreach ($candidates as $candidate) {
    $remarks = $candidate . '(Survey 2025)';
    $head_count = count_survey_responses($c, $remarks, true,  $munquery,$brgyquery);
    $member_count = count_survey_responses($c, $remarks, false, $munquery,$brgyquery);
    
    $cong_totals[$candidate] = [
        'head' => $head_count,
        'member' => $member_count,
        'total' => $head_count + $member_count
    ];
    
    $total_warding_cong += $head_count + $member_count;
}

// // Survey data - Governor candidates
$gov_candidates = ['Bosste', 'Asanza', 'UndecidedGov'];
$gov_totals = [];
$total_warding_gov = 0;

foreach ($gov_candidates as $candidate) {
    $remarks = $candidate . '(Survey 2025)';
    $head_count = count_survey_responses($c, $remarks, true,  $munquery, $brgyquery);
    $member_count = count_survey_responses($c, $remarks, false,  $munquery, $brgyquery);
    
    $gov_totals[$candidate] = [
        'head' => $head_count,
        'member' => $member_count,
        'total' => $head_count + $member_count
    ];
    
    $total_warding_gov += $head_count + $member_count;
}

// // Survey data - Vice Governor candidates
$vgov_candidates = ['Fernandez', 'Abundo', 'UndecidedVGov'];
$vgov_totals = [];
$total_warding_vgov = 0;

foreach ($vgov_candidates as $candidate) {
    $remarks = $candidate . '(Survey 2025)';
    $head_count = count_survey_responses($c, $remarks, true,  $munquery, $brgyquery);
    $member_count = count_survey_responses($c, $remarks, false, $munquery, $brgyquery);
    
    $vgov_totals[$candidate] = [
        'head' => $head_count,
        'member' => $member_count,
        'total' => $head_count + $member_count
    ];
    
    $total_warding_vgov += $head_count + $member_count;
}

$mayor_candidates = ['BossGov', 'Posoy', 'Arcilla', 'UndecidedMayor'];
$mayor_totals = [];
$total_warding_mayor = 0;

foreach ($mayor_candidates as $candidate) {
    $remarks = $candidate . '(Survey 2025)';
    $head_count = count_survey_responses($c, $remarks, true,  $munquery, $brgyquery);
    $member_count = count_survey_responses($c,  $remarks, false, $munquery, $brgyquery);
    
    $mayor_totals[$candidate] = [
        'head' => $head_count,
        'member' => $member_count,
        'total' => $head_count + $member_count
    ];
    
    $total_warding_mayor += $head_count + $member_count;
}


// // Calculate blanks
$cong_blanks = $household_total - $total_warding_cong;
$gov_blanks = $household_total - $total_warding_gov;
$vgov_blanks = $household_total - $total_warding_vgov;
$mayor_blanks = $household_total_virac - $total_warding_mayor;
?>