<?php
/* *THIS CONTROLLER IS CURRENTLY NOT USED, THE LOGIC IS CURRENTLY IN 
THE INDEX.PHP
*
*
*
*
*
*THIS IS FOR FUTURE REFERENCE ONLY, IF YOU WANT TO USE THIS , YOU 
CAN REQUIRE THIS FILE IN THE INDEX.PHP
*/

require_once 'models/leaders.php';
require_once 'models/household.php';

// Initialize variables
$mun = $_GET["mun"] ?? "";
$brgyId = $_GET["brgy"] ?? "";
$brgyName = $brgyId ? get_value("SELECT barangay from barangays WHERE id = '$brgyId'")[0] : "";
$limit = 100;

// Construct query parameters
$munquery = $mun ? " AND municipality = '$mun'" : "";
if ($mun) {
    $limit = 150;
}

$brgyquery = $brgyId ? " AND barangayId = '$brgyId'" : "";
if ($brgyId) {
    $limit = 300;
}

$brgyquery2 = $brgyId ? " AND id = '$brgyId'" : "";

// Get leader counts
$total_mc = count_leaders($c, 4, $munquery, $brgyquery);
$total_dc = count_leaders($c, 3, $munquery, $brgyquery);
$total_bc = count_leaders($c, 2, $munquery, $brgyquery);
$total_wl = count_leaders($c, 1, $munquery, $brgyquery);

function get_municipality_summary($c) {
    $query_households = "SELECT municipality, SUM(households) AS total_households FROM barangays WHERE id IS NOT NULL GROUP BY municipality ORDER BY municipality";
    $result_households = mysqli_query($c, $query_households);
    
    $summary = [];
    $total_all_households = 0;
    
    while ($row = mysqli_fetch_assoc($result_households)) {
        $mun = $row['municipality'];
        $households = $row['total_households'];
        $total_all_households += $households;
        
        $summary[$mun] = [
            'total_households' => $households,
            'surveyed_households' => 0,
            'surveyed_members' => 0,
            'household_percentage' => 0,
            'total_surveyed' => 0,
            'total_voters' => 0,
            'warded_percentage' => 0
        ];
    }
    
    $query_heads = "SELECT barangays.municipality, COUNT(DISTINCT head_household.fh_v_id) AS surveyed_heads FROM head_household INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 GROUP BY barangays.municipality ORDER BY barangays.municipality";
    $result_heads = mysqli_query($c, $query_heads);
    
    while ($row = mysqli_fetch_assoc($result_heads)) {
        $mun = $row['municipality'];
        $summary[$mun]['surveyed_households'] = $row['surveyed_heads'];
        $summary[$mun]['household_percentage'] = $summary[$mun]['total_households'] > 0 ? round(($row['surveyed_heads'] / $summary[$mun]['total_households']) * 100, 2) : 0;
    }
    
    $query_members = "SELECT barangays.municipality, COUNT(DISTINCT household_warding.mem_v_id) AS surveyed_members FROM household_warding INNER JOIN v_info ON v_info.v_id = household_warding.mem_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 GROUP BY barangays.municipality ORDER BY barangays.municipality";
    $result_members = mysqli_query($c, $query_members);
    
    while ($row = mysqli_fetch_assoc($result_members)) {
        $mun = $row['municipality'];
        $summary[$mun]['surveyed_members'] = $row['surveyed_members'];
        $summary[$mun]['total_surveyed'] = $summary[$mun]['surveyed_households'] + $row['surveyed_members'];
    }

    $query_total_voters = "SELECT barangays.municipality, COUNT(v_info.v_id) AS total_voters FROM v_info INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 GROUP BY barangays.municipality ORDER BY barangays.municipality";
    $result_total_voters = mysqli_query($c, $query_total_voters);

    while ($row = mysqli_fetch_assoc($result_total_voters)) {
        $mun = $row['municipality'];
        $summary[$mun]['total_voters'] = $row['total_voters'];
        $summary[$mun]['warded_percentage'] = $row['total_voters'] > 0 ? round(($summary[$mun]['total_surveyed'] / $row['total_voters']) * 100, 2) : 0;
    }
    
    $grand_total = [
        'total_households' => $total_all_households,
        'surveyed_households' => 0,
        'surveyed_members' => 0,
        'household_percentage' => 0,
        'total_surveyed' => 0,
        'total_voters' => 0,
        'warded_percentage' => 0
    ];
    
    $grand_total_voters = 0;

    foreach ($summary as $mun_data) {
        $grand_total['surveyed_households'] += $mun_data['surveyed_households'];
        $grand_total['surveyed_members'] += $mun_data['surveyed_members'];
        $grand_total['total_surveyed'] += $mun_data['total_surveyed'];
        $grand_total_voters += $mun_data['warded_percentage'] > 0 ? ($mun_data['total_surveyed'] / ($mun_data['warded_percentage']/100)) : 0;
    }
    
    $grand_total['household_percentage'] = $grand_total['total_households'] > 0 ? round(($grand_total['surveyed_households'] / $grand_total['total_households']) * 100, 2) : 0;
    $grand_total['warded_percentage'] = $grand_total_voters > 0 ? round(($grand_total['total_surveyed'] / $grand_total_voters) * 100, 2) : 0;

    $summary['GRAND_TOTAL'] = $grand_total;
    
    return $summary;
}

