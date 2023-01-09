<?php

session_start();

require_once("conn.php");

$searchErr = "";
$cardErr = "";
$criteriaErr = "";
$bothErr = "";
$search = "";
$card = "";
$criteria = "";
$count = 0;
$fetch;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $criteria = test($_POST['criteria']);
    $search = test($_POST['search']);

    if (empty($criteria)) {
        $criteriaErr = "<span class='text-danger'>* You did not select any criteria.</span><br />";
    }

    if (empty($search)) {
        $searchErr = "<span class='text-danger'>* Input is required.</span><br />";
    }

    if (empty($criteriaErr) && empty($searchErr)) {
        try {
            $data = $conn->prepare("SELECT * FROM accounts WHERE {$criteria} LIKE '%{$search}%'");
            $data->execute();
            $count = $data->rowCount();
            $fetch = $data->fetchAll();
        } catch (PDOException $e) {
            $bothErr = "Please input required data.";
            echo "query failed: {$e->getMessage()}";
        }
    } else {
        $bothErr = "Please input required data.";
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

    <title>MAC ID Tracker - Search Card Information</title>

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
                        <a class="collapse-item" href="add.php">Add</a>
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
                        <a class="collapse-item" href="add-employee.php">Add Employee</a>
                        <a class="collapse-item active" href="#">Remove Employee</a>
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
                        <h1 class="h3 text-gray-800">Manage employees / Remove Employee</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Search Employee Account</h6>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <?php echo $searchErr; ?>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-light" name="search" id="search" placeholder="Search">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary border-2" type="submit">
                                                    <i class="fas fa-search fa-sm"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="input-group">
                                            <?php echo $criteriaErr; ?>
                                            <select name="criteria" class="btn btn-primary btn-block shadow m-1">
                                                <option class="bg-white text-dark" value="">--- Search criteria ---</option>
                                                <option class="bg-white text-dark" value="role">Account Role</option>
                                                <option class="bg-white text-dark" value="username">Username</option>
                                                <option class="bg-white text-dark" value="lastname">Last Name</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Output Data</h6>
                                </div>
                                <div id="table_result" class="card-body">
                                    <?php
                                    if ($count > 0) {
                                        echo "<table class=\"table table-responsive table-bordered\" id=\"dataTable\" width=\"100%\">
                                        <thead>
                                            <th class='text-dark'>First Name</th>
                                            <th class='text-dark'>Last Name</th>
                                            <th class='text-dark'>Username</th>
                                            <th class='text-dark'>Role</th>
                                            <th class='text-dark'>Age</th>
                                            <th class='text-dark'>Birth Date</th>
                                            <th class='text-dark'>Gender</th>
                                            <th class='text-dark'>Action</th>
                                        </thead>
                                        <tbody>
                                            ";


                                        foreach ($fetch as $row) {
                                            echo "<tr>";
                                            echo "<td class='text-dark'>{$row['firstname']}</td>";
                                            echo "<td class='text-dark'>{$row['lastname']}</td>";
                                            echo "<td class='text-dark'>{$row['username']}</td>";
                                            echo "<td class='text-dark'>{$row['role']}</td>";
                                            echo "<td class='text-dark'>{$row['age']}</td>";
                                            echo "<td class='text-dark'>{$row['birth_date']}</td>";
                                            echo "<td class='text-dark'>{$row['gender']}</td>";
                                            echo "<td class='text-dark'><a class='btn btn-danger' href='remove-employee-script.php?id={$row['id']}'>Remove</button></td>";
                                            echo "</tr>";
                                        }

                                        echo "</tbody>
                                            </table>";

                                            echo "<button onclick=\"clearTbl('table_result');\" class='btn btn-danger float-right'>Clear</button>";
                                    } else {
                                        echo (empty($search)) ? "" : "<p class='text-center text-dark'><span class='text-danger'>${search}</span> not found. :(</p>";;
                                    }
                                    ?>
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

<script type="text/javascript">
    function clearTbl(elementID) {
        document.getElementById(elementID).innerHTML = "";
    }
</script>

</html>

<?php
$conn = null;
?>