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
function get_municipality_summary($c) {
    // Get total households by municipality
    $query_households = "SELECT municipality, SUM(households) AS total_households 
                         FROM barangays 
                         WHERE id IS NOT NULL 
                         GROUP BY municipality
                         ORDER BY municipality";
    
    $result_households = mysqli_query($c, $query_households);
    
    $summary = [];
    $total_all_households = 0;
    
    // Store the household counts
    while ($row = mysqli_fetch_assoc($result_households)) {
        $mun = $row['municipality'];
        $households = $row['total_households'];
        $total_all_households += $households;
        
        if (!isset($summary[$mun])) {
            $summary[$mun] = [
                'total_households' => $households,
                'surveyed_households' => 0,
                'surveyed_members' => 0,
                'household_percentage' => 0,
                'total_surveyed' => 0,
                'total_percentage' => 0
            ];
        }
    }
    
    // Count surveyed household heads by municipality
    $query_heads = "SELECT barangays.municipality, COUNT(DISTINCT head_household.fh_v_id) AS surveyed_heads
                    FROM head_household
                    INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id
                    INNER JOIN barangays ON barangays.id = v_info.barangayId
                    WHERE record_type = 1
                    GROUP BY barangays.municipality
                    ORDER BY barangays.municipality";
    
    $result_heads = mysqli_query($c, $query_heads);
    
    while ($row = mysqli_fetch_assoc($result_heads)) {
        $mun = $row['municipality'];
        $surveyed_heads = $row['surveyed_heads'];
        
        if (isset($summary[$mun])) {
            $summary[$mun]['surveyed_households'] = $surveyed_heads;
            // Calculate percentage if total_households is not zero
            if ($summary[$mun]['total_households'] > 0) {
                $summary[$mun]['household_percentage'] = round(($surveyed_heads / $summary[$mun]['total_households']) * 100, 2);
            }
        }
    }
    
    // Count household members by municipality
    $query_members = "SELECT barangays.municipality, COUNT(DISTINCT household_warding.mem_v_id) AS surveyed_members
                      FROM household_warding
                      INNER JOIN v_info ON v_info.v_id = household_warding.fh_v_id
                      INNER JOIN barangays ON barangays.id = v_info.barangayId
                      WHERE record_type = 1
                      GROUP BY barangays.municipality
                      ORDER BY barangays.municipality";
    
    $result_members = mysqli_query($c, $query_members);
    
    while ($row = mysqli_fetch_assoc($result_members)) {
        $mun = $row['municipality'];
        $surveyed_members = $row['surveyed_members'];
        
        if (isset($summary[$mun])) {
            $summary[$mun]['surveyed_members'] = $surveyed_members;
            $summary[$mun]['total_surveyed'] = $summary[$mun]['surveyed_households'] + $surveyed_members;
        }
    }
    
    // Calculate grand totals
    $grand_total = [
        'total_households' => $total_all_households,
        'surveyed_households' => 0,
        'surveyed_members' => 0,
        'household_percentage' => 0,
        'total_surveyed' => 0,
        'total_percentage' => 0
    ];
    
    foreach ($summary as $mun_data) {
        $grand_total['surveyed_households'] += $mun_data['surveyed_households'];
        $grand_total['surveyed_members'] += $mun_data['surveyed_members'];
        $grand_total['total_surveyed'] += $mun_data['total_surveyed'];
    }
    
    // Calculate grand total percentages
    if ($grand_total['total_households'] > 0) {
        $grand_total['household_percentage'] = round(($grand_total['surveyed_households'] / $grand_total['total_households']) * 100, 2);
    }
    
    $summary['GRAND_TOTAL'] = $grand_total;
    
    return $summary;
}

