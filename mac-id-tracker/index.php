<?php

session_start();

require_once("conn.php");

$username = $password = "";
$usernameErr = $passwordErr = "";
$bothErr = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = test($_POST["username"]);
    $password = test($_POST["password"]);

    if (empty($username)) {
        $usernameErr = "* Username is required!";
    }

    if (empty($password)) {
        $passwordErr = "* Password is required!";
    }

    if (empty($username) && empty($password)) {
        $bothErr = "* Both are required!";
        $passwordErr = "";
        $usernameErr = "";
    } else {
        try {
            $sql = "SELECT * FROM accounts WHERE username=? AND password=?";
            $query = $conn->prepare($sql);
            $query->execute(array($username, $password));
            $row = $query->rowCount();
            $fetch = $query->fetch();

            if ($row > 0) {
                $_SESSION['id'] = $fetch['id'];
                if (!empty($fetch['profile_image'])) {
                    $file = "uploads/{$fetch['profile_image']}";
                    if (file_exists($file)) {
                        $_SESSION['profile_image'] = $fetch['profile_image'];
                    } else {
                        if (strtolower($fetch['gender']) === "male" || $fetch['gender'] === "boy") {
                            $_SESSION['profile_image'] = 'male.svg';
                        } else if (strtolower($fetch['gender']) === "female" || $fetch['gender'] === "girl") {
                            $_SESSION['profile_image'] = 'female.svg';
                        }
                    }
                } else {
                    if (strtolower($fetch['gender']) === "male" || $fetch['gender'] === "boy") {
                        $_SESSION['profile_image'] = 'male.svg';
                    } else if (strtolower($fetch['gender']) === "female" || $fetch['gender'] === "girl") {
                        $_SESSION['profile_image'] = 'female.svg';
                    }
                }

                $_SESSION["firstname"] = $fetch["firstname"];
                $_SESSION["lastname"] = $fetch["lastname"];
                $_SESSION["username"] = $fetch["username"];
                $_SESSION["password"] = $fetch["password"];
                // if remember me clicked . Values will be stored in $_COOKIE  array
			    if(!empty($_POST["remember"])) {
                    //COOKIES for username
                    setcookie("username", $_POST["username"], time()+ (10 * 365 * 24 * 60 * 60));
                    //COOKIES for password
                    setcookie("password", $_POST["password"], time()+ (10 * 365 * 24 * 60 * 60));
                } else {
                    setcookie("username","");
                    setcookie("password","");
                }

                $_SESSION["role"] = $fetch["role"];
                $_SESSION["age"] = $fetch["age"];
                $_SESSION["birth_date"] = $fetch["birth_date"];
                $_SESSION["gender"] = $fetch["gender"];

                try {
                    $fullname = "{$_SESSION['firstname']} {$_SESSION['lastname']}";
                    $action = "Login";
                    $event = "Account Full Name: {$_SESSION["firstname"]} {$_SESSION["lastname"]}";
                    $sql = "INSERT INTO history_log (fullname, action, event, timestamp) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$fullname, $action, $event, date('Y-m-d H:i:s')]);
                } catch (PDOException $e) {
                    echo "query failed: " . $e->getMessage();
                }

                header("location: dashboard.php");
            } else {
                echo "<script>alert('Incorrect credentials.');</script>";
            }
        } catch (PDOException $e) {
            echo "SQL query error: " . $e->getMessage();
        }
    }
}

function test($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../img/makati_logo.png">

    <title>MAC ID Tracker - Login</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-lg-6 col-md-9">

                <div class="card border-1 shadow-lg mx-auto my-5 text-center">
                    <div class="card-body p-2">
                        <div class="p-5">
                            <div class="text-center">
                                <img class="img-fluid" src="img/makati_logo.png" width="180px">
                                <h1 class="h2 text-gray-900 mb-2">MAC ID Tracking System</h1>
                                <h1 class="h4 text-gray-600 mb-5">Login your account</h1>
                            </div>

                            <form class="user" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="form-group mb-4 font-weight-bold">
                                    <input name="username" type="username" class="form-control form-control-user" placeholder="Username" value="<?php if(isset($_COOKIE["username"])) { echo $_COOKIE["username"]; } ?>">
                                    <span class="text-danger"><?php echo $usernameErr; ?></span>
                                </div>
                                <div class="form-group mb-4">
                                    <input name="password" type="password" class="form-control form-control-user" placeholder="Password" value="<?php if(isset($_COOKIE["password"])) { echo $_COOKIE["password"]; } ?>">
                                    <span class="text-danger"><?php echo $passwordErr; ?></span>
                                </div>
                                <div class="form-group mb-5">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck" name="remember" <?php echo (isset($_COOKIE["username"])) ? "checked" : ""; ?> />
                                        <label class="custom-control-label" for="customCheck">Remember
                                            Me</label>
                                    </div>
                                </div>
                                <input type="submit" name="submit" value="Login" class="btn btn-primary btn-user btn-block">
                            </form>
                            <span class="text-danger"><?php echo $bothErr; ?></span>
                            <br />
                            <a href="../index.html" class="link-secondary">Go back to homepage</a>
                        </div>
                    </div>
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

</body>

</html>

<?php

$conn = null;

?>