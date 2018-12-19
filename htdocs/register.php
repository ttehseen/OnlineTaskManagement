<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MY TASK : Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper" style="width:800px; margin:0 auto;">
        <img src="logo.png" alt="Logo" style = " margin-left: auto; margin-right: auto; display: block;">
        <h2>Sign up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="submit" value="Submit">
                <input type="reset" class="btn btn-default" name="reset" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
    <!-- // memos
    // username should be unique? because find row based on username in login.php
    // should check password == confirm password -->
    <?php
    $db     = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=test");
    if (isset($_POST['submit'])) {
      if ($_POST['password'] == $_POST['confirm_password']) {
        $result = pg_query($db, "SELECT user_name FROM users WHERE user_name = '$_POST[username]'");
        $row = pg_fetch_all($result);

        if (!$row) {
          var_dump($result);
          $result = pg_query($db, "SELECT user_email FROM users WHERE user_email = '$_POST[email]'");
          $row = pg_fetch_all($result);
          if (!$row) {
            var_dump($result);
            $result = pg_query($db, "INSERT INTO users (user_name ,user_password,user_email) VALUES ('$_POST[username]','$_POST[password]','$_POST[email]')");
            if (!$result) {
              echo "Failed to create the account!";
            } else {
              echo "Successfully created the account!";
            }
          } else {
            echo "Email entered is a duplicate of existing email!";
          }
        } else {
          echo "Username entered is a duplicate of existing username!";
        }
      } else {
        echo "Password not the same as Confirm Password!";
      }
    }
    ?>
</body>
</html>
