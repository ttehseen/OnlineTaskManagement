<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MY TASK : Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper" style="width:800px; margin:0 auto;">
        <img src="logo.png" alt="Logo" style = " margin-left: auto; margin-right: auto; display: block;">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form name="login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="submit" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
    <?php

    // Connect to the database. Please change the password in the following line accordingly
    $db = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=test");

    if (isset($_POST['submit'])) {

        $result = pg_query($db, "SELECT user_id, user_name, is_admin FROM users WHERE user_name = '$_POST[username]'");
        $row = pg_fetch_all($result);
        $is_admin = $row[0][is_admin];


        session_start();
        echo "Session has started";
        $_SESSION['user'] = $row[0][user_id];
        
        if($row){
            var_dump($result);
            $result = pg_query($db, "SELECT user_id, user_name FROM users WHERE user_password  = '$_POST[password]' AND user_name = '$_POST[username]'");
            $row = pg_fetch_all($result);
            // echo $row[0][user_id]; //this works jsyk
            echo "<br />";

            if (!$row) {
                    echo "Login failed! Wrong Password.";
                } else {
                    echo "Login successful! Welcome!";

                    if ($is_admin=="t") {
                        header("Location: index.php");
                    } else {
                        header("Location: menu.php");
                    }
                    exit;
                }
        } else {
            echo "Wrong user provided";
        }
    } 

    ?>
</body>
</html>
