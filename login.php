<?php
session_start();
require "../koneksi.php";

if (isset($_POST['loginbt'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    $query = $con->prepare("SELECT * FROM pengguna WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_array(MYSQLI_ASSOC);

        if (password_verify($password, $data['password'])) {
            $_SESSION['username'] = $data['username'];
            $_SESSION['login'] = true;
            echo "<script>window.location='../adminpanel';</script>";
        } else {
            $error_message = "<div class='alert alert-warning shadow-sm border border-dark rounded-3'>Password salah</div>";
        }
    } else {
        $error_message = "<div class='alert alert-warning shadow-sm border border-dark rounded-3'>Akun tidak ditemukan</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-box {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            border: 1px solid black;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .alert {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 100px; max-width: 400px;">
        <div class="login-box">
            <h4 class="mb-3 text-center">Login</h4>
            <form action="" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" name="loginbt" class="btn btn-success w-100">Login</button>
            </form>
        </div>

        <?php
        if (isset($error_message)) {
            echo "<div class='mt-3'>$error_message</div>";
        }
        ?>
    </div>
</body>
</html>
