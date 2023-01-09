<?php

session_start();

require_once("conn.php");

$firstname = "";
$lastname = "";
$username = $usernameErr = "";
$role = $roleErr = "";
$age = "";
$birth_date = "";
$gender = "";

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $id = $_POST['id'];
    $firstname = test($_POST['firstname']);
    $lastname = test($_POST['lastname']);
    $username = test($_POST['username']);
    $role = test($_POST['role']);
    $age = test($_POST['age']);
    $birth_date = test($_POST['birth_date']);
    $gender = test($_POST['gender']);

    if (empty($username)) {
        $usernameErr = "<span class='text-danger'>* Please input your username.</span><br />";
    }

    if (empty($role)) {
        $roleErr = "<span class='text-danger'>* Please choose a role.</span><br />";
    }

    if (empty($usernameErr) && empty($roleErr)) {
        try {
            $sql = "UPDATE accounts SET id=?, firstname=?, lastname=?, username=?, password=?, role=?, age=?, birth_date=?, gender=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id, $firstname, $lastname, $username, $password, $role, $age, $birth_date, $gender, $id]);

            try {
                $fullname = "{$_SESSION['firstname']} {$_SESSION['lastname']}";
                $action = "Update";
                $event = "Employee Full Name: {$firstname} {$lastname}";
                $sql = "INSERT INTO history_log (fullname, action, event, timestamp) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$fullname, $action, $event, date('Y-m-d H:i:s')]);
            } catch (PDOException $e) {
                echo "query failed: " . $e->getMessage();
            }

            header("location: update-employee.php");
            exit();
        } catch (PDOException $e) {
            echo "query failed: " . $e->getMessage();
        }
    }
} else {
    if (!empty($_GET['id'])) {
        $id = $_GET['id'];

        try {
            $sql = "SELECT * FROM accounts WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);

            $rowCount = $stmt->rowCount();
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($rowCount > 0) {
                $firstname = $fetch['firstname'];
                $lastname = $fetch['lastname'];
                $username = $fetch['username'];
                $role = $fetch['role'];
                $age = $fetch['age'];
                $birth_date = $fetch['birth_date'];
                $gender = $fetch['gender'];
            }
            

        } catch (PDOException $e) {
            echo "query failed: " . $e->getMessage();
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

    <title>MAC ID Tracker - Add Employee Account</title>

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
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#card" aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-id-card"></i>
                    <span>Cards</span>
                </a>
                <div id="card" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Card tools:</h6>
                        <a class="collapse-item" href="search.php">Search</a>
                        <a class="collapse-item " href="add.php">Add</a>
                        <a class="collapse-item" href="archive.php">Archive</a>
                        <a class="collapse-item" href="update.php">Update</a>
                    </div>
                </div>
            </li>

            <li class="nav-item active">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#profile" aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Manage employees</span>
                </a>
                <div id="profile" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Employee Tools:</h6>
                        <a class="collapse-item active" href="add-employee.php">Add Employee</a>
                        <a class="collapse-item" href="remove-employee.php">Remove Employee</a>
                        <a class="collapse-item" href="#">Update Employee</a>
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
                        <h1 class="h3 mb-0 text-gray-800">Manage employees / Update Employee</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-lg-12 mb-3">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary text-center text-uppercase">Account Registration Form</h6>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <label for="firstname" class="col-md-4 control-label text-dark mb-1">First Name</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="firstname" class="form-control bg-light" name="firstname" value="<?php echo $firstname; ?>">
                                        </div>

                                        <label for="lastname" class="col-md-4 control-label text-dark mb-1">Last Name</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="lastname" class="form-control bg-light" name="lastname" value="<?php echo $lastname; ?>">
                                        </div>

                                        <label for="age" class="col-md-4 control-label text-dark mb-1">Age</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="age" class="form-control bg-light" name="age" value="<?php echo $age; ?>">
                                        </div>

                                        <label for="birth_date" class="col-md-4 control-label text-dark mb-1">Birth Date</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="birth_date" class="form-control bg-light" name="birth_date" value="<?php echo $birth_date; ?>">
                                        </div>

                                        <label for="gender" class="col-md-4 control-label text-dark mb-1">Gender</label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="gender" class="form-control bg-light" name="gender" value="<?php echo $gender; ?>">
                                        </div>

                                        <?php echo $usernameErr; ?>
                                        <label for="username" class="col-md-4 control-label text-dark mb-1">Username <small class="text-sm text-danger">*</small></label>
                                        <div class="form-group mb-3 col-lg-12">
                                            <input type="text" id="username" class="form-control bg-light" name="username" value="<?php echo $username; ?>">
                                        </div>

                                        <?php echo $roleErr; ?>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <select name="role" class="btn btn-primary btn-block shadow mb-1">
                                                    <?php 
                                                        if ($role === "administrator") {
                                                            echo "<option class=\"bg-white text-dark\" value=\"administrator\">Administrator</option>";
                                                            echo "<option class=\"bg-white text-dark\" value=\"employee\">Employee</option>";
                                                        } else {
                                                            echo "<option class=\"bg-white text-dark\" value=\"employee\">Employee</option>";
                                                            echo "<option class=\"bg-white text-dark\" value=\"administrator\">Administrator</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                                <button class="btn btn-success btn-block shadow mb-1" type="submit">
                                                    Update
                                                </button>
                                            </div>
                                            <div class="col-lg-4">
                                            <a href="update-employee.php" class="btn btn-secondary btn-block">Cancel</a>
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