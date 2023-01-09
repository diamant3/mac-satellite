<?php
ob_start();
session_start();

require_once("conn.php");

$file_err = "";
$filename = $upload_dir = "";
$is_image_exist = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_SESSION['id'];
    $profile_image = $_POST['profile_image'];
    $firstname = test($_POST['firstname']);
    $lastname = test($_POST['lastname']);
    $age = test($_POST['age']);
    $birth_date = test($_POST['birth_date']);
    $gender = test($_POST['gender']);

    // file image
    $img_file = $_FILES['profile_image']['name'];
    $tmp_dir = $_FILES['profile_image']['tmp_name'];
    $img_size = $_FILES['profile_image']['size'];

    if ($img_file) {
        $upload_dir = 'uploads/';
        $img_ext = strtolower(pathinfo($img_file, PATHINFO_EXTENSION));
        $valid_ext = array('jpeg', 'jpg', 'png', 'gif');
        $filename = "{$_SESSION['id']}.{$img_ext}";

        if (in_array($img_ext, $valid_ext)) {
            // image size 3MB
            if ($img_size < 3000000) {
                if ($_SESSION['profile_image'] === $filename) {
                    unlink($upload_dir . $_SESSION['profile_image']);
                }

                move_uploaded_file($tmp_dir, $upload_dir . $filename);
                $is_image_exist = 1;
            } else {
                $file_err = "file is too large, please upload maximum of 5MB";
            }
        } else {
            $file_err = "Sorry, only JPG, JPEG, PNG and GIF files are allowed.";
        }
    } else {
        $is_image_exist = 0;
    }

    if ($is_image_exist) {
        try {
            $sql = "UPDATE accounts SET profile_image=?, firstname=?, lastname=?, age=?, birth_date=?, gender=? WHERE id=?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bindParam(1, $filename, PDO::PARAM_STR);
                $stmt->bindParam(2, $firstname, PDO::PARAM_STR);
                $stmt->bindParam(3, $lastname, PDO::PARAM_STR);
                $stmt->bindParam(4, $age, PDO::PARAM_STR);
                $stmt->bindParam(5, $birth_date, PDO::PARAM_STR);
                $stmt->bindParam(6, $gender, PDO::PARAM_STR);
                $stmt->bindParam(7, $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $_SESSION['profile_image'] = $filename;
                    $_SESSION['firstname'] = $firstname;
                    $_SESSION['lastname'] = $lastname;
                    $_SESSION['age'] = $age;
                    $_SESSION['birth_date'] = $birth_date;
                    $_SESSION['gender'] = $gender;

                    try {
                        $fullname = "{$_SESSION['firstname']} {$_SESSION['lastname']}";
                        $action = "Update Profile";
                        $event = "Full Name: {$_SESSION['firstname']} {$_SESSION['lastname']}";
                        $sql = "INSERT INTO history_log (fullname, action, event, timestamp) VALUES (?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$fullname, $action, $event, date('Y-m-d H:i:s')]);
                    } catch (PDOException $e) {
                        echo "query failed: " . $e->getMessage();
                    }
                }
            }
        } catch (PDOException $e) {
            echo "query failed: " . $e->getMessage();
        }
    } else {
        try {
            $sql = "UPDATE accounts SET firstname=?, lastname=?, age=?, birth_date=?, gender=? WHERE id=?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bindParam(1, $firstname, PDO::PARAM_STR);
                $stmt->bindParam(2, $lastname, PDO::PARAM_STR);
                $stmt->bindParam(3, $age, PDO::PARAM_STR);
                $stmt->bindParam(4, $birth_date, PDO::PARAM_STR);
                $stmt->bindParam(5, $gender, PDO::PARAM_STR);
                $stmt->bindParam(6, $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $_SESSION['firstname'] = $firstname;
                    $_SESSION['lastname'] = $lastname;
                    $_SESSION['age'] = $age;
                    $_SESSION['birth_date'] = $birth_date;
                    $_SESSION['gender'] = $gender;
                }
            }
        } catch (PDOException $e) {
            echo "query failed: " . $e->getMessage();
        }
    }
    
    header('location: profile.php');
}

function test($data_str)
{
    $data_str = trim($data_str);
    $data_str = stripslashes($data_str);
    $data_str = htmlspecialchars($data_str);

    return $data_str;
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="icon" href="img/makati_logo.png">

    <title>MAC ID Tracker - Update Profile</title>

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
                <a class="nav-link" href="#">
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
                        <?php if ($_SESSION['role'] === "administrator") { ?>
                        <a class="collapse-item" href="add.php">Add</a>
                        <a class="collapse-item" href="archive.php">Archive</a>
                        <a class="collapse-item" href="update.php">Update</a>
                        <?php } ?>
                    </div>
                </div>
            </li>

            <?php if ($_SESSION['role'] === "administrator") { ?>
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
            <a class="nav-link collapsed" href="logs.php" data-toggle="collapse" data-target="#card" aria-expanded="true" aria-controls="collapsePages">
                <i class="fas fa-fw fa-history"></i>
                    <span>Transaction Logs</span>
                </a>
            </li>
            <?php } ?>

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
                                <img class="img-profile rounded-circle" src="uploads/<?php echo $_SESSION['profile_image']; ?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item active" href="profile.php">
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
                        <h1 class="h3 mb-0 text-gray-800">User Profile</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Profile Dashboard</h6>
                                </div>
                                <div class="card-body text-center">
                                    <img src="uploads/<?php echo $_SESSION['profile_image']; ?>" alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
                                    <h5 class="text-capitalize my-1 text-dark font-weight-bold"><?php echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></h5>
                                    <p class="mb-1 text-capitalize"><?php echo $_SESSION['role']; ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">User Details</h6>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label class="text-dark font-weight-bold">Upload a profile picture:</label>
                                            <input type="file" name="profile_image" value="" />
                                        </div>
                                        <?php echo $file_err; ?>
                                        <div class="row">
                                            <div class="form-group col-lg-6">
                                                <label class="text-dark font-weight-bold">First Name</label>
                                                <input type="text" name="firstname" class="form-control text-capitalize" value="<?php echo $_SESSION['firstname']; ?>">
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label class="text-dark font-weight-bold">Last Name</label>
                                                <input type="text" name="lastname" class="form-control text-capitalize" value="<?php echo $_SESSION['lastname']; ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-4">
                                                <label class="text-dark font-weight-bold">Age</label>
                                                <input name="age" type="text" class="form-control text-capitalize" value="<?php echo $_SESSION['age']; ?>">
                                            </div>
                                            <div class="form-group col-lg-4">
                                                <label class="text-dark font-weight-bold">Birth Date</label>
                                                <input name="birth_date" type="text" class="form-control text-capitalize" value="<?php echo $_SESSION['birth_date']; ?>">
                                            </div>
                                            <div class="form-group col-lg-4">
                                                <label class="text-dark font-weight-bold">Gender</label>
                                                <input name="gender" type="text" class="form-control text-capitalize" value="<?php echo $_SESSION['gender']; ?>">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-lg-6">
                                                <input value="Save" type="submit" class="form-control btn btn-success btn-block">
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <a href="profile.php" class="form-control btn btn-secondary btn-block">Cancel</a>
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