function get_barangay_summary($c, $municipality) {
    // Get total households by barangay within the selected municipality
    $query_households = "SELECT id, barangay, households AS total_households 
                         FROM barangays 
                         WHERE municipality = '$municipality' 
                         ORDER BY barangay";
    
    $result_households = mysqli_query($c, $query_households);
    
    $summary = [];
    $total_all_households = 0;
    
    // Store the household counts
    while ($row = mysqli_fetch_assoc($result_households)) {
        $brgy_id = $row['id'];
        $brgy_name = $row['barangay'];
        $households = $row['total_households'];
        $total_all_households += $households;
        
        if (!isset($summary[$brgy_id])) {
            $summary[$brgy_id] = [
                'barangay' => $brgy_name,
                'total_households' => $households,
                'surveyed_households' => 0,
                'surveyed_members' => 0,
                'household_percentage' => 0,
                'total_surveyed' => 0
            ];
        }
    }
    
    // Count surveyed household heads by barangay
    $query_heads = "SELECT barangays.id, barangays.barangay, COUNT(DISTINCT head_household.fh_v_id) AS surveyed_heads
                    FROM head_household
                    INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id
                    INNER JOIN barangays ON barangays.id = v_info.barangayId
                    WHERE record_type = 1 AND barangays.municipality = '$municipality'
                    GROUP BY barangays.id, barangays.barangay
                    ORDER BY barangays.barangay";
    
    $result_heads = mysqli_query($c, $query_heads);
    
    while ($row = mysqli_fetch_assoc($result_heads)) {
        $brgy_id = $row['id'];
        $surveyed_heads = $row['surveyed_heads'];
        
        if (isset($summary[$brgy_id])) {
            $summary[$brgy_id]['surveyed_households'] = $surveyed_heads;
            // Calculate percentage if total_households is not zero
            if ($summary[$brgy_id]['total_households'] > 0) {
                $summary[$brgy_id]['household_percentage'] = round(($surveyed_heads / $summary[$brgy_id]['total_households']) * 100, 2);
            }
        }
    }
    
    // Count household members by barangay
    $query_members = "SELECT barangays.id, barangays.barangay, COUNT(DISTINCT household_warding.mem_v_id) AS surveyed_members
                      FROM household_warding
                      INNER JOIN v_info ON v_info.v_id = household_warding.fh_v_id
                      INNER JOIN barangays ON barangays.id = v_info.barangayId
                      WHERE record_type = 1 AND barangays.municipality = '$municipality'
                      GROUP BY barangays.id, barangays.barangay
                      ORDER BY barangays.barangay";
    
    $result_members = mysqli_query($c, $query_members);
    
    while ($row = mysqli_fetch_assoc($result_members)) {
        $brgy_id = $row['id'];
        $surveyed_members = $row['surveyed_members'];
        
        if (isset($summary[$brgy_id])) {
            $summary[$brgy_id]['surveyed_members'] = $surveyed_members;
            $summary[$brgy_id]['total_surveyed'] = $summary[$brgy_id]['surveyed_households'] + $surveyed_members;
        }
    }
    
    // Calculate grand totals
    $grand_total = [
        'barangay' => 'GRAND TOTAL',
        'total_households' => $total_all_households,
        'surveyed_households' => 0,
        'surveyed_members' => 0,
        'household_percentage' => 0,
        'total_surveyed' => 0
    ];
    
    foreach ($summary as $brgy_data) {
        $grand_total['surveyed_households'] += $brgy_data['surveyed_households'];
        $grand_total['surveyed_members'] += $brgy_data['surveyed_members'];
        $grand_total['total_surveyed'] += $brgy_data['total_surveyed'];
    }
    
    // Calculate grand total percentages
    if ($grand_total['total_households'] > 0) {
        $grand_total['household_percentage'] = round(($grand_total['surveyed_households'] / $grand_total['total_households']) * 100, 2);
    }
    
    $summary['GRAND_TOTAL'] = $grand_total;
    
    return $summary;
}
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

// Get municipality summary data
$municipality_summary = get_municipality_summary($c);
?>