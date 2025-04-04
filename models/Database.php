<?php

    /*     
    *THIS MODELFOR DB CONNECTION IS CURRENTLY NOT USED, THE LOGIC IS CURRENTLY IN THE CONN.PHP
    *
    *
    *
    *
    *
    *THIS IS FOR FUTURE REFERENCE ONLY, IF YOU WANT TO USE THIS , YOU CAN REQUIRE THIS FILE IN THE CONN.PHP
    */
    
function get_db_connection() {
        // Your database connection code here
        $c = mysqli_connect("localhost", "root", "", "db");
        if (!$c) {
            die("Connection failed: " . mysqli_connect_error());
        }
        return $c;
}

function db_query($sql) {
    return mysqli_query(get_db_connection(), $sql);
}

function get_value($sql) {

}

function get_array($sql) {

}
?> 