<?php 

/*
 * homepage.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)  
 */

 // Start PHP Session 
 session_start();

 try {

    // Database Connection Info
    $HOST = 'localhost';
    $USER = 'mossfree_admin';
    $PASSWORD = 'Btf7@w&7Dhi1';
    $DATABASE = 'mossfree_tutordatabase';

    try {
        $con = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE);
    } catch (Exception $e) {
        // Redirect back to root
        header('Location: index.html');
        exit();
    }

    if (mysqli_connect_errno()) {
        // Redirect back to root
        header('Location: index.html');
        exit();
    }

    // Get rows from module table
    $stmt = $con->prepare('SELECT * from modules ORDER BY module_num ASC');
    $stmt->execute();
    $modulerows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get rows from teaching assistants table 
    $stmt = $con->prepare('SELECT ta_num, fname, lname from teaching_assistants ORDER BY ta_num ASC');
    $stmt->execute();
    $tarows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    header('Location: index.html');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tutor Scheduling Software</title>

    <!-- Meta Elements -->
    <meta charset="utf-8">
    <meta name="description" content="Local Antique Dealer">
    <!-- Cand No: 235319 -->
    <meta name="author" content="William Moss">

    <!-- Primary Stylesheets -->
    <link href="assets/css/styles.css" rel="stylesheet" type="text/css">

    <!-- Third Party Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <!-- JavaScript -->
    <script defer src="assets/js/functions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <script defer src="assets/js/brands.js"></script>
    <script defer src="assets/js/solid.js"></script>
    <script defer src="assets/js/fontawesome.js"></script>