function get_barangay_summary($c, $municipality) {
    $query_households = "SELECT id, barangay, households AS total_households FROM barangays WHERE municipality = '$municipality' ORDER BY barangay";
    $result_households = mysqli_query($c, $query_households);
    
    $summary = [];
    $total_all_households = 0;
    
    while ($row = mysqli_fetch_assoc($result_households)) {
        $brgy_id = $row['id'];
        $summary[$brgy_id] = [
            'barangay' => $row['barangay'],
            'total_households' => $row['total_households'],
            'surveyed_households' => 0,
            'surveyed_members' => 0,
            'household_percentage' => 0,
            'total_voters' => 0,
            'total_surveyed' => 0,
            'warded_percentage' => 0
        ];
        $total_all_households += $row['total_households'];
    }
    
    $query_heads = "SELECT barangays.id, barangays.barangay, COUNT(DISTINCT head_household.fh_v_id) AS surveyed_heads FROM head_household INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 AND barangays.municipality = '$municipality' GROUP BY barangays.id, barangays.barangay ORDER BY barangays.barangay";
    $result_heads = mysqli_query($c, $query_heads);
    
    while ($row = mysqli_fetch_assoc($result_heads)) {
        $brgy_id = $row['id'];
        $summary[$brgy_id]['surveyed_households'] = $row['surveyed_heads'];
        $summary[$brgy_id]['household_percentage'] = $summary[$brgy_id]['total_households'] > 0 ? round(($row['surveyed_heads'] / $summary[$brgy_id]['total_households']) * 100, 2) : 0;
    }
    
    $query_members = "SELECT barangays.id, barangays.barangay, COUNT(DISTINCT household_warding.mem_v_id) AS surveyed_members FROM household_warding INNER JOIN v_info ON v_info.v_id = household_warding.fh_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 AND barangays.municipality = '$municipality' GROUP BY barangays.id, barangays.barangay ORDER BY barangays.barangay";
    $result_members = mysqli_query($c, $query_members);
    
    while ($row = mysqli_fetch_assoc($result_members)) {
        $brgy_id = $row['id'];
        $summary[$brgy_id]['surveyed_members'] = $row['surveyed_members'];
        $summary[$brgy_id]['total_surveyed'] = $summary[$brgy_id]['surveyed_households'] + $row['surveyed_members'];
        $summary[$brgy_id]['total_voters'] = $summary[$brgy_id]['surveyed_households'];
    }

    $query_total_voters = "SELECT barangays.id, barangays.barangay, COUNT(v_info.v_id) AS total_voters FROM v_info INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 AND barangays.municipality = '$municipality' GROUP BY barangays.id, barangays.barangay ORDER BY barangays.barangay";
    $result_total_voters = mysqli_query($c, $query_total_voters);

    while ($row = mysqli_fetch_assoc($result_total_voters)) {
        $brgy_id = $row['id'];
        $summary[$brgy_id]['total_voters'] = $row['total_voters'];
        $summary[$brgy_id]['warded_percentage'] = $row['total_voters'] > 0 ? round(($summary[$brgy_id]['total_surveyed'] / $row['total_voters']) * 100, 2) : 0;
    }
    
    $grand_total = [
        'barangay' => 'GRAND TOTAL',
        'total_households' => $total_all_households,
        'surveyed_households' => 0,
        'surveyed_members' => 0,
        'household_percentage' => 0,
        'total_surveyed' => 0,
        'warded_percentage' => 0
    ];
    
    $grand_total_voters = 0;

    foreach ($summary as $brgy_data) {
        if (is_array($brgy_data)) {
            $grand_total['surveyed_households'] += $brgy_data['surveyed_households'];
            $grand_total['surveyed_members'] += $brgy_data['surveyed_members'];
            $grand_total['total_surveyed'] += $brgy_data['total_surveyed'];
            $grand_total_voters += $brgy_data['warded_percentage'] > 0 ? ($brgy_data['total_surveyed'] / ($brgy_data['warded_percentage']/100)) : 0;
        }
    }
    
    $grand_total['household_percentage'] = $grand_total['total_households'] > 0 ? round(($grand_total['surveyed_households'] / $grand_total['total_households']) * 100, 2) : 0;
    $grand_total['warded_percentage'] = $grand_total_voters > 0 ? round(($grand_total['total_surveyed'] / $grand_total_voters) * 100, 2) : 0;

    $summary['GRAND_TOTAL'] = $grand_total;
    
    return $summary;
}

