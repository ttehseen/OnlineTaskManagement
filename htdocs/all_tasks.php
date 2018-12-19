<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>MY TASK : All Available Tasks </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }

          table {
            width: 100%;
            min-width: 500px;
          }
          th, td {
            padding: 20px;
            border: 1px solid #444444;
          }
    </style>
</head>
<body>
    <div class="menu">
        <?php include 'menu.php';?>
    </div>
    <div class="wrapper">
        <h2>All Available Tasks</h2>
        <form name='search_bar' action='all_tasks.php' method='POST' >
        <li>Search For Tasks With Description</li>
        <li><input type='text' name='search_bar' value='' /></li>
        <li><input type='submit' name='search' /></li>
        </form>
        <?php
        session_start();
        $userid = $_SESSION['user'];

        echo '<h5>' . 'Tasks you can bid for' . '</h5>';
        ob_start();
        include 'login.php';
        ob_end_clean();

        $db     = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=test");
        $result = pg_query($db, "SELECT t.description, t.due_date, t.due_time, u.user_name FROM tasks t, users u WHERE t.owner_id = u.user_id AND t.task_id NOT IN (SELECT p.task_id FROM is_picked_for p) AND t.owner_id <> $userid ORDER BY t.due_date");


        echo '<table width="300%"><tr>';

        if (isset($_POST['search'])) { // search
          $result = pg_query($db, "SELECT t.description, t.due_date, t.due_time, u.user_name
            FROM tasks t, users u WHERE t.owner_id = u.user_id AND t.task_id
            NOT IN (SELECT p.task_id FROM is_picked_for p) AND t.owner_id <> $userid AND t.description LIKE '%$_POST[search_bar]%' ORDER BY t.due_date");
        }
        $i = 0;
        while ($i < pg_num_fields($result))
        {
            $fieldName = str_replace("_"," ",pg_field_name($result, $i));
            $fieldName = str_replace("user","owner",$fieldName);
            echo '<td>' . ucwords($fieldName) . '</td>';
            $i = $i + 1;
        }
        echo '<th> Make a bid </th>';
        echo '</tr>';

        $counter = 0;

        while ($row = pg_fetch_row($result))
        {
            echo '<tr>';
            $count = count($row);
            $y = 0;
            while ($y < $count)
            {
                $c_row = current($row);
                echo '<td>' . $c_row . '</td>';
                next($row);
                $y = $y + 1;
            }

            echo "<td>";

            echo "<form action='' method='post'>";
            echo "<input type='text' name='amount' id='amount' >";
            echo "<input type='submit' name='accept' value=$counter > ";
            echo "</form>";
            $counter = $counter + 1;
            echo "</td>";
            echo '</tr>';

        }
        pg_free_result($result);

        if (isset($_POST['accept'])) {
            $rownumber = $_POST['accept'];
            $result = pg_query($db, "SELECT t.task_id FROM tasks t, users u WHERE t.owner_id = u.user_id AND t.task_id NOT IN (SELECT p.task_id FROM is_picked_for p) AND t.owner_id <> $userid ORDER BY t.due_date LIMIT 1 OFFSET $rownumber;");
            $task = pg_fetch_assoc($result);
            $taskno = $task['task_id'];
            $amount = $_POST['amount'];
            $result = pg_query($db, "INSERT INTO bids(bidder_id, task_id, amount) VALUES ($userid, $taskno, $amount);");
            echo "Bid successful!";
          }

        echo '</table>';



        ?>
</body>
</html>
