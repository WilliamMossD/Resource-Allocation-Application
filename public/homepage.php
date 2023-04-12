<?php

/*
 * homepage.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)  
 */

// Start PHP Session 
session_start();

require_once('../src/inc/utilities.php');

// Check is user is logged in
if (empty($_SESSION['loggedin'])) {
    header('Location: index.html?errorcode=5');
    echo 'No session ID';
    exit();
}

if (!$_SESSION['loggedin']) {
    header('Location: index.html?errorcode=5');
    exit();
}

if (empty($_SESSION['email'])) {
    header('Location: index.html?errorcode=5');
    exit();
}

// Connect to database
try {
    $con = mysqliConnect();
    if ($con->connect_error) {
        header('Location: index.html?errorcode=3');
        exit();
    }
} catch (Exception $e) {
    header('Location: index.html?errorcode=4');
    exit();
}

// Get session email and make sure its valid
if (!userExistsByEmail($_SESSION['email'], $con)) {
    header('Location: index.html?errorcode=1');
    exit();
}

// Get user data
$userData = getUserDataByEmail($_SESSION['email'], $con)->fetch_array(MYSQLI_ASSOC);
$name = getUserName($userData['ta_num'], $con);
$admin = $userData['admin'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Homepage - Tutor Scheduling Software</title>

    <!-- Meta Elements -->
    <meta charset="utf-8">
    <meta name="description" content="TALL - Homepage">
    <!-- Cand No: 235319 -->
    <meta name="author" content="William Moss">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="/assets/favicon/site.webmanifest">
    <link rel="mask-icon" href="/assets/favicon/safari-pinned-tab.svg" color="#02345a">
    <link rel="shortcut icon" href="/assets/favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-config" content="/assets/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <!-- Primary Stylesheets -->
    <link href="assets/css/styles.css?version=13" rel="stylesheet" type="text/css">

    <!-- Third Party Stylesheets -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <!-- JavaScript -->
    <script defer src="assets/js/functions.js?version=58"></script>
    <?php if ($admin == '1') : ?><script defer src="assets/js/adminfunctions.js?version=4"></script><?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script defer src="assets/js/brands.js"></script>
    <script defer src="assets/js/solid.js"></script>
    <script defer src="assets/js/fontawesome.js"></script>
</head>

<body class="bg-gradient min-vh-100" onload="load()">
    <div class="container-fluid p-0">
        <nav class="navbar">
            <div class="container-fluid text-center">
                <span class="col-4" id="time">HH:MM</span>
                <a class="navbar-brand col-4"><img src="assets/images/TALL.png" style="height: 1.8em;"></a>
                <span class="col-4" id="date">DD:MM:YYYY</span>
            </div>
        </nav>
    </div>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 d-flex user-row rounded shadow bg-lightblue p-5 mt-5">
                <div class="col-8 d-flex text-center">
                    <img class="profile-icon" src="assets/images/person-icon.png">
                    <div class="container ps-4 text-start">
                        <h1 class="display-6">Welcome, <?= $name ?></h1>
                        <h5 class="fw-light">Position: <?php if ($admin == '1') : ?>Admin<? else : ?>Teaching Assistant<?php endif; ?></h5>
                        <h5 class="fw-light" id='email'>Email: <?= $_SESSION['email'] ?></h5>
                        <h5 class="fw-light" id='id'>ID: <?= getUserIDByEmail($_SESSION['email'], $con) ?></h5>
                    </div>
                </div>
                <div class="col-4 text-center user-button">
                    <button type="button" class="btn btn-primary me-3"><i class="fa-solid fa-message fa-2xl"></i><br>
                        <p class="mb-1 mt-2 fs-6 text-wrap">Messages</p>
                    </button>
                    <button onclick="location.href='logout.php'" type="button" class="btn btn-primary"><i class="fa-solid fa-right-from-bracket fa-2xl"></i><br>
                        <p class="mb-1 mt-2 fs-6 text-wrap">Logout</p>
                    </button>
                </div>
            </div>
            <div class="col-12 p-0 mt-5 mb-5 d-lg-flex">
                <div class="col-lg-2 rounded shadow bg-lightblue p-3 mb-5" style="height: fit-content;">
                    <div class="nav flex-lg-column nav-pills justify-content-center" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <?php if ($admin == '0') : ?><button class="nav-link active" id="v-pills-timetable-tab" data-bs-toggle="pill" data-bs-target="#v-pills-timetable" type="button" role="tab" aria-controls="v-pills-timetable" aria-selected="true">Timetable</button><?php endif; ?>
                        <button <?php if ($admin == '0') : ?>class="nav-link" <? else : ?>class="nav-link active" <?php endif; ?> id="v-pills-work-tab" data-bs-toggle="pill" data-bs-target="#v-pills-work" type="button" role="tab" aria-controls="v-pills-work" aria-selected="false">Modules</button>
                        <?php if ($admin == '0') : ?><button class="nav-link" id="v-pills-availability-tab" data-bs-toggle="pill" data-bs-target="#v-pills-availability" type="button" role="tab" aria-controls="v-pills-availability" aria-selected="false">Availability</button><?php endif; ?>
                        <button class="nav-link" id="v-pills-timesheets-tab" data-bs-toggle="pill" data-bs-target="#v-pills-timesheets" type="button" role="tab" aria-controls="v-pills-timesheets" aria-selected="false">Timesheets</button>
                        <?php if ($admin == '1') : ?><button class="nav-link" id="v-pills-admin-tab" data-bs-toggle="pill" data-bs-target="#v-pills-admin" type="button" role="tab" aria-controls="v-pills-admin" aria-selected="false">Admin Menu</button><?php endif; ?>
                        <button class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill" data-bs-target="#v-pills-settings" type="button" role="tab" aria-controls="v-pills-settings" aria-selected="false">Settings</button>
                    </div>
                </div>
                <div class="col-lg-9 offset-lg-1 shadow rounded bg-lightblue p-3">
                    <div class="tab-content" id="v-pills-tabContent">
                        <?php if ($admin == '0') : ?>
                            <div class="tab-pane fade show active" id="v-pills-timetable" role="tabpanel" aria-labelledby="v-pills-timetable-tab" tabindex="0">
                                <h1 class="display-6 ps-3">Timetable</h1>
                                <hr>
                                <div class="container">
                                    <div class="row p-3">
                                        <?php include('../src/tpl/timetable.php') ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div <?php if ($admin == '0') : ?>class="tab-pane fade" <? else : ?>class="tab-pane fade active show" <?php endif; ?> id="v-pills-work" role="tabpanel" aria-labelledby="v-pills-work-tab" tabindex="0">
                            <h1 class="display-6 ps-3">Modules</h1>
                            <hr>
                            <div class="container p-3">
                                <div class="row" id="moduleCards">
                                    <?php include('assets/php/getModuleCards.php') ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($admin == '0') : ?>
                            <div class="tab-pane fade" id="v-pills-availability" role="tabpanel" aria-labelledby="v-pills-availability-tab" tabindex="0">
                                <h1 class="display-6 ps-3">Availability</h1>
                                <hr>
                                <p class="ps-3">Adjust your availability by clicking cells within the table to mark yourself as free/busy. A green stripped cell means you are available.</p>
                                <div class="container">
                                    <div class="row p-3">
                                        <div class="col-12">
                                            <?php include('../src/tpl/availtable.php') ?>
                                        </div>
                                        <div class="col-4 ps-4">
                                            <button type="button" id="clearAvail" class="btn btn-danger">Clear</button>
                                        </div>
                                        <div class="col-4 text-center">
                                            <button type="button" id="resetAvail" class="btn btn-warning">Reset</button>
                                        </div>
                                        <div class="col-4 text-end pe-4">
                                            <button type="button" id="saveAvail" class="btn btn-success">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="tab-pane fade" id="v-pills-timesheets" role="tabpanel" aria-labelledby="v-pills-timesheets-tab" tabindex="0">
                            <h1 class="display-6 ps-3">Timesheets</h1>
                            <hr>
                            <?php if ($admin == '0') : ?><?php include('../src/tpl/usertimesheettab.php') ?><? else : ?><?php include('../src/tpl/admintimesheettab.php') ?><?php endif; ?>
                        </div>
                        <?php if ($admin == '1') : ?><div class="tab-pane fade" id="v-pills-admin" role="tabpanel" aria-labelledby="v-pills-admin-tab" tabindex="0">
                                <h1 class="display-6 ps-3">Admin Menu</h1>
                                <hr>
                                <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="pills-usermanagement-tab" data-bs-toggle="pill" data-bs-target="#pills-usermanage" type="button" role="tab" aria-controls="pills-usermanage" aria-selected="true">User Management</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-modulemanagement-tab" data-bs-toggle="pill" data-bs-target="#pills-modulemanage" type="button" role="tab" aria-controls="pills-modulemanage" aria-selected="false">Module
                                            Management</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-sessionmanagement-tab" data-bs-toggle="pill" data-bs-target="#pills-sessionmanage" type="button" role="tab" aria-controls="pills-sessionmanage" aria-selected="false">Session
                                            Management</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-sessionallocation-tab" data-bs-toggle="pill" data-bs-target="#pills-sessionallocation" type="button" role="tab" aria-controls="pills-sessionallocation" aria-selected="false">Session
                                            Allocation</button>
                                    </li>
                                </ul>
                                <hr>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-usermanage" role="tabpanel" aria-labelledby="pills-usermanagement-tab" tabindex="0">
                                        <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="pills-adduser-tab" data-bs-toggle="pill" data-bs-target="#pills-adduser" type="button" role="tab" aria-controls="pills-adduser" aria-selected="true">Add User</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-edituser-tab" data-bs-toggle="pill" data-bs-target="#pills-edituser" type="button" role="tab" aria-controls="pills-edituser" aria-selected="false">Edit User</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-deleteuser-tab" data-bs-toggle="pill" data-bs-target="#pills-deleteuser" type="button" role="tab" aria-controls="pills-deleteuser" aria-selected="false">Delete
                                                    User</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-adduser" role="tabpanel" aria-labelledby="pills-adduser-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="addUser" name='addUser' class="row g-3 pt-2 pb-2">
                                                        <div class="col-6 mb-2">
                                                            <label for="firstNameInput" class="form-label">First
                                                                Name</label>
                                                            <input type="text" class="form-control custom-input" id="firstNameInput" name="firstNameInput" required>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <label for="lastNameInput" class="form-label">Last Name</label>
                                                            <input type="text" class="form-control custom-input" id="lastNameInput" name="lastNameInput" required>
                                                        </div>
                                                        <div class="col-12 mb-2">
                                                            <label for="emailInput" class="form-label">Email Address</label>
                                                            <input type="email" class="form-control custom-input" id="emailInput" name="emailInput" required>
                                                        </div>
                                                        <div class="col-12 mb-2 form-check" style="padding-left: 2rem !important;">
                                                            <input type="checkbox" class="form-check-input" id="adminCheck" name="adminCheck">
                                                            <label class="form-check-label" for="adminCheck">Make User
                                                                Admin</label>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Create User</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-edituser" role="tabpanel" aria-labelledby="pills-edituser-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="selectUser" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="userSelect" class="form-label">Select User</label>
                                                            <div class="input-group mb-3">
                                                                <select type="user" class="form-control custom-input" id="userSelect" name="userSelect" required>
                                                                    <?php include('assets/php/getUserList.php') ?>
                                                                </select>
                                                                <button type="submit" class="btn btn-primary ms-2">Select</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <form id="updateUser" class="row g-3 pt-2 pb-2">
                                                        <input type="hidden" id="editUserSelect" name="editUserSelect" value="">
                                                        <div class="col-6 mb-2">
                                                            <label for="editFirstNameInput" class="form-label">First
                                                                Name</label>
                                                            <input type="text" class="form-control custom-input" id="editFirstNameInput" name="editFirstNameInput" disabled>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <label for="editLastNameInput" class="form-label">Last
                                                                Name</label>
                                                            <input type="text" class="form-control custom-input" id="editLastNameInput" name="editLastNameInput" disabled>
                                                        </div>
                                                        <div class="col-12 mb-2">
                                                            <label for="editEmailInput" class="form-label">Email
                                                                Address</label>
                                                            <input type="email" class="form-control custom-input" id="editEmailInput" name="editEmailInput" disabled>
                                                        </div>
                                                        <div class="col-12 mb-2 form-check" style="padding-left: 2rem !important;">
                                                            <input type="checkbox" class="form-check-input" id="editAdminCheck" name="editAdminCheck" disabled>
                                                            <label class="form-check-label" for="editAdminCheck">Make User
                                                                Admin</label>
                                                        </div>
                                                        <button type="submit" id="savebtn" class="btn btn-primary" disabled>Save
                                                            Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-deleteuser" role="tabpanel" aria-labelledby="pills-deleteuser-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="deleteUser" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="deleteUserSelect" class="form-label">Select User</label>
                                                            <select type="user" class="form-control custom-input" id="deleteUserSelect" name="deleteUserSelect" required>
                                                                <?php include('assets/php/getUserList.php') ?>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Delete User</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-modulemanage" role="tabpanel" aria-labelledby="pills-modulemanagement-tab" tabindex="0">
                                        <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="pills-addmodule-tab" data-bs-toggle="pill" data-bs-target="#pills-addmodule" type="button" role="tab" aria-controls="pills-addmodule" aria-selected="true">Add
                                                    Module</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-editmodule-tab" data-bs-toggle="pill" data-bs-target="#pills-editmodule" type="button" role="tab" aria-controls="pills-editmodule" aria-selected="false">Edit
                                                    Module</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-deletemodule-tab" data-bs-toggle="pill" data-bs-target="#pills-deletemodule" type="button" role="tab" aria-controls="pills-deletemodule" aria-selected="false">Delete
                                                    Module</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-addmodule" role="tabpanel" aria-labelledby="pills-addmodule-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="addModule" class="row g-3 pt-2 pb-2">
                                                        <div class="col-6 mb-2">
                                                            <label for="moduleNameInput" class="form-label">Module
                                                                Name</label>
                                                            <input type="text" class="form-control custom-input" id="moduleNameInput" name="moduleNameInput" required>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <label for="moduleConInput" class="form-label">Module
                                                                Convenor</label>
                                                            <input type="text" class="form-control custom-input" id="moduleConInput" name="moduleConInput" required>
                                                        </div>
                                                        <div class="col-12 mb-2">
                                                            <label for="moduleDesInput" class="form-label">Module
                                                                Description</label>
                                                            <textarea class="form-control custom-textarea" id="moduleDesInput" name="moduleDesInput" rows="2" maxlength="100"></textarea>
                                                        </div>
                                                        <div class="col-12 mb-2">
                                                            <label for="moduleLinkInput" class="form-label">Canvas
                                                                Link</label>
                                                            <input type="url" class="form-control custom-input" id="moduleLinkInput" name="moduleLinkInput" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Create Module</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-editmodule" role="tabpanel" aria-labelledby="pills-editmodule-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="selectModule" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="moduleSelect" class="form-label">Select Module</label>
                                                            <div class="input-group mb-3">
                                                                <select type="module" class="form-control custom-input" id="moduleSelect" name="moduleSelect" required>
                                                                    <?php include('assets/php/getModuleList.php') ?>
                                                                </select>
                                                                <button type="submit" class="btn btn-primary ms-2">Select</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <form id="updateModule" class="row g-3 pt-2 pb-2">
                                                        <input type="hidden" id="editModuleSelect" name="editModuleSelect" value="">
                                                        <div class="col-6 mb-2">
                                                            <label for="editModuleNameInput" class="form-label">Module
                                                                Name</label>
                                                            <input type="text" class="form-control custom-input" id="editModuleNameInput" name="editModuleNameInput" required disabled>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <label for="editModuleConInput" class="form-label">Module
                                                                Convenor</label>
                                                            <input type="text" class="form-control custom-input" id="editModuleConInput" name="editModuleConInput" required disabled>
                                                        </div>
                                                        <div class="col-12 mb-2">
                                                            <label for="editModuleDesInput" class="form-label">Module
                                                                Description</label>
                                                            <textarea class="form-control custom-textarea" id="editModuleDesInput" name="editModuleDesInput" rows="2" maxlength="100" disabled></textarea>
                                                        </div>
                                                        <div class="col-12 mb-2">
                                                            <label for="editModuleLinkInput" class="form-label">Canvas
                                                                Link</label>
                                                            <input type="url" class="form-control custom-input" id="editModuleLinkInput" name="editModuleLinkInput" required disabled>
                                                        </div>
                                                        <button type="submit" id="savebtn2" class="btn btn-primary" disabled>Save
                                                            Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-deletemodule" role="tabpanel" aria-labelledby="pills-deletemodule-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="deleteModule" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="deleteModuleSelect" class="form-label">Select Module</label>
                                                            <select type="module" class="form-control custom-input" id="deleteModuleSelect" name="deleteModuleSelect" required>
                                                                <?php include('assets/php/getModuleList.php') ?>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Delete Module</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-sessionmanage" role="tabpanel" aria-labelledby="pills-sessionmanagement-tab" tabindex="0">
                                        <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="pills-viewsession-tab" data-bs-toggle="pill" data-bs-target="#pills-viewsession" type="button" role="tab" aria-controls="pills-viewsession" aria-selected="false">View
                                                    Sessions</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-addsession-tab" data-bs-toggle="pill" data-bs-target="#pills-addsession" type="button" role="tab" aria-controls="pills-addsession" aria-selected="true">Add
                                                    Session</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-editsession-tab" data-bs-toggle="pill" data-bs-target="#pills-editsession" type="button" role="tab" aria-controls="pills-editsession" aria-selected="true">Edit
                                                    Session</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-deletesession-tab" data-bs-toggle="pill" data-bs-target="#pills-deletesession" type="button" role="tab" aria-controls="pills-deletesession" aria-selected="true">Delete
                                                    Session</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-viewsession" role="tabpanel" aria-labelledby="pills-viewsession-tab" tabindex="0">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <form id="viewSession" class="row g-3 pt-2 pb-2">
                                                                <div class="col-12 mb-2">
                                                                    <label for="sessionsModuleSelect" class="form-label">Select Module</label>
                                                                    <div class="input-group mb-3">
                                                                        <select type="module" class="form-control custom-input" id="sessionsModuleSelect" name="sessionsModuleSelect" required>
                                                                            <?php include('assets/php/getModuleList.php') ?>
                                                                        </select>
                                                                        <button class="btn ms-3 btn-primary" type="submit">View Sessions</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="col-12" id="sessionsDiv">
                                                        </div>
                                                        <button type="button" class="btn btn-primary" style="display: none;" onclick="printTable('sessionsDiv')" id="sessionsTablePrint">Print</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-addsession" role="tabpanel" aria-labelledby="pills-addsession-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="addSession" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="sessionModuleNameInput" class="form-label">Module Name</label>
                                                            <select type="module" class="form-control custom-input" id="sessionModuleNameInput" name="sessionModuleNameInput" required>
                                                                <?php include('assets/php/getModuleList.php') ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-5 mb-2">
                                                            <label for="moduleLocInput" class="form-label">Session
                                                                Location</label>
                                                            <input type="text" class="form-control custom-input" id="moduleLocInput" name="moduleLocInput" required>
                                                        </div>
                                                        <div class="col-5 mb-2">
                                                            <label for="sessionTypeSelect" class="form-label">Type of
                                                                Session</label>
                                                            <select class="form-select custom-input" aria-label="Select Session Type" id="sessionTypeSelect" name="sessionTypeSelect" required>
                                                                <option value="">Select Session Type</option>
                                                                <option value="Lab">Lab</option>
                                                                <option value="Teaching">Teaching</option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-2 mb-2">
                                                            <label for="sessionTAInput" class="form-label">TA
                                                                Allocation</label>
                                                            <input type="number" class="form-control custom-input" id="sessionTAInput" name="sessionTAInput" min="1" max="5" required>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <label for="sessionDaySelect" class="form-label">Day of
                                                                Session</label>
                                                            <select class="form-select custom-input" aria-label="Select Session Day" id="sessionDaySelect" name="sessionDaySelect" required>
                                                                <option value="">Select Day of Week</option>
                                                                <option value="Monday">Monday</option>
                                                                <option value="Tuesday">Tuesday</option>
                                                                <option value="Wednesday">Wednesday</option>
                                                                <option value="Thursday">Thursday</option>
                                                                <option value="Friday">Friday</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-3 mb-2">
                                                            <label for="sessionStartTimeInput" class="form-label">Start
                                                                Time</label>
                                                            <input type="time" class="form-control custom-input" id="sessionStartTimeInput" name="sessionStartTimeInput" min="08:00" max="20:00" step="3600" required>
                                                        </div>
                                                        <div class="col-3 mb-2">
                                                            <label for="sessionEndTimeInput" class="form-label">End
                                                                Time</label>
                                                            <input type="time" class="form-control custom-input" id="sessionEndTimeInput" name="sessionEndTimeInput" min="09:00" max="21:00" step="3600" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Add Session</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-editsession" role="tabpanel" aria-labelledby="pills-editsession-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="selectSession" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="sessionSelect" class="form-label">Select Session</label>
                                                            <div class="input-group mb-3">
                                                                <select type="session" class="form-control custom-input" id="sessionSelect" name="sessionSelect" required>
                                                                    <?php include('assets/php/getSessionList.php') ?>
                                                                </select>
                                                                <button type="submit" class="btn btn-primary ms-2">Select</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <form id="updateSession" class="row g-3 pt-2 pb-2">
                                                        <input type="hidden" id="editSessionSelect" name="editSessionSelect" value="">
                                                        <div class="col-12 mb-2">
                                                            <label for="editSessionModuleNameInput" class="form-label">Module Name</label>
                                                            <select type="module" class="form-control custom-input" id="editSessionModuleNameInput" name="editSessionModuleNameInput" required disabled>
                                                                <?php include('assets/php/getModuleList.php') ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-5 mb-2">
                                                            <label for="editSessionLocInput" class="form-label">Session
                                                                Location</label>
                                                            <input type="text" class="form-control custom-input" id="editSessionLocInput" name="editSessionLocInput" required disabled>
                                                        </div>
                                                        <div class="col-5 mb-2">
                                                            <label for="editSessionTypeSelect" class="form-label">Type of
                                                                Session</label>
                                                            <select class="form-select custom-input" aria-label="Select Session Type" id="editSessionTypeSelect" name="editSessionTypeSelect" required disabled>
                                                                <option value="">Select Session Type</option>
                                                                <option value="Lab">Lab</option>
                                                                <option value="Teaching">Teaching</option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-2 mb-2">
                                                            <label for="editSessionTAInput" class="form-label">TA
                                                                Allocation</label>
                                                            <input type="number" class="form-control custom-input" id="editSessionTAInput" name="editSessionTAInput" min="1" required disabled>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <label for="editSessionDaySelect" class="form-label">Day of
                                                                Session</label>
                                                            <select class="form-select custom-input" aria-label="Select Session Day" id="editSessionDaySelect" name="editSessionDaySelect" required disabled>
                                                                <option value="">Select Day of Week</option>
                                                                <option value="Monday">Monday</option>
                                                                <option value="Tuesday">Tuesday</option>
                                                                <option value="Wednesday">Wednesday</option>
                                                                <option value="Thursday">Thursday</option>
                                                                <option value="Friday">Friday</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-3 mb-2">
                                                            <label for="editSessionStartTimeInput" class="form-label">Start
                                                                Time</label>
                                                            <input type="time" class="form-control custom-input" id="editSessionStartTimeInput" name="editSessionStartTimeInput" min="08:00" max="20:30" step="1800" required disabled>
                                                        </div>
                                                        <div class="col-3 mb-2">
                                                            <label for="editSessionEndTimeInput" class="form-label">End
                                                                Time</label>
                                                            <input type="time" class="form-control custom-input" id="editSessionEndTimeInput" name="editSessionEndTimeInput" min="08:30" max="21:00" step="1800" required disabled>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary" id="savebtn3">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-deletesession" role="tabpanel" aria-labelledby="pills-deletesession-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="deleteSession" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="deleteSessionSelect" class="form-label">Select Session</label>
                                                            <select type="session" class="form-control custom-input" id="deleteSessionSelect" name="deleteSessionSelect" required>
                                                                <?php include('assets/php/getSessionList.php') ?>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Delete Session</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-sessionallocation" role="tabpanel" aria-labelledby="pills-sessionallocation-tab" tabindex="0">
                                        <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="pills-viewallocation-tab" data-bs-toggle="pill" data-bs-target="#pills-viewallocation" type="button" role="tab" aria-controls="pills-viewallocation" aria-selected="false">View
                                                    Allocation</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-manualallocation-tab" data-bs-toggle="pill" data-bs-target="#pills-manualallocation" type="button" role="tab" aria-controls="pills-manualallocation" aria-selected="true">Manual
                                                    Allocation</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-autoallocation-tab" data-bs-toggle="pill" data-bs-target="#pills-autoallocation" type="button" role="tab" aria-controls="pills-autoallocation" aria-selected="true">Automatic
                                                    Allocation</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-viewallocation" role="tabpanel" aria-labelledby="pills-viewallocation-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="viewAllocationByModule" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="viewAllocModuleSelect" class="form-label">Select Module</label>
                                                            <div class="input-group mb-3">
                                                                <select type="module" class="form-control custom-input" id="viewAllocModuleSelect" name="viewAllocModuleSelect" required>
                                                                    <?php include('assets/php/getModuleList.php') ?>
                                                                </select>
                                                                <button class="btn ms-3 btn-primary" type="submit">View Allocation</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="col-12 mb-2">
                                                        <p class="mb-0 text-center"> or </p>
                                                    </div>
                                                    <form id="viewAllocationBySession" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="viewAllocSessionSelect" class="form-label">Select Session</label>
                                                            <div class="input-group mb-3">
                                                                <select type="session" class="form-control custom-input" id="viewAllocSessionSelect" name="viewAllocSessionSelect" required>
                                                                    <?php include('assets/php/getSessionList.php') ?>
                                                                </select>
                                                                <button class="btn ms-3 btn-primary" type="submit">View Allocation</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="col-12 mb-2">
                                                        <p class="mb-0 text-center"> or </p>
                                                    </div>
                                                    <form id="viewAllocationByUser" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="viewAllocUserSelect" class="form-label">Select User</label>
                                                            <div class="input-group mb-3">
                                                                <select type="user" class="form-control custom-input" id="viewAllocUserSelect" name="viewAllocUserSelect" required>
                                                                    <?php include('assets/php/getUserList.php') ?>
                                                                </select>
                                                                <button class="btn ms-3 btn-primary" type="submit">View Allocation</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="col-12" id="allocationDiv">
                                                    </div>
                                                    <button type="button" class="btn btn-primary" style="display: none;" onclick="printTable('allocationDiv')" id="allocationDivPrint">Print</button>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-manualallocation" role="tabpanel" aria-labelledby="pills-manualallocation-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="manualAlloc" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="manualAllocSessionSelect" class="form-label">Select Session</label>
                                                            <select type="session" class="form-control custom-input" id="manualAllocSessionSelect" name="manualAllocSessionSelect" required>
                                                                <?php include('assets/php/getSessionList.php') ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 mb-2">
                                                            <label for="manualAllocUserSelect" class="form-label">Select User(s)</label>
                                                            <select type="user" class="form-control custom-input" id="manualAllocUserSelect" name="manualAllocUserSelect[]" multiple required>
                                                                <?php include('assets/php/getUserList.php') ?>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Allocate</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-autoallocation" role="tabpanel" aria-labelledby="pills-autoallocation-tab" tabindex="0">
                                                <div class="container">
                                                    <form id="autoAlloc" class="row g-3 pt-2 pb-2">
                                                        <div class="col-12 mb-2">
                                                            <label for="autoAllocSessionSelect" class="form-label">Select Session</label>
                                                            <select type="session" class="form-control custom-input" id="autoAllocSessionSelect" name="autoAllocSessionSelect" required>
                                                                <?php include('assets/php/getSessionList.php') ?>
                                                            </select>
                                                        </div>
                                                        <p class="fw-light mt-0">NOTE: Users that are admins will not be automatically allocated to sessions</p>
                                                        <button type="submit" class="btn btn-primary">Auto Allocate</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><?php endif; ?>
                        <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab" tabindex="0">
                            <h1 class="display-6 ps-3">Settings</h1>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>