<?php
function count_household($c, $munquery, $brgyquery) {
    $query = "SELECT SUM(households) AS total_households 
              FROM barangays 
              WHERE id IS NOT NULL 
              $munquery 
              $brgyquery";
    
    $result = mysqli_query($c, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total_households'] ?? 0;
}

function count_survey_responses($c, $remarks_txt, $is_household_head = true, $munquery, $brgyquery) {
    $table = $is_household_head ? "head_household" : "household_warding";
    $id_field = $is_household_head ? "fh_v_id" : "mem_v_id";
    
    $query = "SELECT COUNT(*)
              FROM $table
              INNER JOIN v_info ON v_info.v_id = $table.$id_field
              INNER JOIN v_remarks ON v_remarks.v_id = $table.$id_field
              INNER JOIN barangays ON barangays.id = v_info.barangayId
              INNER JOIN quick_remarks ON quick_remarks.remarks_id = v_remarks.remarks_id
              WHERE record_type = 1  $munquery $brgyquery
              AND remarks_txt = '$remarks_txt'

              GROUP BY v_remarks.v_id";
    
    $result = mysqli_query($c, $query);
    return mysqli_num_rows($result);
}