$res_head_household = mysqli_query($c, "SELECT COUNT(*) FROM head_household INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 $munquery $brgyquery GROUP BY fh_v_id");
$res_household_member = mysqli_query($c, "SELECT COUNT(*) FROM household_warding INNER JOIN v_info ON v_info.v_id = household_warding.fh_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 $munquery $brgyquery GROUP BY mem_v_id");

$res_head_household_virac = mysqli_query($c, "SELECT COUNT(*) FROM head_household INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 AND municipality = 'VIRAC' GROUP BY fh_v_id");
$res_household_member_virac = mysqli_query($c, "SELECT COUNT(*) FROM household_warding INNER JOIN v_info ON v_info.v_id = household_warding.fh_v_id INNER JOIN barangays ON barangays.id = v_info.barangayId WHERE record_type = 1 AND municipality = 'VIRAC' GROUP BY mem_v_id");

$head_household = mysqli_num_rows($res_head_household);
$household_member = mysqli_num_rows($res_household_member);

$head_household_virac = mysqli_num_rows($res_head_household_virac);
$household_member_virac = mysqli_num_rows($res_household_member_virac);

$household_total = $head_household + $household_member;
$household_total_virac = $head_household_virac + $household_member_virac;

$candidates = ['Laynes', 'Rodriguez', 'Alberto', 'UndecidedCong'];
$cong_totals = [];
$total_warding_cong = 0;

foreach ($candidates as $candidate) {
    $remarks = $candidate . '(Survey 2025)';
    $head_count = count_survey_responses($c, $remarks, true, $munquery, $brgyquery);
    $member_count = count_survey_responses($c, $remarks, false, $munquery, $brgyquery);
    $cong_totals[$candidate] = ['head' => $head_count, 'member' => $member_count, 'total' => $head_count + $member_count];
    $total_warding_cong += $head_count + $member_count;
}

$gov_candidates = ['Bosste', 'Asanza', 'UndecidedGov'];
$gov_totals = [];
$total_warding_gov = 0;

foreach ($gov_candidates as $candidate) {
    $remarks = $candidate . '(Survey 2025)';
    $head_count = count_survey_responses($c, $remarks, true, $munquery, $brgyquery);
    $member_count = count_survey_responses($c, $remarks, false, $munquery, $brgyquery);
    $gov_totals[$candidate] = ['head' => $head_count, 'member' => $member_count, 'total' => $head_count + $member_count];
    $total_warding_gov += $head_count + $member_count;
}

$vgov_candidates = ['Fernandez', 'Abundo', 'UndecidedVGov'];
$vgov_totals = [];
$total_warding_vgov = 0;

foreach ($vgov_candidates as $candidate) {
    $remarks = $candidate . '(Survey 2025)';
    $head_count = count_survey_responses($c, $remarks, true, $munquery, $brgyquery);
    $member_count = count_survey_responses($c, $remarks, false, $munquery, $brgyquery);
    $vgov_totals[$candidate] = ['head' => $head_count, 'member' => $member_count, 'total' => $head_count + $member_count];
    $total_warding_vgov += $head_count + $member_count;
}

$mayor_candidates = ['BossGov', 'Posoy', 'Arcilla', 'UndecidedMayor'];
$mayor_totals = [];
$total_warding_mayor = 0;

foreach ($mayor_candidates as $candidate) {
    $remarks = $candidate . '(Survey 2025)';
    $head_count = count_survey_responses($c, $remarks, true, $munquery, $brgyquery);
    $member_count = count_survey_responses($c, $remarks, false, $munquery, $brgyquery);
    $mayor_totals[$candidate] = ['head' => $head_count, 'member' => $member_count, 'total' => $head_count + $member_count];
    $total_warding_mayor += $head_count + $member_count;
}

$cong_blanks = $household_total - $total_warding_cong;
$gov_blanks = $household_total - $total_warding_gov;
$vgov_blanks = $household_total - $total_warding_vgov;
$mayor_blanks = $household_total_virac - $total_warding_mayor;

$municipality_summary = get_municipality_summary($c);
?>