<?php

session_start();

require_once("conn.php");

$id_number = $id_numberErr = "";
$firstname = $firstnameErr = "";
$lastname = $lastnameErr = "";
$address = $addressErr = "";
$card = $cardErr = "";
$issue_date = $issue_dateErr = "";
$exp_date = $exp_dateErr = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id_number = test($_POST['id_number']);
    if (empty($id_number)) {
        $id_numberErr = "<span class='text-danger'>* Please input your ID Number.</span><br />";
    }

    $firstname = test($_POST['firstname']);
    if (empty($firstname)) {
        $firstnameErr = "<span class='text-danger'>* Please input your First Name.</span><br />";
    }

    $lastname = test($_POST['lastname']);
    if (empty($lastname)) {
        $lastnameErr = "<span class='text-danger'>* Please input your Last Name.</span><br />";
    }

    $address = test($_POST['address']);
    if (empty($address)) {
        $addressErr = "<span class='text-danger'>* Please input your Address.</span><br />";
    }

    $card = test($_POST['card']);
    if (empty($card)) {
        $cardErr = "<span class='text-danger'>* Please choose a Card.</span><br />";
    }

    $issue_date = test($_POST['issue_date']);
    if (empty($issue_date)) {
        $issue_dateErr = "<span class='text-danger'>* Please input your Issue Date.</span><br />";
    }

    $exp_date = test($_POST['exp_date']);
    if (empty($exp_date) && $card === "blue_id") {
        $exp_dateErr = "<span class='text-danger'>* Please Input Expiry Date.</span><br />";
    } else if (empty($exp_date) && $card === "yellow_id") {
        $exp_dateErr = "<span class='text-danger'>* Please Input Expiry Date.</span><br />";
    } else {
        $exp_dateErr = "";
    }

    if (empty($id_numberErr) && empty($firstnameErr) && empty($lastnameErr) && empty($addressErr) && empty($cardErr) && empty($issue_dateErr) && empty($exp_dateErr)) {
        if (!empty($exp_date) && $card === "white_id") {
            $exp_dateErr = "<span class='text-danger'>* Expiration Date is not applicable to white card.</span><br />";
        } else {
            $cardtype = "";
            if ($card === "blue_id") {
                $cardtype = "blue";
            }
            if ($card === "yellow_id") {
                $cardtype = "yellow";
            }
            if ($card === "white_id") {
                $cardtype = "white";
            }

            if (empty($exp_date) && $card === "white_id") {
                try {
                    $query = "INSERT INTO {$card} (id_number, firstname, lastname, address, cardtype, issue_date) VALUES (?, ?, ?, ?, ?, ?)";
                    $data = $conn->prepare($query);
                    $data->execute([$id_number, $firstname, $lastname, $address, $cardtype, $issue_date]);
                } catch (PDOException $e) {
                    echo "query failed: " . $e->getMessage();
                }
                echo "<script>alert(\"white card registered successfully!\");</script>";

                try {
                    $action = "Add";
                    $event = "Card ID Number: {$id_number} Type: {$cardtype}";
                    $sql = "INSERT INTO history_log (action, event, timestamp) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$action, $event, date('Y-m-d H:i:s')]);
                } catch (PDOException $e) {
                    echo "query failed: " . $e->getMessage();
                }
            } else {
                try {
                    $query = "INSERT INTO {$card} (id_number, firstname, lastname, address, cardtype, issue_date, exp_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $data = $conn->prepare($query);
                    $data->execute([$id_number, $firstname, $lastname, $address, $cardtype, $issue_date, $exp_date]);
                } catch (PDOException $e) {
                    echo "query failed: " . $e->getMessage();
                }

                echo "<script>alert(\"{$cardtype} card registered successfully!\");</script>";

                try {
                    $fullname = "{$_SESSION['firstname']} {$_SESSION['lastname']}";
                    $action = "Add";
                    $event = "Card ID Number: {$id_number} Type: {$cardtype}";
                    $sql = "INSERT INTO history_log (fullname, action, event, timestamp) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$fullname, $action, $event, date('Y-m-d H:i:s')]);
                } catch (PDOException $e) {
                    echo "query failed: " . $e->getMessage();
                }
            }
        }
    }
}

