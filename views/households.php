<?php
$mun = isset($_GET['mun']) ? $_GET['mun'] : null;
$brgy = isset($_GET['brgy']) ? $_GET['brgy'] : null;

// Build query conditions based on mun and brgy
$familyheads = get_array("SELECT
    municipality,
    barangay,
    v_lname,
    v_fname,
    v_mname,
    'Family Head' as role,
    head_household.fh_v_id as household_id,
    CONCAT_WS(' ', v_lname, v_fname, v_mname) as full_name,
    CASE
        WHEN congressman = 660 THEN 'Laynes'
        WHEN congressman = 661 THEN 'Rodriguez'
        WHEN congressman = 678 THEN 'Alberto'
        WHEN congressman = 679 THEN 'Undecided'
        WHEN congressman IS NULL THEN 'Undecided'
    END AS cong,
    CASE
        WHEN governor = 662 THEN 'Bosste'
        WHEN governor = 663 THEN 'Asanza'
        WHEN governor = 680 THEN 'Undecided'
        WHEN governor IS NULL THEN 'Undecided'
    END AS gov,
    CASE
        WHEN vicegov = 676 THEN 'Fernandez'
        WHEN vicegov = 677 THEN 'Abundo'
        WHEN vicegov = 681 THEN 'Undecided'
        WHEN vicegov IS NULL THEN 'Undecided'
    END AS vgov,
    CASE
        WHEN mayor = 693 THEN 'Boboy'
        WHEN mayor = 694 THEN 'Posoy'
        WHEN mayor = 695 THEN 'Arcilla'
        WHEN mayor = 696 THEN 'Undecided'
        WHEN mayor IS NULL THEN 'Undecided'
    END AS mayor,
    CASE
        WHEN op = 626 THEN 'OP'
        ELSE null
    END AS op,
    CASE
        WHEN na = 682 THEN 'NA'
        ELSE null
    END AS na
FROM head_household
INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id
INNER JOIN barangays ON barangays.id = v_info.barangayId
LEFT JOIN politics ON politics.v_id = v_info.v_id
WHERE record_type = 1
$munquery $brgyquery
GROUP BY v_info.v_id
ORDER BY barangay, v_lname, v_mname");

// Initialize counters for grand total summary
$total_count = 0;
$total_summary = [
    'Laynes' => 0, 'Rodriguez' => 0, 'Alberto' => 0, 'Undecided_cong' => 0,
    'Bosste' => 0, 'Asanza' => 0, 'Undecided_gov' => 0,
    'Fernandez' => 0, 'Abundo' => 0, 'Undecided_vgov' => 0,
    'Boboy' => 0, 'Posoy' => 0, 'Arcilla' => 0, 'Undecided_mayor' => 0,
    'OP' => 0, 'NA' => 0
];

// Initialize arrays to track opposition and undecided by family and barangay
$barangay_stats = [];
$opposition_families = [];
$undecided_families = [];

// Process family heads and members to populate summary data
foreach ($familyheads as $family) {
    $household_id = $family["household_id"];
    $barangay = $family["barangay"];
    $family_name = $family["full_name"];

    // Initialize barangay stats if not already done
    if (!isset($barangay_stats[$barangay])) {
        $barangay_stats[$barangay] = [
            'total' => 0,
            'Rodriguez' => 0, 'Alberto' => 0, 'Undecided_cong' => 0,
            'Asanza' => 0, 'Undecided_gov' => 0,
            'Abundo' => 0, 'Undecided_vgov' => 0,
            'Posoy' => 0, 'Arcilla' => 0, 'Undecided_mayor' => 0
        ];
    }

    // Get family members
    $members = get_array("SELECT
        municipality,
        barangay,
        v_lname,
        v_fname,
        v_mname,
        household_warding.mem_v_id,
        CONCAT_WS(' ', v_lname, v_fname, v_mname) as full_name,
        CASE
            WHEN congressman = 660 THEN 'Laynes'
            WHEN congressman = 661 THEN 'Rodriguez'
            WHEN congressman = 678 THEN 'Alberto'
            WHEN congressman = 679 THEN 'Undecided'
            WHEN congressman IS NULL THEN 'Undecided'
        END AS cong,
        CASE
            WHEN governor = 662 THEN 'Bosste'
            WHEN governor = 663 THEN 'Asanza'
            WHEN governor = 680 THEN 'Undecided'
            WHEN governor IS NULL THEN 'Undecided'
        END AS gov,
        CASE
            WHEN vicegov = 676 THEN 'Fernandez'
            WHEN vicegov = 677 THEN 'Abundo'
            WHEN vicegov = 681 THEN 'Undecided'
            WHEN vicegov IS NULL THEN 'Undecided'
        END AS vgov,
        CASE
            WHEN mayor = 693 THEN 'Boboy'
            WHEN mayor = 694 THEN 'Posoy'
            WHEN mayor = 695 THEN 'Arcilla'
            WHEN mayor = 696 THEN 'Undecided'
            WHEN mayor IS NULL THEN 'Undecided'
        END AS mayor,
        CASE
            WHEN op = 626 THEN 'OP'
            ELSE null
        END AS op,
        CASE
            WHEN na = 682 THEN 'NA'
            ELSE null
        END AS na
    FROM household_warding
    INNER JOIN v_info ON v_info.v_id = household_warding.mem_v_id
    INNER JOIN barangays ON barangays.id = v_info.barangayId
    LEFT JOIN politics ON politics.v_id = v_info.v_id
    WHERE record_type = 1
    $munquery $brgyquery
    AND fh_v_id = $household_id
    GROUP BY v_info.v_id
    ORDER BY barangay, v_lname, v_mname");

    // Count family head for totals
    $total_count++;
    $barangay_stats[$barangay]['total']++;

    // Track family opposition/undecided status
    $family_opposition = [];
    $family_undecided = [];

    // Track family head preferences
    if ($family['cong'] == 'Rodriguez' || $family['cong'] == 'Alberto') {
        $barangay_stats[$barangay][$family['cong']]++;
        $family_opposition[] = 'Congressman: ' . $family['cong'];
    } elseif ($family['cong'] == 'Undecided') {
        $barangay_stats[$barangay]['Undecided_cong']++;
        $family_undecided[] = 'Congressman: Undecided';
    }

    if ($family['gov'] == 'Asanza') {
        $barangay_stats[$barangay]['Asanza']++;
        $family_opposition[] = 'Governor: Asanza';
    } elseif ($family['gov'] == 'Undecided') {
        $barangay_stats[$barangay]['Undecided_gov']++;
        $family_undecided[] = 'Governor: Undecided';
    }

    if ($family['vgov'] == 'Abundo') {
        $barangay_stats[$barangay]['Abundo']++;
        $family_opposition[] = 'Vice Governor: Abundo';
    } elseif ($family['vgov'] == 'Undecided') {
        $barangay_stats[$barangay]['Undecided_vgov']++;
        $family_undecided[] = 'Vice Governor: Undecided';
    }
    if ($mun == "VIRAC") {
        if ($family['mayor'] == 'Posoy' || $family['mayor'] == 'Arcilla') {
            $barangay_stats[$barangay][$family['mayor']]++;
            $family_opposition[] = 'Mayor: ' . $family['mayor'];
        } elseif ($family['mayor'] == 'Undecided') {
            $barangay_stats[$barangay]['Undecided_mayor']++;
            $family_undecided[] = 'Mayor: Undecided';
        }
    }

    // Add to main summary
    if ($family['cong'] == 'Laynes') $total_summary['Laynes']++;
    elseif ($family['cong'] == 'Rodriguez') $total_summary['Rodriguez']++;
    elseif ($family['cong'] == 'Alberto') $total_summary['Alberto']++;
    elseif ($family['cong'] == 'Undecided') $total_summary['Undecided_cong']++;

    if ($family['gov'] == 'Bosste') $total_summary['Bosste']++;
    elseif ($family['gov'] == 'Asanza') $total_summary['Asanza']++;
    elseif ($family['gov'] == 'Undecided') $total_summary['Undecided_gov']++;

    if ($family['vgov'] == 'Fernandez') $total_summary['Fernandez']++;
    elseif ($family['vgov'] == 'Abundo') $total_summary['Abundo']++;
    elseif ($family['vgov'] == 'Undecided') $total_summary['Undecided_vgov']++;

    if ($mun == "VIRAC") {
        if ($family['mayor'] == 'Boboy') $total_summary['Boboy']++;
        elseif ($family['mayor'] == 'Posoy') $total_summary['Posoy']++;
        elseif ($family['mayor'] == 'Arcilla') $total_summary['Arcilla']++;
        elseif ($family['mayor'] == 'Undecided') $total_summary['Undecided_mayor']++;
    }

    if ($family['op'] == 'OP') $total_summary['OP']++;
    if ($family['na'] == 'NA') $total_summary['NA']++;

    // Count members for totals
    foreach ($members as $member) {
        $total_count++;
        $barangay_stats[$barangay]['total']++;

        // Track member preferences
        if ($member['cong'] == 'Rodriguez' || $member['cong'] == 'Alberto') {
            $barangay_stats[$barangay][$member['cong']]++;
            $family_opposition[] = 'Congressman: ' . $member['cong'] . ' (Member)';
        } elseif ($member['cong'] == 'Undecided') {
            $barangay_stats[$barangay]['Undecided_cong']++;
            $family_undecided[] = 'Congressman: Undecided (Member)';
        }

        if ($member['gov'] == 'Asanza') {
            $barangay_stats[$barangay]['Asanza']++;
            $family_opposition[] = 'Governor: Asanza (Member)';
        } elseif ($member['gov'] == 'Undecided') {
            $barangay_stats[$barangay]['Undecided_gov']++;
            $family_undecided[] = 'Governor: Undecided (Member)';
        }

        if ($member['vgov'] == 'Abundo') {
            $barangay_stats[$barangay]['Abundo']++;
            $family_opposition[] = 'Vice Governor: Abundo (Member)';
        } elseif ($member['vgov'] == 'Undecided') {
            $barangay_stats[$barangay]['Undecided_vgov']++;
            $family_undecided[] = 'Vice Governor: Undecided (Member)';
        }

        if ($mun == "VIRAC") {
            if ($member['mayor'] == 'Posoy' || $member['mayor'] == 'Arcilla') {
                $barangay_stats[$barangay][$member['mayor']]++;
                $family_opposition[] = 'Mayor: ' . $member['mayor'] . ' (Member)';
            } elseif ($member['mayor'] == 'Undecided') {
                $barangay_stats[$barangay]['Undecided_mayor']++;
                $family_undecided[] = 'Mayor: Undecided (Member)';
            }
        }

        // Add to main summary
        if ($member['cong'] == 'Laynes') $total_summary['Laynes']++;
        elseif ($member['cong'] == 'Rodriguez') $total_summary['Rodriguez']++;
        elseif ($member['cong'] == 'Alberto') $total_summary['Alberto']++;
        elseif ($member['cong'] == 'Undecided') $total_summary['Undecided_cong']++;

        if ($member['gov'] == 'Bosste') $total_summary['Bosste']++;
        elseif ($member['gov'] == 'Asanza') $total_summary['Asanza']++;
        elseif ($member['gov'] == 'Undecided') $total_summary['Undecided_gov']++;

        if ($member['vgov'] == 'Fernandez') $total_summary['Fernandez']++;
        elseif ($member['vgov'] == 'Abundo') $total_summary['Abundo']++;
        elseif ($member['vgov'] == 'Undecided') $total_summary['Undecided_vgov']++;

        if ($mun == "VIRAC") {
            if ($member['mayor'] == 'Boboy') $total_summary['Boboy']++;
            elseif ($member['mayor'] == 'Posoy') $total_summary['Posoy']++;
            elseif ($member['mayor'] == 'Arcilla') $total_summary['Arcilla']++;
            elseif ($member['mayor'] == 'Undecided') $total_summary['Undecided_mayor']++;
        }

        if ($member['op'] == 'OP') $total_summary['OP']++;
        if ($member['na'] == 'NA') $total_summary['NA']++;
    }

    // Store family info if they have opposition or undecided preferences
    if (!empty($family_opposition)) {
        $opposition_families[] = [
            'name' => $family_name,
            'barangay' => $barangay,
            'preferences' => $family_opposition
        ];
    }

    if (!empty($family_undecided)) {
        $undecided_families[] = [
            'name' => $family_name,
            'barangay' => $barangay,
            'preferences' => $family_undecided
        ];
    }
}

// Display the summary tables FIRST
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
                            - Opposition Strength Analysis
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
<h3>Barangays</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Barangay</th>
            <th>Warded</th>
            <th>Rodriguez</th>
            <th>Alberto</th>
            <th>Asanza</th>
            <th>Abundo</th>
            <?php
            if ($mun == "VIRAC") {
                ?>
            <th>Posoy</th>
            <th>Arcilla</th>
            <?php
            }
            ?>

            <th>Priority Level</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $cnt = 1;
        foreach ($barangay_stats as $brgy => $stats) {
            // Calculate opposition percentage for priority level
            $opposition_count = $stats['Rodriguez'] + $stats['Alberto'] + $stats['Asanza'] +
                $stats['Abundo'];

            if ($mun == "VIRAC") {
                $opposition_count += $stats['Posoy'] + $stats['Arcilla'];
            }
            $opposition_percent = ($stats['total'] > 0) ? ($opposition_count / $stats['total']) * 100 : 0;

            // Determine priority level
            $priority = "Low";
            $badge_color = "success"; // Default badge color for low priority

            if ($opposition_percent >= 30) {
                $priority = "High";
                $badge_color = "danger"; // Red for high priority
            } elseif ($opposition_percent >= 15) {
                $priority = "Medium";
                $badge_color = "warning"; // Yellow for medium priority
            }
            ?>
        <tr>
            <td><?php echo $cnt; ?></td>
            <td><?php echo $brgy; ?></td>
            <td><?php echo $stats['total']; ?></td>
            <td><?php echo $stats['Rodriguez']; ?>
                (<?php echo ($stats['total'] > 0) ? round(($stats['Rodriguez'] / $stats['total']) * 100, 1) : 0; ?>%)
            </td>
            <td><?php echo $stats['Alberto']; ?>
                (<?php echo ($stats['total'] > 0) ? round(($stats['Alberto'] / $stats['total']) * 100, 1) : 0; ?>%)
            </td>
            <td><?php echo $stats['Asanza']; ?>
                (<?php echo ($stats['total'] > 0) ? round(($stats['Asanza'] / $stats['total']) * 100, 1) : 0; ?>%)
            </td>
            <td><?php echo $stats['Abundo']; ?>
                (<?php echo ($stats['total'] > 0) ? round(($stats['Abundo'] / $stats['total']) * 100, 1) : 0; ?>%)
            </td>
            <?php
                if ($mun == "VIRAC") {
                    ?>
            <td><?php echo $stats['Posoy']; ?>
                (<?php echo ($stats['total'] > 0) ? round(($stats['Posoy'] / $stats['total']) * 100, 1) : 0; ?>%)
            </td>
            <td><?php echo $stats['Arcilla']; ?>
                (<?php echo ($stats['total'] > 0) ? round(($stats['Arcilla'] / $stats['total']) * 100, 1) : 0; ?>%)
            </td>
            <?php
                }
                ?>
            <td><span class="badge bg-<?php echo $badge_color; ?>"><?php echo $priority; ?></span></td>
        </tr>
        <?php
            $cnt++;
        } ?>
    </tbody>
</table>
<footer></footer>
<h3 class="mt-4">Undecided Voters</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Barangay</th>
            <th>Warded</th>
            <th>Undecided (Cong)</th>
            <th>Undecided (Gov)</th>
            <th>Undecided (VGov)</th>
            <?php
            if ($mun == "VIRAC") {
                ?>
            <th>Undecided (Mayor)</th>
            <?php
            }
            ?>
            <th>Opportunity Level</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $cnt = 1;
        foreach ($barangay_stats as $brgy => $stats) {
            // Calculate undecided percentage for opportunity level
            $undecided_count = $stats['Undecided_cong'] + $stats['Undecided_gov'] +
                $stats['Undecided_vgov'];

            if ($mun == "VIRAC") {
                $undecided_count += $stats['Undecided_mayor'];
            }
            $undecided_percent = ($stats['total'] > 0) ? ($undecided_count / $stats['total']) * 100 : 0;

            // Determine opportunity level
            $opportunity = "Low";
            $badge_color = "success"; // Green for low undecided (good)

            if ($undecided_percent >= 30) {
                $opportunity = "High";
                $badge_color = "danger"; // Red for high undecided (bad)
            } elseif ($undecided_percent >= 15) {
                $opportunity = "Medium";
                $badge_color = "warning"; // Yellow for medium undecided (caution)
            }
            ?>
        <tr>
            <td><?php echo $cnt; ?></td>
            <td><?php echo $brgy; ?></td>
            <td><?php echo $stats['total']; ?></td>
            <td><?php echo $stats['Undecided_cong']; ?>
                (<?php echo ($stats['total'] > 0) ? round(($stats['Undecided_cong'] / $stats['total']) * 100, 1) : 0; ?>%)
            </td>
            <td><?php echo $stats['Undecided_gov']; ?>
                (<?php echo ($stats['total'] > 0) ? round(($stats['Undecided_gov'] / $stats['total']) * 100, 1) : 0; ?>%)
            </td>
            <td><?php echo $stats['Undecided_vgov']; ?>
                (<?php echo ($stats['total'] > 0) ? round(($stats['Undecided_vgov'] / $stats['total']) * 100, 1) : 0; ?>%)
            </td>
            <?php
                if ($mun == "VIRAC") {
                    ?>
            <td><?php echo $stats['Undecided_mayor']; ?>
                (<?php echo ($stats['total'] > 0) ? round(($stats['Undecided_mayor'] / $stats['total']) * 100, 1) : 0; ?>%)
            </td>
            <?php
                }
                ?>
            <td><span class="badge bg-<?php echo $badge_color; ?>"><?php echo $opportunity; ?></span></td>
        </tr>
        <?php
            $cnt++;
        } ?>
    </tbody>
</table>

<?php
// if (isset($_GET["mun"]) != "" && isset($_GET["brgy"]) != "") {
        ?>
<h3 class="mt-4">Families Supporting Opposition Candidates</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Family Name</th>
            <th>Barangay</th>
            <th>Opposition Support</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $count = 1;
        foreach ($opposition_families as $key => $family) {
            // Create a summary string instead of a list
            $positions = [];
            $positions_count = [
                'Congressman' => 0,
                'Governor' => 0,
                'Vice Governor' => 0,
                'Mayor' => 0
            ];

            // Count positions across all family members
            foreach ($family['preferences'] as $pref) {
                if (strpos($pref, 'Congressman:') !== false) {
                    $positions_count['Congressman']++;
                } elseif (strpos($pref, 'Governor:') !== false) {
                    $positions_count['Governor']++;
                } elseif (strpos($pref, 'Vice Governor:') !== false) {
                    $positions_count['Vice Governor']++;
                } elseif ($mun == "VIRAC" && strpos($pref, 'Mayor:') !== false) {
                    $positions_count['Mayor']++;
                }
            }

            // Create summary text
            foreach ($positions_count as $position => $count) {
                if ($count > 0) {
                    $candidates = [];

                    // Extract candidate names for this position
                    foreach ($family['preferences'] as $pref) {
                        if (strpos($pref, $position . ':') !== false) {
                            $parts = explode(':', $pref);
                            $candidate = trim($parts[1]);
                            if (strpos($candidate, '(Member)') !== false) {
                                $candidate = str_replace(' (Member)', '', $candidate);
                            }

                            // Only add candidate if not already in the array
                            if (!in_array($candidate, $candidates)) {
                                $candidates[] = $candidate;
                            }
                        }
                    }

                    // Add summary for this position
                    $positions[] = $position . ': ' . implode(', ', $candidates);
                }
            }

            $summary = implode(' | ', $positions);
            ?>
        <tr>
            <td><?php echo $key + 1; ?></td>
            <td><?php echo $family['name']; ?></td>
            <td><?php echo $family['barangay']; ?></td>
            <td><?php echo $summary; ?></td>
        </tr>
        <?php
        } ?>
    </tbody>
</table>

<h3 class="mt-4">Families with Undecided Voters</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Family Name</th>
            <th>Barangay</th>
            <th>Undecided Positions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $count = 1;
        foreach ($undecided_families as $key => $family) {
            // Create a summary text instead of a list
            $undecided_count = [
                'Congressman' => 0,
                'Governor' => 0,
                'Vice Governor' => 0,
                'Mayor' => 0
            ];

            // Count undecided positions
            foreach ($family['preferences'] as $pref) {
                if (strpos($pref, 'Congressman:') !== false) {
                    $undecided_count['Congressman']++;
                } elseif (strpos($pref, 'Governor:') !== false) {
                    $undecided_count['Governor']++;
                } elseif (strpos($pref, 'Vice Governor:') !== false) {
                    $undecided_count['Vice Governor']++;
                } elseif ($mun == "VIRAC" && strpos($pref, 'Mayor:') !== false) {
                    $undecided_count['Mayor']++;
                }
            }

            // Format the summary
            $undecided_summary = [];
            foreach ($undecided_count as $position => $count) {
                if ($count > 0) {
                    $undecided_summary[] = $position . ' (' . $count . ')';
                }
            }

            $summary = implode(' | ', $undecided_summary);
            ?>
        <tr>
            <td><?php echo $key + 1; ?></td>
            <td><?php echo $family['name']; ?></td>
            <td><?php echo $family['barangay']; ?></td>
            <td><?php echo $summary; ?></td>
        </tr>
        <?php
        } ?>
    </tbody>
</table>
<?php
   // }
?>

<?php
?>


<?php
// Display family head and members tables AFTER the summary tables
// Display family head and members tables AFTER the summary tables
//if (isset($_GET["mun"]) != "" && isset($_GET["brgy"]) != "") {
    ?>
<h1>Households</h1>
<?php
    foreach ($familyheads as $family) {
        $household_id = $family["household_id"];

        // Get family members
        $members = get_array("SELECT
            municipality,
            barangay,
            v_lname,
            v_fname,
            v_mname,
            household_warding.mem_v_id,
            CONCAT_WS(' ', v_lname, v_fname, v_mname) as full_name,
            CASE
                WHEN congressman = 660 THEN 'Laynes'
                WHEN congressman = 661 THEN 'Rodriguez'
                WHEN congressman = 678 THEN 'Alberto'
                WHEN congressman = 679 THEN 'Undecided'
                WHEN congressman IS NULL THEN 'Undecided'
            END AS cong,
            CASE
                WHEN governor = 662 THEN 'Bosste'
                WHEN governor = 663 THEN 'Asanza'
                WHEN governor = 680 THEN 'Undecided'
                WHEN governor IS NULL THEN 'Undecided'
            END AS gov,
            CASE
                WHEN vicegov = 676 THEN 'Fernandez'
                WHEN vicegov = 677 THEN 'Abundo'
                WHEN vicegov = 681 THEN 'Undecided'
                WHEN vicegov IS NULL THEN 'Undecided'
            END AS vgov,
            CASE
                WHEN mayor = 693 THEN 'Boboy'
                WHEN mayor = 694 THEN 'Posoy'
                WHEN mayor = 695 THEN 'Arcilla'
                WHEN mayor = 696 THEN 'Undecided'
                WHEN mayor IS NULL THEN 'Undecided'
            END AS mayor,
            CASE
                WHEN op = 626 THEN 'OP'
                ELSE null
            END AS op,
            CASE
                WHEN na = 682 THEN 'NA'
                ELSE null
            END AS na
        FROM household_warding
        INNER JOIN v_info ON v_info.v_id = household_warding.mem_v_id
        INNER JOIN barangays ON barangays.id = v_info.barangayId
        LEFT JOIN politics ON politics.v_id = v_info.v_id
        WHERE record_type = 1
        $munquery $brgyquery
        AND fh_v_id = $household_id
        GROUP BY v_info.v_id
        ORDER BY barangay, v_lname, v_mname");

        // Check if the family head or any member is undecided or opposition
        $highlight_family = false;

        if (!function_exists('isOppositionOrUndecided')) {
            function isOppositionOrUndecided($person, $mun) {
                return (
                    $person['cong'] !== 'Laynes' ||
                    $person['gov'] !== 'Bosste' ||
                    $person['vgov'] !== 'Fernandez' ||
                    ($mun === 'VIRAC' && $person['mayor'] !== 'Boboy') ||
                    $person['cong'] === 'Undecided' ||
                    $person['gov'] === 'Undecided' ||
                    $person['vgov'] === 'Undecided' ||
                    ($mun === 'VIRAC' && $person['mayor'] === 'Undecided')
                );
            }
        }

        // Check the family head
        if (isOppositionOrUndecided($family, $mun)) {
            $highlight_family = true;
        }

        // Check the members if not already highlighted
        if (!$highlight_family) {
            foreach ($members as $member) {
                if (isOppositionOrUndecided($member, $mun)) {
                    $highlight_family = true;
                    break;
                }
            }
        }


        if ($highlight_family) {
            ?>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Role</th>
            <th>Congressman</th>
            <th>Governor</th>
            <th>Vice Governor</th>
            <?php
                        if ($mun == "VIRAC") {
                            ?>
            <th>Mayor</th>
            <?php
                        }
                        ?>
            <th>OP</th>
            <th>NA</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td><?php echo $family['full_name']; ?></td>
            <td>Family Head</td>
            <td><?php echo $family['cong']; ?></td>
            <td><?php echo $family['gov']; ?></td>
            <td><?php echo $family['vgov']; ?></td>
            <?php
                        if ($mun == "VIRAC") {
                            ?>
            <td><?php echo $family['mayor']; ?></td>
            <?php
                        }
                        ?>
            <td><?php echo ($family['op'] ? $family['op'] : '-'); ?></td>
            <td><?php echo ($family['na'] ? $family['na'] : '-'); ?></td>
        </tr>

        <?php
                    $row_count = 2;
                    foreach ($members as $member) {
                        ?>
        <tr>
            <td><?php echo $row_count++; ?></td>
            <td><?php echo $member['full_name']; ?></td>
            <td>Member</td>
            <td><?php echo $member['cong']; ?></td>
            <td><?php echo $member['gov']; ?></td>
            <td><?php echo $member['vgov']; ?></td>
            <?php
                            if ($mun == "VIRAC") {
                                ?>
            <td><?php echo $member['mayor']; ?></td>
            <?php
                            }
                            ?>
            <td><?php echo ($member['op'] ? $member['op'] : '-'); ?></td>
            <td><?php echo ($member['na'] ? $member['na'] : '-'); ?></td>
        </tr>
        <?php
                    }
                    ?>
    </tbody>
</table>
<?php
        }
    }
    ?>
<?php //} ?>