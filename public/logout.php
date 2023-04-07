<?php 

/*
 * login.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Enables login via Microsoft Azure Directory
 */

    // Start PHP Session 
    session_start();
    session_destroy();
    header('Location: https://login.microsoftonline.com/common/oauth2/v2.0/logout?client_id=7b0feefa-d830-4aac-b302-ee754ac66acf&post_logout_redirect_uri=https%3A%2F%2Fwww.mossfreelancing.co.uk%2Findex.html');
    exit();
?>