function test($data_str)
{
    $data_str = trim($data_str);
    $data_str = stripslashes($data_str);
    $data_str = htmlspecialchars($data_str);

    return $data_str;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../img/makati_logo.png">

    <title>MAC ID Tracker - Add Card Information</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                <img class="img-fluid" width="55px" src="img/makati_logo.png">
                </div>
                <div class="sidebar-brand-text mx-3">MAC ID Tracker</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item active">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#card" aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-id-card"></i>
                    <span>Cards</span>
                </a>
                <div id="card" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Card tools:</h6>
                        <a class="collapse-item" href="search.php">Search</a>
                        <a class="collapse-item active" href="#">Add</a>
                        <a class="collapse-item" href="archive.php">Archive</a>
                        <a class="collapse-item" href="update.php">Update</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#profile" aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Manage employees</span>
                </a>
                <div id="profile" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Employee Tools:</h6>
                        <a class="collapse-item" href="add-employee.php">Add Employee</a>
                        <a class="collapse-item" href="remove-employee.php">Remove Employee</a>
                        <a class="collapse-item" href="update-employee.php">Update Employee</a>
                        <div class="collapse-divider"></div>
                    </div>
                </div>
            </li>

            <li class="nav-item">
            <a class="nav-link collapsed" href="logs.php">
                <i class="fas fa-fw fa-history"></i>
                    <span>Transaction Logs</span>
                </a>
            </li>

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-700 small text-capitalize"><?php echo $_SESSION["firstname"] . " " . $_SESSION["lastname"]; ?></span>
                                <img class="img-profile rounded-circle" src="uploads/<?php echo $_SESSION['profile_image'] ?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Cards / Add Card Information</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-lg-12 mb-3">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary text-center text-uppercase">Card Information Registration Form</h6>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <?php echo $id_numberErr; ?>
                                        <label for="id_number" class="col-md-4 control-label text-dark mb-1">ID Number</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="id_number" class="form-control bg-light" name="id_number" placeholder="0123456789">
                                        </div>

                                        <?php echo $firstnameErr; ?>
                                        <label for="firstname" class="col-md-4 control-label text-dark mb-1">First Name</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="firstname" class="form-control bg-light" name="firstname" placeholder="Juan">
                                        </div>

                                        <?php echo $lastnameErr; ?>
                                        <label for="lastname" class="col-md-4 control-label text-dark mb-1">Last Name</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="lastname" class="form-control bg-light" name="lastname" placeholder="Dela Cruz">
                                        </div>

                                        <?php echo $addressErr; ?>
                                        <label for="address" class="col-md-4 control-label text-dark mb-1">Address</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <textarea type="text" id="address" class="form-control bg-light" name="address" placeholder="Blk 420 Lot 69 Mabango St, Brgy. Makati City"></textarea>
                                        </div>

                                        <?php echo $issue_dateErr; ?>
                                        <label for="issue_date" class="col-md-4 control-label text-dark mb-1">Date Issued</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="issue_date" class="form-control bg-light" name="issue_date" placeholder="June 22 2022">
                                        </div>

                                        <?php echo $exp_dateErr; ?>
                                        <label for="exp_date" class="col-md-4 control-label text-dark mb-1">Expiry Date</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="exp_date" class="form-control bg-light" name="exp_date" placeholder="June 22 2025">
                                        </div>

                                        <?php echo $cardErr; ?>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <select name="card" class="btn btn-primary btn-block shadow mb-1">
                                                    <option class="bg-white text-dark" value="">--- Choose a card ---</option>
                                                    <option class="bg-white text-dark" value="blue_id">Blue Card</option>
                                                    <option class="bg-white text-dark" value="yellow_id">Yellow Card</option>
                                                    <option class="bg-white text-dark" value="white_id">White Card</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <button class="btn btn-primary btn-block shadow mb-1" type="submit">
                                                    <i class="fa fa-paper-plane fa-sm"></i>
                                                    Submit
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Makati Action Center 2022</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Do you want to logout?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>