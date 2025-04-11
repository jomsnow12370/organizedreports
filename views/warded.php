<?php
$mun = isset($_GET['mun']) ? $_GET['mun'] : null;
$brgy = isset($_GET['brgy']) ? $_GET['brgy'] : null;

// Use prepared statements to prevent SQL injection
$munquery = $mun ? "AND municipality = ?" : "";
$brgyquery2 = $brgy ? "AND barangay = ?" : "";

// Create a prepared statement
$stmt = $conn->prepare("SELECT barangay, id FROM barangays WHERE id IS NOT NULL $munquery $brgyquery2");

// Bind parameters if they exist
if ($mun && $brgy) {
    $stmt->bind_param("ss", $mun, $brgy);
} elseif ($mun) {
    $stmt->bind_param("s", $mun);
} elseif ($brgy) {
    $stmt->bind_param("s", $brgy);
}

$stmt->execute();
$result = $stmt->get_result();
$barangays = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Define constant values to avoid repetition
$CONGRESSMAN_LABELS = [
    660 => 'Laynes',
    661 => 'Rodriguez',
    678 => 'Alberto',
    679 => 'Undecided',
    null => 'Undecided'
];

$GOVERNOR_LABELS = [
    662 => 'Bosste',
    663 => 'Asanza',
    680 => 'Undecided',
    null => 'Undecided'
];

$VICEGOV_LABELS = [
    676 => 'Fernandez',
    677 => 'Abundo',
    681 => 'Undecided',
    null => 'Undecided'
];

$MAYOR_LABELS = [
    693 => 'Boboy',
    694 => 'Posoy',
    695 => 'Arcilla',
    696 => 'Undecided',
    null => 'Undecided'
];

// Create common query components for reuse
$politicsSelect = "CASE
        WHEN congressman IN (660, 661, 678, 679) THEN '" . implode("' WHEN congressman = ", array_keys($CONGRESSMAN_LABELS)) . "' ELSE 'Other'
        END AS cong,
    CASE
        WHEN governor IN (662, 663, 680) THEN '" . implode("' WHEN governor = ", array_keys($GOVERNOR_LABELS)) . "' ELSE 'Other'
        END AS gov,
    CASE
        WHEN vicegov IN (676, 677, 681) THEN '" . implode("' WHEN vicegov = ", array_keys($VICEGOV_LABELS)) . "' ELSE 'Other'
        END AS vgov,
    CASE
        WHEN mayor IN (693, 694, 695, 696) THEN '" . implode("' WHEN mayor = ", array_keys($MAYOR_LABELS)) . "' ELSE 'Other'
        END AS mayor";

