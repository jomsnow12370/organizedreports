<?php
function count_leaders($c, $leader_type, $munquery, $brgyquery) {
    $query = "SELECT COUNT(*) from leaders 
              INNER JOIN v_info ON leaders.v_id = v_info.v_id 
              INNER JOIN barangays ON barangays.id = v_info.barangayId 
              WHERE leaders.type = $leader_type 
              AND v_info.record_type = 1 
              AND electionyear = 2025 
              AND status is null 
                $munquery $brgyquery
              GROUP by leaders.v_id";
    $result = mysqli_query($c, $query);
    return mysqli_num_rows($result);
}
?>