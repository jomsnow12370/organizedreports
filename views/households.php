<?php

?>

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
                                        echo $brgyName . ', ' . $mun . "";
                                    } else {
                                        echo $mun . "";
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
<div class="row">
    <?php 
echo $munquery . '<br>';
echo $brgyquery . '<br>';

$barangays = get_array("SELECT barangay, id from barangays WHERE id is not null $munquery $brgyquery");
foreach ($barangays as $key => $value) {
  $barangay = $value["barangay"];
   $barangayid = $value["id"];
  
$not_warded = get_array(query: "SELECT 
    v_lname, v_fname, v_mname, municipality, barangay, v_birthday, v_gender
FROM
    v_info
        LEFT JOIN
    household_warding ON household_warding.mem_v_id = v_info.v_id
        INNER JOIN
    barangays ON barangays.id = v_info.barangayId
WHERE
    record_type = 1
    AND household_warding.mem_v_id IS NULL AND barangays.id = '$barangayid'");
}
echo "SELECT 
    v_lname, v_fname, v_mname, municipality, barangay, v_birthday, v_gender
FROM
    v_info
        LEFT JOIN
    household_warding ON household_warding.mem_v_id = v_info.v_id
        INNER JOIN
    barangays ON barangays.id = v_info.barangayId
WHERE
    record_type = 1
    AND household_warding.mem_v_id IS NULL AND barangays.id = '$barangayid'";
foreach ($not_warded as $key => $value) {
  //echo $value["v_lname"] . ', ' . $value["v_fname"] . ' ' . $value["v_mname"] . '<br>';
}
?>
</div>

<!-- Statistics Cards -->