// Process each barangay
foreach ($barangays as $barangay) {
    $brgyid = $barangay['id'];
    $brgyname = $barangay['barangay'];
    
    // Prepare the statement once and reuse
    $headQuery = $conn->prepare("SELECT
        municipality,
        barangay,
        v_lname,
        v_fname,
        v_mname,
        'Family Head' as role,
        head_household.fh_v_id as household_id,
        CONCAT(v_lname, ', ', v_fname, ' ', v_mname) as full_name,
        CASE
            WHEN congressman = 660 THEN 'Laynes'
            WHEN congressman = 661 THEN 'Rodriguez'
            WHEN congressman = 678 THEN 'Alberto'
            WHEN congressman = 679 THEN 'Undecided'
            WHEN congressman IS NULL THEN 'Undecided'
            ELSE 'Other'
        END AS cong,
        CASE
            WHEN governor = 662 THEN 'Bosste'
            WHEN governor = 663 THEN 'Asanza'
            WHEN governor = 680 THEN 'Undecided'
            WHEN governor IS NULL THEN 'Undecided'
            ELSE 'Other'
        END AS gov, 
        CASE
            WHEN vicegov = 676 THEN 'Fernandez'
            WHEN vicegov = 677 THEN 'Abundo'
            WHEN vicegov = 681 THEN 'Undecided'
            WHEN vicegov IS NULL THEN 'Undecided'
            ELSE 'Other'
        END AS vgov, 
        CASE
            WHEN mayor = 693 THEN 'Boboy'
            WHEN mayor = 694 THEN 'Posoy'
            WHEN mayor = 695 THEN 'Arcilla'
            WHEN mayor = 696 THEN 'Undecided'
            WHEN mayor IS NULL THEN 'Undecided'
            ELSE 'Other'
        END AS mayor
    FROM head_household
    INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id
    INNER JOIN barangays ON barangays.id = v_info.barangayId
    LEFT JOIN politics ON politics.v_id = v_info.v_id
    WHERE record_type = 1
    AND barangays.id = ?
    GROUP BY v_info.v_id
    ORDER BY v_lname, v_mname");
    
    $headQuery->bind_param("i", $brgyid);
    $headQuery->execute();
    $headResult = $headQuery->get_result();
    $headhouseholds = $headResult->fetch_all(MYSQLI_ASSOC);
    $headQuery->close();
    
    // Prepare statement for family members (will be reused for each household)
    $memberQuery = $conn->prepare("SELECT
        CONCAT(v_lname, ', ', v_fname, ' ', v_mname) as full_name,
        'Family Member' as role,
        CASE
            WHEN congressman = 660 THEN 'Laynes'
            WHEN congressman = 661 THEN 'Rodriguez'
            WHEN congressman = 678 THEN 'Alberto'
            WHEN congressman = 679 THEN 'Undecided'
            WHEN congressman IS NULL THEN 'Undecided'
            ELSE 'Other'
        END AS cong,
        CASE
            WHEN governor = 662 THEN 'Bosste'
            WHEN governor = 663 THEN 'Asanza'
            WHEN governor = 680 THEN 'Undecided'
            WHEN governor IS NULL THEN 'Undecided'
            ELSE 'Other'
        END AS gov,
        CASE
            WHEN vicegov = 676 THEN 'Fernandez'
            WHEN vicegov = 677 THEN 'Abundo'
            WHEN vicegov = 681 THEN 'Undecided'
            WHEN vicegov IS NULL THEN 'Undecided'
            ELSE 'Other'
        END AS vgov,
        CASE
            WHEN mayor = 693 THEN 'Boboy'
            WHEN mayor = 694 THEN 'Posoy'
            WHEN mayor = 695 THEN 'Arcilla'
            WHEN mayor = 696 THEN 'Undecided'
            WHEN mayor IS NULL THEN 'Undecided'
            ELSE 'Other'
        END AS mayor
    FROM household_warding
    INNER JOIN v_info ON v_info.v_id = household_warding.mem_v_id
    LEFT JOIN politics ON politics.v_id = v_info.v_id
    WHERE fh_v_id = ?
    ORDER BY v_lname, v_mname");
?>
<h3 class="mt-5"><?php echo htmlspecialchars($brgyname); ?> - Family Lists</h3>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-primary">
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Congressman</th>
                <th>Governor</th>
                <th>Vice Governor</th>
                <th>Mayor</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($headhouseholds && count($headhouseholds) > 0) {
                foreach ($headhouseholds as $head) {
                    // Escape data for display
                    $head['full_name'] = htmlspecialchars($head['full_name']);
            ?>
            <tr>
                <td><strong><?php echo $head['full_name']; ?></strong></td>
                <td><strong><?php echo $head['role']; ?></strong></td>
                <td><?php echo $head['cong']; ?></td>
                <td><?php echo $head['gov']; ?></td>
                <td><?php echo $head['vgov']; ?></td>
                <td><?php echo $head['mayor']; ?></td>
            </tr>
            <?php
                    // Get family members (reuses prepared statement)
                    $household_id = $head['household_id'];
                    $memberQuery->bind_param("i", $household_id);
                    $memberQuery->execute();
                    $memberResult = $memberQuery->get_result();
                    $familyMembers = $memberResult->fetch_all(MYSQLI_ASSOC);
                    
                    if ($familyMembers && count($familyMembers) > 0) {
                        foreach ($familyMembers as $member) {
                            // Escape data for display
                            $member['full_name'] = htmlspecialchars($member['full_name']);
            ?>
            <tr>
                <td class="ps-4"><?php echo $member['full_name']; ?></td>
                <td><?php echo $member['role']; ?></td>
                <td><?php echo $member['cong']; ?></td>
                <td><?php echo $member['gov']; ?></td>
                <td><?php echo $member['vgov']; ?></td>
                <td><?php echo $member['mayor']; ?></td>
            </tr>
            <?php
                        }
                    } else {
            ?>
            <tr>
                <td colspan="6" class="text-center">No family members found</td>
            </tr>
            <?php
                    }
                    
                    // Add divider row between families
                    echo '<tr class="table-secondary"><td colspan="6"></td></tr>';
                }
                
                // Close the member query after all households have been processed
                $memberQuery->close();
            } else {
            ?>
            <tr>
                <td colspan="6" class="text-center">No households found in this barangay</td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>
<?php
}
?>