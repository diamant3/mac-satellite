<?php

session_start();

// Include config file
require_once("conn.php");

// Define variables and initialize with empty values
$id_number = $firstname = $lastname = $address = $card = "";
$id_number_err = $firstname_err = $lastname_err = $address_err = "";

$issue_date = $issue_dateErr = "";
$exp_date = $exp_dateErr = "";

// Processing form data when form is submitted
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    // Get hidden input value
    $id = $_POST["id"];

    $id_number = test($_POST["id_number"]);
    if (empty($id_number)) {
        $id_num_err = "Please enter your ID number.";
    }

    $firstname = test($_POST["firstname"]);
    if (empty($firstname)) {
        $firstname_err = "Please enter your First Name.";
    }

    $lastname = test($_POST["lastname"]);
    if (empty($lastname)) {
        $lastname_err = "Please enter your Last Name.";
    }

    $address = test($_POST["address"]);
    if (empty($address)) {
        $address_err = "Please enter your Address.";
    }

    $issue_date = test($_POST["issue_date"]);
    if (empty($issue_date)) {
        $issue_dateErr = "Please enter your Issue Date.";
    }

    $exp_date = test($_POST["exp_date"]);
    if (empty($exp_date) && $card === "white_id") {
        $exp_dateErr = "Please enter your Expiry Date.";
    } else {
        $exp_dateErr = "";
    }

    // Check input errors before inserting in database
    if (empty($id_number_err) && empty($firstname_err) && empty($lastname_err) && empty($address_err) && empty($issue_dateErr) && empty($exp_dateErr)) {
        $card = $_SESSION['card_db'];
        if ($card === "white_id") {
            // Prepare an update statement
            $sql = "UPDATE {$card} SET id_number=?, firstname=?, lastname=?, `address`=?, issue_date=? WHERE id=?";

            if ($stmt = $conn->prepare($sql)) {
                if (empty($exp_date)) {
                    $stmt->bindParam(1, $id_number, PDO::PARAM_STR);
                    $stmt->bindParam(2, $firstname, PDO::PARAM_STR);
                    $stmt->bindParam(3, $lastname, PDO::PARAM_STR);
                    $stmt->bindParam(4, $address, PDO::PARAM_STR);
                    $stmt->bindParam(5, $issue_date, PDO::PARAM_STR);
                    $stmt->bindParam(6, $id, PDO::PARAM_INT);


                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        // Records updated successfully. Redirect to landing page
                        header("location: update.php");
                        exit();
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                } else {
                    $exp_dateErr = "Expiration Date is not applicable to white card.";
                }
            }
        } else {
            // Prepare an update statement
            $sql = "UPDATE {$card} SET id_number=?, firstname=?, lastname=?, `address`=?, issue_date=?, exp_date=? WHERE id=?";

            if ($stmt = $conn->prepare($sql)) {
                // Bind variables to the prepared statement as parameters

                $stmt->bindParam(1, $id_number, PDO::PARAM_STR);
                $stmt->bindParam(2, $firstname, PDO::PARAM_STR);
                $stmt->bindParam(3, $lastname, PDO::PARAM_STR);
                $stmt->bindParam(4, $address, PDO::PARAM_STR);
                $stmt->bindParam(5, $issue_date, PDO::PARAM_STR);
                $stmt->bindParam(6, $exp_date, PDO::PARAM_STR);
                $stmt->bindParam(7, $id, PDO::PARAM_INT);

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Records updated successfully. Redirect to landing page
                    header("location: update.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        }

        // Close statement
        unset($stmt);
    }

    // Close connection
    unset($conn);
} else {
    // Check existence of id parameter before processing further
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Get URL parameter
        $id =  $_GET["id"];
        $card = $_SESSION['card_db'];
        if ($card === "white_id") {

            // Prepare a select statement
            $sql = "SELECT id_number, firstname, lastname, `address`, `issue_date` FROM {$card} WHERE id = :id";
            if ($stmt = $conn->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":id", $param_id);

                // Set parameters
                $param_id = $id;

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Retrieve individual field value
                        $id_number = $row["id_number"];
                        $firstname = $row["firstname"];
                        $lastname = $row["lastname"];
                        $address = $row["address"];
                        $issue_date = $row["issue_date"];
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        } else {
            // Prepare a select statement
            $sql = "SELECT id_number, firstname, lastname, `address`, `issue_date`, `exp_date` FROM {$card} WHERE id = :id";
            if ($stmt = $conn->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":id", $param_id);

                // Set parameters
                $param_id = $id;

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Retrieve individual field value
                        $id_number = $row["id_number"];
                        $firstname = $row["firstname"];
                        $lastname = $row["lastname"];
                        $address = $row["address"];
                        $issue_date = $row["issue_date"];
                        $exp_date = $row["exp_date"];
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        }

        if ($card == "blue_id") $card = "blue";
        if ($card == "yellow_id") $card = "yellow";
        if ($card == "white_id") $card = "white";
        try {
            $fullname = "{$_SESSION['firstname']} {$_SESSION['lastname']}";
            $action = "Update";
            $event = "Card ID Number: {$id_number} Type: {$card}";
            $sql = "INSERT INTO history_log (fullname, action, event, timestamp) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fullname, $action, $event, date('Y-m-d H:i:s')]);
        } catch (PDOException $e) {
            echo "query failed: " . $e->getMessage();
        }

        // Close statement
        unset($stmt);

        // Close connection
        unset($conn);
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
    <link rel="icon" href="img/makati_logo.png">

    <title>MAC ID Tracker - Update Card Information</title>

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
                        <a class="collapse-item" href="add.php">Add</a>
                        <a class="collapse-item" href="archive">Archive</a>
                        <a class="collapse-item active" href="#">Update</a>
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
                        <h6 class="collapse-header">User Tools:</h6>
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
                        <h1 class="h3 text-gray-800">Cards / Update Card Information</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card shadow mb-4 d-flex justify-content-center">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary text-center text-uppercase">Update Card Record</h6>
                                    <p class="m-0 font-weight-light text-danger font-italic text-center">* Please edit the input values and submit to update the card record. *</p>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                                        <div class="form-group">
                                            <label>ID Number</label>
                                            <input type="text" name="id_number" class="form-control <?php echo (!empty($id_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $id_number; ?>">
                                            <span class="invalid-feedback"><?php echo $id_number_err; ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input name="firstname" class="form-control <?php echo (!empty($firstname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $firstname; ?>">
                                            <span class="invalid-feedback"><?php echo $firstname_err; ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" name="lastname" class="form-control <?php echo (!empty($lastname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $lastname; ?>">
                                            <span class="invalid-feedback"><?php echo $lastname_err; ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea type="text" name="address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>"><?php echo $address; ?></textarea>
                                            <span class="invalid-feedback"><?php echo $address_err; ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Date Issued</label>
                                            <input type="text" name="issue_date" class="form-control <?php echo (!empty($issue_dateErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $issue_date; ?>">
                                            <span class="invalid-feedback"><?php echo $issue_dateErr; ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Expiry Date</label>
                                            <input type="text" name="exp_date" class="form-control <?php echo (!empty($exp_dateErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $exp_date; ?>">
                                            <span class="invalid-feedback"><?php echo $exp_dateErr; ?></span>
                                        </div>

                                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                                        <a href="update.php" class="btn btn-secondary float-right ml-2">Cancel</a>
                                        <input type="submit" class="btn btn-primary float-right" value="Submit">
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

<?php
$conn = null;
?>