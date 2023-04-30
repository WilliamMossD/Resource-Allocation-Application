<?php 

/*
 * login.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Enables login via Microsoft Azure Directory
 */

    // Start PHP Session 
    session_start();

    require_once ('../vendor/autoload.php');

    // Load .env file
    $dotenv = Dotenv\Dotenv::createImmutable('../config');
    $dotenv->load();
    $dotenv->required(['LOGOUT_URL']);

    session_destroy();
    header('Location: '. $_ENV['LOGOUT_URL']);
    exit();
?>