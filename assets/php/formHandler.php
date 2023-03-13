<?php 

/*
 * formHandler.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Handles form submissions
 */

    // Validation Function
    function validateInput($input, $expectedType, $expectedLength) {
        return true;
    }

    // Database Connection Info
    $HOST = '';
    $USER = 'mossfree_admin';
    $PASSWORD = 'Btf7@w&7Dhi1';
    $DATABASE = 'mossfree_';

    // DEBUGGING ONLY
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

    try {
        switch ($_POST["formID"]) {
            // addUser Form - [firstNameInput, lastNameInput, emailInput] / [Text, Text, Email] / [50,50,100]
            case "addUser":
                break;
            // editUser Form 
            case "editUser":
                break;
            // deleteUser Form - [deleteUserSelect] / [Text] / [50]
            case "deleteUser":
                break;
            // addModule Form - [moduleNameInput, ModuleConInput, ModuleDesInput, ModuleLinkInput] / [Text, Text, Text, URL] / [50, 50, 100, 2048]
            case "addModule":
                break;
            // editModule Form
            case "editModule":
                break;
            // deleteModule Form -  [deleteModuleSelect] / [Text] / [50]
            case "deleteModule":
                break;
            // viewSession Form
            case "viewSession":
                break;
            // addSession Form
            case "addSession":
                break;
            // Unknown Form ID
            default:
                echo "Error: Unknown Form ID";
                break;
        }
    } catch (Exception $e) {
        echo "Unknown Error";
    }

?>