</head>
<body class="bg-body" onload="load()">
    <div class="container-fluid p-0">
        <nav class="navbar bg-dark" data-bs-theme="dark">
            <div class="container-fluid text-center">
                <span class="col-4" id="time">HH:MM</span>
                <a class="navbar-brand col-4">Tutor Scheduling Software</a>
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
                        <h1 class="display-6">Welcome, John Doe</h1>
                        <h5 class="fw-light">Position: *Insert Position Here*</h2>
                            <h5 class="fw-light">Email: *Insert Email*</h2>
                    </div>
                </div>
                <div class="col-4 text-center user-button">
                    <button type="button" class="btn btn-primary me-4"><i class="fa-solid fa-message fa-2xl"></i><br>
                        <p class="mb-1 mt-2">Messages</p>
                    </button>
                    <button type="button" class="btn btn-primary"><i
                            class="fa-solid fa-right-from-bracket fa-2xl"></i><br>
                        <p class="mb-1 mt-2">Logout</p>
                    </button>
                </div>
            </div>
            <div class="col-12 p-0 mt-5 mb-5 d-flex">
                <div class="col-2 rounded shadow bg-lightblue p-3" style="height: fit-content;">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="v-pills-timetable-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-timetable" type="button" role="tab"
                            aria-controls="v-pills-timetable" aria-selected="true">Timetable</button>
                        <button class="nav-link" id="v-pills-work-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-work" type="button" role="tab" aria-controls="v-pills-work"
                            aria-selected="false">Modules</button>
                        <button class="nav-link" id="v-pills-avaliability-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-avaliability" type="button" role="tab"
                            aria-controls="v-pills-avaliability" aria-selected="false">Avaliability</button>
                        <button class="nav-link" id="v-pills-timesheets-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-timesheets" type="button" role="tab"
                            aria-controls="v-pills-timesheets" aria-selected="false">Timesheets</button>
                        <button class="nav-link" id="v-pills-admin-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-admin" type="button" role="tab" aria-controls="v-pills-admin"
                            aria-selected="false">Admin Menu</button>
                        <button class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-settings" type="button" role="tab" aria-controls="v-pills-settings"
                            aria-selected="false">Settings</button>
                    </div>
                </div>
                <div class="col-95 offset-05 shadow rounded bg-lightblue p-3">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-timetable" role="tabpanel"
                            aria-labelledby="v-pills-timetable-tab" tabindex="0">
                            <h1 class="display-6 ps-3">Timetable</h1>
                            <hr>
                            <div class="container">
                                <div class="row p-3">
                                    <table class="table text-center fw-normal table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="first-cell" scope="col"></th>
                                                <th scope="col">Monday</th>
                                                <th scope="col">Tuesday</th>
                                                <th scope="col">Wednesday</th>
                                                <th scope="col">Thursday</th>
                                                <th scope="col">Friday</th>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <th scope="row">08:00</th>
                                            <td id="08Mon"></td>
                                            <td id="08Tue"></td>
                                            <td id="08Wed"></td>
                                            <td id="08Thu"></td>
                                            <td id="08Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">09:00</th>
                                            <td id="09Mon"></td>
                                            <td id="09Tue"></td>
                                            <td id="09Wed"></td>
                                            <td id="09Thu"></td>
                                            <td id="09Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">10:00</th>
                                            <td id="10Mon"></td>
                                            <td id="10Tue"></td>
                                            <td id="10Wed"></td>
                                            <td id="10Thu"></td>
                                            <td id="10Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">11:00</th>
                                            <td id="11Mon"></td>
                                            <td id="11Tue"></td>
                                            <td id="11Wed"></td>
                                            <td id="11Thu"></td>
                                            <td id="11Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">12:00</th>
                                            <td id="12Mon"></td>
                                            <td id="12Tue"></td>
                                            <td id="12Wed"></td>
                                            <td id="12Thu"></td>
                                            <td id="12Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">13:00</th>
                                            <td id="13Mon"></td>
                                            <td id="13Tue"></td>
                                            <td id="13Wed"></td>
                                            <td id="13Thu"></td>
                                            <td id="13Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">14:00</th>
                                            <td id="14Mon"></td>
                                            <td id="14Tue"></td>
                                            <td id="14Wed"></td>
                                            <td id="14Thu"></td>
                                            <td id="14Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">15:00</th>
                                            <td id="15Mon"></td>
                                            <td id="15Tue"></td>
                                            <td id="15Wed"></td>
                                            <td id="15Thu"></td>
                                            <td id="15Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">16:00</th>
                                            <td id="16Mon"></td>
                                            <td id="16Tue"></td>
                                            <td id="16Wed"></td>
                                            <td id="16Thu"></td>
                                            <td id="16Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">17:00</th>
                                            <td id="17Mon"></td>
                                            <td id="17Tue"></td>
                                            <td id="17Wed"></td>
                                            <td id="17Thu"></td>
                                            <td id="17Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">18:00</th>
                                            <td id="18Mon"></td>
                                            <td id="18Tue"></td>
                                            <td id="18Wed"></td>
                                            <td id="18Thu"></td>
                                            <td id="18Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">19:00</th>
                                            <td id="19Mon"></td>
                                            <td id="19Tue"></td>
                                            <td id="19Wed"></td>
                                            <td id="19Thu"></td>
                                            <td id="19Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">20:00</th>
                                            <td id="20Mon"></td>
                                            <td id="20Tue"></td>
                                            <td id="20Wed"></td>
                                            <td id="20Thu"></td>
                                            <td id="20Fri"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">21:00</th>
                                            <td id="21Mon"></td>
                                            <td id="21Tue"></td>
                                            <td id="21Wed"></td>
                                            <td id="21Thu"></td>
                                            <td id="21Fri"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="v-pills-work" role="tabpanel" aria-labelledby="v-pills-work-tab"
                            tabindex="0">
                            <h1 class="display-6 ps-3">Modules</h1>
                            <hr>
                            <div class="container p-3">
                                <div class="row">
                                    <?php foreach ($modulerows as $row) : ?>
                                        <div class="col-4">
                                            <div class="card bg-primary">
                                                <div class="card-header">
                                                    <h6 class="mt-2 mb-2"><?= $row['module_name'] ?> (ID: <?= $row['module_num'] ?>)</h6>
                                                </div>
                                                <div class="card-body">
                                                    <h6 class="card-subtitle mb-2"><i class="fa-solid fa-chalkboard-user"></i> :
                                                    <?= $row['module_convenor'] ?></h6>
                                                    <p class="card-text"><?= $row['module_description'] ?></p>
                                                    <a href="<?= $row['link'] ?>" class="card-link link-info" target="_blank">Canvas Page</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="v-pills-avaliability" role="tabpanel"
                            aria-labelledby="v-pills-avaliability-tab" tabindex="0">
                            <h1 class="display-6 ps-3">Avaliability</h1>
                            <hr>
                            <p class="ps-3">Adjust your avaliability by clicking cells within the table to mark yourself as free/busy. A stripped cell means you are free.</p>
                            <div class="container">
                                <div class="row p-3">
                                    <div class="col-12">
                                        <table id="avaltable"
                                            class="table text-center fw-normal table-striped table-bordered aval-table">
                                            <thead>
                                                <tr>
                                                    <th class="first-cell" scope="col"></th>
                                                    <th scope="col">Monday</th>
                                                    <th scope="col">Tuesday</th>
                                                    <th scope="col">Wednesday</th>
                                                    <th scope="col">Thursday</th>
                                                    <th scope="col">Friday</th>
                                                </tr>
                                            </thead>
                                            <tr>
                                                <th scope="row">08:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">09:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">10:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">11:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">12:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">13:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">14:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">15:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">16:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">17:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">18:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">19:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">20:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">21:00</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-4 ps-4">
                                        <button type="button" id="clearAval" class="btn btn-danger">Clear</button>
                                    </div>
                                    <div class="col-4 text-center">
                                        <button type="button" id="resetAval" class="btn btn-warning">Reset</button>
                                    </div>
                                    <div class="col-4 text-end pe-4">
                                        <button type="button" id="saveAval" class="btn btn-success">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="v-pills-timesheets" role="tabpanel"
                            aria-labelledby="v-pills-timesheets-tab" tabindex="0">
                            <h1 class="display-6 ps-3">Timesheets</h1>
                            <hr>
                        </div>
                        <div class="tab-pane fade" id="v-pills-admin" role="tabpanel"
                            aria-labelledby="v-pills-admin-tab" tabindex="0">
                            <h1 class="display-6 ps-3">Admin Menu</h1>
                            <hr>
                            <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-usermanagement-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-usermanage" type="button" role="tab"
                                        aria-controls="pills-usermanage" aria-selected="true">User Management</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-modulemanagement-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-modulemanage" type="button" role="tab"
                                        aria-controls="pills-modulemanage" aria-selected="false">Module
                                        Management</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-sessionmanagement-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-sessionmanage" type="button" role="tab"
                                        aria-controls="pills-sessionmanage" aria-selected="false">Session
                                        Management</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-sessionallocation-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-sessionallocation" type="button" role="tab"
                                        aria-controls="pills-sessionallocation" aria-selected="false">Session
                                        Allocation</button>
                                </li>
                            </ul>
                            <hr>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-usermanage" role="tabpanel"
                                    aria-labelledby="pills-usermanagement-tab" tabindex="0">
                                    <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-adduser-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-adduser" type="button" role="tab"
                                                aria-controls="pills-adduser" aria-selected="true">Add User</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-edituser-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-edituser" type="button" role="tab"
                                                aria-controls="pills-edituser" aria-selected="false">Edit User</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-deleteuser-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-deleteuser" type="button" role="tab"
                                                aria-controls="pills-deleteuser" aria-selected="false">Delete
                                                User</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-adduser" role="tabpanel"
                                            aria-labelledby="pills-adduser-tab" tabindex="0">
                                            <div class="container">
                                                <form id="addUser" name='addUser' class="row g-3 pt-2 pb-2">
                                                    <div class="col-6 mb-2">
                                                        <label for="firstNameInput" class="form-label">First
                                                            Name</label>
                                                        <input type="text" class="form-control custom-input"
                                                            id="firstNameInput" name="firstNameInput" required>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <label for="lastNameInput" class="form-label">Last Name</label>
                                                        <input type="text" class="form-control custom-input"
                                                            id="lastNameInput" name="lastNameInput" required>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <label for="emailInput" class="form-label">Email Address</label>
                                                        <input type="email" class="form-control custom-input"
                                                            id="emailInput" name="emailInput" required>
                                                    </div>
                                                    <div class="col-12 mb-2 form-check"
                                                        style="padding-left: 2rem !important;">
                                                        <input type="checkbox" class="form-check-input" id="adminCheck" name="adminCheck">
                                                        <label class="form-check-label" for="adminCheck">Make User
                                                            Admin</label>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Create User</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-edituser" role="tabpanel"
                                            aria-labelledby="pills-edituser-tab" tabindex="0">
                                            <div class="container">
                                                <form id="selectUser" class="row g-3 pt-2 pb-2">
                                                    <div class="col-12 mb-2">
                                                        <label for="userSelect" class="form-label">Enter User's
                                                            ID</label>
                                                        <div class="input-group mb-3">
                                                            <input class="form-control custom-input" list="userList"
                                                                id="userSelect" name="userSelect" required>
                                                            <button type="submit" class="btn btn-primary ms-2">Select</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <form id="updateUser" class="row g-3 pt-2 pb-2">
                                                    <div class="col-6 mb-2">
                                                        <label for="editFirstNameInput" class="form-label">First
                                                            Name</label>
                                                        <input type="text" class="form-control custom-input"
                                                            id="editFirstNameInput" name="editFirstNameInput" disabled>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <label for="editLastNameInput" class="form-label">Last
                                                            Name</label>
                                                        <input type="text" class="form-control custom-input"
                                                            id="editLastNameInput" name="editLastNameInput" disabled>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <label for="editEmailInput" class="form-label">Email
                                                            Address</label>
                                                        <input type="email" class="form-control custom-input"
                                                            id="editEmailInput" name="editEmailInput" disabled>
                                                    </div>
                                                    <div class="col-12 mb-2 form-check"
                                                        style="padding-left: 2rem !important;">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="editAdminCheck" name="editAdminCheck" disabled>
                                                        <label class="form-check-label" for="editAdminCheck">Make User
                                                            Admin</label>
                                                    </div>
                                                    <input type="hidden" id="editUserSelect" name="editUserSelect" value="">
                                                    <button type="submit" id="savebtn" class="btn btn-primary" disabled>Save
                                                        Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-deleteuser" role="tabpanel"
                                            aria-labelledby="pills-deleteuser-tab" tabindex="0">
                                            <div class="container">
                                                <form id="deleteUser" class="row g-3 pt-2 pb-2">
                                                    <div class="col-12 mb-2">
                                                        <label for="deleteUserSelect" class="form-label">Enter User's
                                                            ID</label>
                                                        <input class="form-control custom-input" list="userList"
                                                            id="deleteUserSelect" name="deleteUserSelect" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Delete User</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-modulemanage" role="tabpanel"
                                    aria-labelledby="pills-modulemanagement-tab" tabindex="0">
                                    <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-addmodule-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-addmodule" type="button"
                                                role="tab" aria-controls="pills-addmodule" aria-selected="true">Add
                                                Module</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-editmodule-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-editmodule" type="button" role="tab"
                                                aria-controls="pills-editmodule" aria-selected="false">Edit
                                                Module</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-deletemodule-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-deletemodule" type="button" role="tab"
                                                aria-controls="pills-deletemodule" aria-selected="false">Delete
                                                Module</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-addmodule" role="tabpanel"
                                            aria-labelledby="pills-addmodule-tab" tabindex="0">
                                            <div class="container">
                                                <form id="addModule" class="row g-3 pt-2 pb-2">
                                                    <div class="col-6 mb-2">
                                                        <label for="moduleNameInput" class="form-label">Module
                                                            Name</label>
                                                        <input type="text" class="form-control custom-input"
                                                            id="moduleNameInput" name="moduleNameInput" required>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <label for="moduleConInput" class="form-label">Module
                                                            Convenor</label>
                                                        <input type="text" class="form-control custom-input"
                                                            id="moduleConInput" name="moduleConInput" required>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <label for="moduleDesInput" class="form-label">Module
                                                            Description</label>
                                                        <textarea class="form-control custom-textarea"
                                                            id="moduleDesInput" name="moduleDesInput" rows="2" maxlength="100"></textarea>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <label for="moduleLinkInput" class="form-label">Canvas
                                                            Link</label>
                                                        <input type="url" class="form-control custom-input"
                                                            id="moduleLinkInput" name="moduleLinkInput" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Create Module</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-editmodule" role="tabpanel"
                                            aria-labelledby="pills-editmodule-tab" tabindex="0">
                                            <div class="container">
                                                <form id="selectModule" class="row g-3 pt-2 pb-2">
                                                    <div class="col-12 mb-2">
                                                        <label for="moduleSelect" class="form-label">Enter Module
                                                            ID</label>
                                                        <div class="input-group mb-3">
                                                            <input class="form-control custom-input" list="moduleList"
                                                                id="moduleSelect" name="moduleSelect" required>
                                                            <button type="submit" class="btn btn-primary ms-2">Select</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <form id="updateModule" class="row g-3 pt-2 pb-2">
                                                    <div class="col-6 mb-2">
                                                        <label for="editModuleNameInput" class="form-label">Module
                                                            Name</label>
                                                        <input type="text" class="form-control custom-input"
                                                            id="editModuleNameInput" name="editModuleNameInput" required disabled>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <label for="editModuleConInput" class="form-label">Module
                                                            Convenor</label>
                                                        <input type="text" class="form-control custom-input"
                                                            id="editModuleConInput" name="editModuleConInput" required disabled>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <label for="editModuleDesInput" class="form-label">Module
                                                            Description</label>
                                                        <textarea class="form-control custom-textarea"
                                                            id="editModuleDesInput" name="editModuleDesInput" rows="2" maxlength="100"
                                                            disabled></textarea>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <label for="editModuleLinkInput" class="form-label">Canvas
                                                            Link</label>
                                                        <input type="url" class="form-control custom-input"
                                                            id="editModuleLinkInput" name="editModuleLinkInput" required disabled>
                                                    </div>
                                                    <input type="hidden" id="editModuleSelect" name="editModuleSelect" value="">
                                                    <button type="submit" id="savebtn2" class="btn btn-primary" disabled>Save
                                                        Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-deletemodule" role="tabpanel"
                                            aria-labelledby="pills-deletemodule-tab" tabindex="0">
                                            <div class="container">
                                                <form id="deleteModule" class="row g-3 pt-2 pb-2">
                                                    <div class="col-12 mb-2">
                                                        <label for="deleteModuleSelect" class="form-label">Enter Module
                                                            ID</label>
                                                        <input class="form-control custom-input" list="moduleList"
                                                            id="deleteModuleSelect" name="deleteModuleSelect" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Delete Module</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-sessionmanage" role="tabpanel"
                                    aria-labelledby="pills-sessionmanagement-tab" tabindex="0">
                                    <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-viewsession-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-viewsession" type="button"
                                                role="tab" aria-controls="pills-viewsession" aria-selected="false">View
                                                Sessions</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-addsession-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-addsession" type="button" role="tab"
                                                aria-controls="pills-addsession" aria-selected="true">Add
                                                Session</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-viewsession" role="tabpanel"
                                            aria-labelledby="pills-viewsession-tab" tabindex="0">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <form id="viewSession" class="row g-3 pt-2 pb-2">
                                                            <div class="col-12 mb-2">
                                                                <label for="sessionsModuleSelect" class="form-label">Enter
                                                                    Module
                                                                    ID</label>
                                                                <div class="input-group mb-3">
                                                                    <input class="form-control custom-input" list="moduleList"
                                                                        id="sessionsModuleSelect" name="sessionsModuleSelect" required>
                                                                    <button class="btn ms-3 btn-primary" type="submit">View Sessions</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="col-12">
                                                        <table class="table table-striped" style="display: none;" id="sessionsTable">
                                                            <thead>
                                                                <tr>
                                                                <th scope="col">Session ID</th>
                                                                <th scope="col">Day</th>
                                                                <th scope="col">Start Time</th>
                                                                <th scope="col">End Time</th>
                                                                <th scope="col">Session Type</th>
                                                                <th scope="col">Location</th>
                                                                <th scope="col">Num of TA</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-addsession" role="tabpanel"
                                            aria-labelledby="pills-addsession-tab" tabindex="0">
                                            <div class="container">
                                                <form id="addSession" class="row g-3 pt-2 pb-2">
                                                    <div class="col-12 mb-2">
                                                        <label for="sessionModuleNameInput" class="form-label">Module
                                                            ID</label>
                                                        <input class="form-control custom-input" list="moduleList"
                                                            id="sessionModuleNameInput" name="sessionModuleNameInput" required>
                                                    </div>
                                                    <div class="col-5 mb-2">
                                                        <label for="moduleLocInput" class="form-label">Session
                                                            Location</label>
                                                        <input type="text" class="form-control custom-input"
                                                            id="moduleLocInput" name="moduleLocInput" required>
                                                    </div>
                                                    <div class="col-5 mb-2">
                                                        <label for="sessionTypeSelect" class="form-label">Type of
                                                            Session</label>
                                                        <select class="form-select custom-input"
                                                            aria-label="Select Session Type" id="sessionTypeSelect" name="sessionTypeSelect"
                                                            required>
                                                            <option value="">Select Session Type</option>
                                                            <option value="Lab">Lab</option>
                                                            <option value="Teaching">Teaching</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-2 mb-2">
                                                        <label for="sessionTAInput" class="form-label">TA
                                                            Allocation</label>
                                                        <input type="number" class="form-control custom-input"
                                                            id="sessionTAInput" name="sessionTAInput" min="1" required>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <label for="sessionDaySelect" class="form-label">Day of
                                                            Session</label>
                                                        <select class="form-select custom-input"
                                                            aria-label="Select Session Day" id="sessionDaySelect" name="sessionDaySelect"
                                                            required>
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
                                                        <input type="time" class="form-control custom-input"
                                                            id="sessionStartTimeInput" name="sessionStartTimeInput" min="08:00" max="20:30"
                                                            step="1800" required>
                                                    </div>
                                                    <div class="col-3 mb-2">
                                                        <label for="sessionEndTimeInput" class="form-label">End
                                                            Time</label>
                                                        <input type="time" class="form-control custom-input"
                                                            id="sessionEndTimeInput" name="sessionEndTimeInput" min="08:30" max="21:00" step="1800"
                                                            required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Add Session</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-sessionallocation" role="tabpanel"
                                    aria-labelledby="pills-sessionallocation-tab" tabindex="0">
                                    <div class="container">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                            aria-labelledby="v-pills-settings-tab" tabindex="0">
                            <h1 class="display-6 ps-3">Settings</h1>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- User Datalist -->
        <datalist id="userList">
            <?php foreach ($tarows as $row) : ?>
                <option value="<?= $row['ta_num'] ?>"><?= $row['fname'] ?> <?= $row['lname'] ?></option>
            <?php endforeach; ?>
        </datalist>
        <!-- Module Datalist -->
        <datalist id="moduleList">
        <?php foreach ($modulerows as $row) : ?>
                <option value="<?= $row['module_num'] ?>"><?= $row['module_name'] ?></option>
            <?php endforeach; ?>
        </datalist>
    </body>
</html>