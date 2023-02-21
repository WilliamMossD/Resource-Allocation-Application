<?php 

/*
 * formHandler.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Handles form submissions
 */

    // Database Connection Info
    $HOST = '';
    $USER = 'mossfree_admin';
    $PASSWORD = 'Btf7@w&7Dhi1';
    $DATABASE = 'mossfree_';

    echo(print_r($_POST));

    // Connect to database
    try {
        $con = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE);
        if ($conn->connect_error) {
            echo "Connection Failed";
        } 
    } catch (Exception $e) {
        echo "Unknown Error. Please try again";
    }

?>
