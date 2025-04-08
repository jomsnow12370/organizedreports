<?php
$mun = isset($_GET['mun']) ? $_GET['mun'] : null;
$brgy = isset($_GET['brgy']) ? $_GET['brgy'] : null;
$barangays = get_array("SELECT barangay, id FROM barangays WHERE id is not null $munquery $brgyquery2");

foreach ($barangays as $barangay) {
    $brgyid = $barangay['id'];
    $brgyname = $barangay[0];
    
    // Get head of households with their IDs
    $headhouseholds = get_array("SELECT 
                                    municipality, 
                                    barangay, 
                                    v_lname, 
                                    v_fname, 
                                    v_mname, 
                                    'Head of Household' as role,
                                    head_household.fh_v_id as household_id,
                                    head_household.fh_v_id as person_id,
                                    CONCAT(v_lname, ', ', v_fname, ' ', v_mname) as full_name
                                 FROM head_household 
                                 INNER JOIN v_info ON v_info.v_id = head_household.fh_v_id 
                                 INNER JOIN barangays ON barangays.id = v_info.barangayId 
                                 LEFT JOIN v_remarks ON v_remarks.v_id = v_info.v_id 
                                 INNER JOIN quick_remarks ON quick_remarks.remarks_id = v_remarks.remarks_id 
                                 WHERE record_type = 1 
                                 AND barangays.id = $brgyid 
                                 AND quick_remarks.remarks_id = 661 ORDER BY v_lname, v_mname
                                 ");
    ?>
<h3>Rodriguez-tagged Family Heads <br><small style="font-size: 12px"><?php echo $brgyname . ', ' . $mun;?></small></h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <td>#</td>
            <td>Fullname</td>
            <td>Municipality</td>
            <td>Barangay</td>
        </tr>

        <?php
    foreach ($headhouseholds as $key => $value) {
        ?>
        <tr>
            <td><?php echo $key +1; ?></td>
            <td><?php echo $value["full_name"]?></td>
            <td><?php echo $value["municipality"]?></td>
            <td><?php echo $value["barangay"]?></td>
        </tr>
        <?php
    }
?>
    </thead>
</table>
<?php
$householdmembers = get_array("SELECT 
                                    municipality, 
                                    barangay, 
                                    v_lname, 
                                    v_fname, 
                                    v_mname, 
                                    household_warding.mem_v_id as person_id,
                                    CONCAT(v_lname, ', ', v_fname, ' ', v_mname) as full_name
                                 FROM household_warding 
                                 INNER JOIN v_info ON v_info.v_id = household_warding.mem_v_id 
                                 INNER JOIN barangays ON barangays.id = v_info.barangayId 
                                 LEFT JOIN v_remarks ON v_remarks.v_id = v_info.v_id 
                                 INNER JOIN quick_remarks ON quick_remarks.remarks_id = v_remarks.remarks_id 
                                 WHERE record_type = 1 
                                 AND barangays.id = $brgyid 
                                 AND quick_remarks.remarks_id = 661 ORDER BY v_lname, v_mname");
    ?>
<h3>Rodriguez-tagged Family Members <br><small style="font-size: 12px"><?php echo $brgyname . ', ' . $mun;?></small>
</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <td>#</td>
            <td>Fullname</td>
            <td>Municipality</td>
            <td>Barangay</td>
        </tr>

        <?php
    foreach ($householdmembers as $key => $value) {
        ?>
        <tr>
            <td><?php echo $key +1; ?></td>
            <td><?php echo $value["full_name"]?></td>
            <td><?php echo $value["municipality"]?></td>
            <td><?php echo $value["barangay"]?></td>
        </tr>
        <?php
    }
?>
    </thead>
</table>
<footer></footer>
<?php
}
?>