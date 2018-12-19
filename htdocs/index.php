<!DOCTYPE html>
<head>
  <title>UPDATE PostgreSQL data with PHP</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>li {list-style: none;}</style>
  <?php 
    $result = pg_query($db, "SELECT user_id, user_name, is_admin FROM users WHERE user_name = '$_POST[username]'");
    $row = pg_fetch_all($result);
    $is_admin = $row[0][is_admin]; 
    
  ?>
</head>
<body>
  <div class="menu">
        <?php include 'menu.php';?>
  </div>
  <h2>Supply user data and enter to create</h2>
  <ul>
    <form name="create_user" action="index.php" method="POST" >
     <li>User ID:</li>
      <li><input type='text' name='user_id_created'/></li>
      <li>User Name:</li>
      <li><input type='text' name='user_name_created'/></li>
      <li>Password:</li>
      <li><input type='text' name='user_password_created'/></li>
      <li>Email:</li>
      <li><input type='text' name='email_created'/></li>
      <li><input type='submit' name='create_user' /></li>
    </form>
  </ul>
  <h2>Supply userid and enter to search</h2>
  <ul>
    <form name="display_user" action="index.php" method="POST" >
      <li>User ID:</li>
      <li><input type="text" name="userid" /></li>
      <li><input type="submit" name="search_user" /></li>
    </form>
  </ul>
  <h2>Supply task data and enter to create</h2>
  <ul>
    <form name="create_task" action="index.php" method="POST" >
      <li>Task ID:</li>
      <li><input type='text' name='task_id_created'/></li>
      <li>Owner ID:</li>
      <li><input type='text' name='owner_id_created'/></li>
      <li>Due Date (e.g.: yyyy-mm-dd):</li>
      <li><input type='text' name='due_date_created'/></li>
      <li>Due Time (e.g.: hh:mm:ss):</li>
      <li><input type='text' name='due_time_created'/></li>
      <li>Description:</li>
      <li><input type='text' name='description_created'/></li>
      <li><input type='submit' name='create_task' /></li>
    </form>
  </ul>
  <h2>Supply taskid and enter to search</h2>
  <ul>
    <form name="display_task" action="index.php" method="POST" >
      <li>Task ID:</li>
      <li><input type="text" name="taskid" /></li>
      <li><input type="submit" name="search_task" /></li>
    </form>
  </ul>
  <h2>Supply bid data and enter to create</h2>
  <ul>
    <form name="create_bid" action="index.php" method="POST" >
      <li>Bid ID:</li>
      <li><input type='text' name='bid_id_created'/></li>
      <li>Bidder ID:</li>
      <li><input type='text' name='bidder_id_created'/></li>
      <li>Task ID:</li>
      <li><input type='text' name='task_id_created'/></li>
      <li>Amount:</li>
      <li><input type='text' name='amount_created'/></li>
      <li><input type='submit' name='create_bid' /></li>
    </form>
  </ul>
  <h2>Supply bidid and enter to search</h2>
  <ul>
    <form name="display_bid" action="index.php" method="POST" >
      <li>Bid ID:</li>
      <li><input type="text" name="bidid" /></li>
      <li><input type="submit" name="search_bid" /></li>
    </form>
  </ul>


  <?php
    // Connect to the database. Please change the password in the following line accordingly
    $db     = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=test");
    $result = pg_query($db, "SELECT * FROM users where user_id = '$_POST[userid]'");    // Query template
    $row    = pg_fetch_assoc($result);    // To store the result row
    if (isset($_POST['search_user'])) { // search
        echo "<ul>
      <form name='update_user' action='index.php' method='POST' >
      <li>User ID:</li>
      <li><input type='text' name='user_id_updated' value='$row[user_id]' /></li>
      <li>User Name:</li>
      <li><input type='text' name='user_name_updated' value='$row[user_name]' /></li>
      <li>User Email:</li>
      <li><input type='text' name='user_email_updated' value='$row[user_email]' /></li>
      <li><input type='submit' name='update_user' /></li>
      </form>
      </ul>";
    }
    // for users
    if (isset($_POST['create_user'])) { // create
        $result = pg_query($db, "INSERT INTO users VALUES ('$_POST[user_id_created]',
    '$_POST[user_name_created]', '$_POST[user_password_created]', '$_POST[email_created]')");
        if (!$result) {
            echo "Create task failed!!";
        } else {
            echo "Create task successful!";
        }
    }
    if (isset($_POST['update_user'])) { // update
        $result = pg_query($db, "UPDATE users SET user_name = '$_POST[user_name_updated]', user_password = '$_POST[user_password_updated]', user_email = '$_POST[user_email_updated]' WHERE user_id = $_POST[user_id_updated]");
        // var_dump($result);
        // var_dump($_POST);
        if (!$result) {
            echo "Update user failed!!";
        } else {
            echo "Update user successful!";
        }
    }
    // for tasks
    if (isset($_POST['search_task'])) { // search
      $result = pg_query($db, "SELECT * FROM tasks where task_id = '$_POST[taskid]'");
      $row    = pg_fetch_assoc($result);    // To store the result row
        echo "<ul>
      <form name='update_task' action='index.php' method='POST' >
      <li>Task ID:</li>
      <li><input type='text' name='task_id_updated' value='$row[task_id]' /></li>
      <li>Owner ID:</li>
      <li><input type='text' name='owner_id_updated' value='$row[owner_id]' /></li>
      <li>Due Date (e.g.: yyyy-mm-dd):</li>
      <li><input type='text' name='task_due_date_updated' value='$row[due_date]' /></li>
      <li>Due Time (e.g.: hh:mm:ss):</li>
      <li><input type='text' name='task_due_time_updated' value='$row[due_time]' /></li>
      <li>Description:</li>
      <li><input type='text' name='description_updated' value='$row[description]' /></li>
      <li><input type='submit' name='update_task' /></li>
      </form>
      </ul>";
      // var_dump($result);
      // var_dump($_POST);
    }
    if (isset($_POST['create_task'])) { // create
        $result = pg_query($db, "INSERT INTO tasks VALUES ('$_POST[task_id_created]',
    '$_POST[owner_id_created]', '$_POST[due_date_created]',
    '$_POST[due_time_created]', '$_POST[description_created]')");
        if (!$result) {
            echo "Create task failed!!";
        } else {
            echo "Create task successful!";
        }
    }
    if (isset($_POST['update_task'])) { // update
        $result = pg_query($db, "UPDATE tasks SET owner_id = '$_POST[owner_id_updated]',
          due_date = '$_POST[task_due_date_updated]',
          due_time = '$_POST[task_due_time_updated]',
          description = '$_POST[description_updated]'
    WHERE task_id = $_POST[task_id_updated]");
        // var_dump($result);
        // var_dump($_POST);
        if (!$result) {
            echo "Update user failed!!";
        } else {
            echo "Update user successful!";
        }
    }
    // for bids
    if (isset($_POST['search_bid'])) { // search
      $result = pg_query($db, "SELECT * FROM bids where bid_id = '$_POST[bidid]'");
      $row    = pg_fetch_assoc($result);    // To store the result row
        echo "<ul><form name='update_bid' action='index.php' method='POST' >
      <li>Bid ID:</li>
      <li><input type='text' name='bid_id_updated' value='$row[bid_id]' /></li>
      <li>Bidder ID:</li>
      <li><input type='text' name='bidder_id_updated' value='$row[bidder_id]' /></li>
      <li>Task ID:</li>
      <li><input type='text' name='task_id_updated' value='$row[task_id]' /></li>
      <li>Amount:</li>
      <li><input type='text' name='amount_updated' value='$row[amount]' /></li>
      <li><input type='submit' name='update_bid' /></li>
      </form>
      </ul>";
      // var_dump($result);
      // var_dump($_POST);
    }
    if (isset($_POST['create_bid'])) { // create
        $result = pg_query($db, "INSERT INTO bids VALUES ('$_POST[bid_id_created]',
    '$_POST[bidder_id_created]', '$_POST[task_id_created]',
    '$_POST[amount_created]')");
        if (!$result) {
            echo "Create bid failed!!";
        } else {
            echo "Create bid successful!";
        }
    }
    if (isset($_POST['update_bid'])) {  // update
        $result = pg_query($db, "UPDATE bids SET bidder_id = '$_POST[bidder_id_updated]',
          task_id = '$_POST[task_id_updated]',
          amount = '$_POST[amount_updated]'
    WHERE bid_id = '$_POST[bid_id_updated]'");
        // var_dump($result);
        // var_dump($_POST);
        if (!$result) {
            echo "Update bid failed!!";
        } else {
            echo "Update bid successful!";
        }
    }
    ?>
</body>
</html>
