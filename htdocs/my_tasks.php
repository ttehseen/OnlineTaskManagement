<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>MY TASK : My Tasks </title>
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
        <h2>My Tasks</h2>
        <?php
        session_start();
        $userid = $_SESSION['user'];

        echo '<h5>' . 'Tasks you have created with assignees' . '</h5>';
        ob_start();
        include 'login.php';
        ob_end_clean();

        $db     = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=test");
        $result = pg_query($db, "SELECT t.description, bd.user_name as bidder, t.due_date, t.due_time FROM users o JOIN tasks t on o.user_id = t.owner_id JOIN is_picked_for i on i.task_id = t.task_id JOIN bids b on b.bid_id = i.bid_id JOIN users bd on b.bidder_id = bd.user_id WHERE o.user_id = $userid ORDER BY t.due_date");
        echo "Here";
        echo $result;
        echo '<table width="300%"><tr>';

        $i = 0;
        while ($i < pg_num_fields($result))
        {
            $fieldName = str_replace("_"," ",pg_field_name($result, $i));
            $fieldName = str_replace("user","owner",$fieldName);
            echo '<th>' . ucwords($fieldName) . '</th>';
            $i = $i + 1;
        }
        echo '<th> Delete </th>';
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
            echo "<input type='submit' name='accept' value=$counter > ";
            echo "</form>";
            $counter = $counter + 1;
            echo "</td>";
            echo '</tr>';

        }
        pg_free_result($result);

        if (isset($_POST['accept'])) {
            $rownumber = $_POST['accept'];
            $result = pg_query($db, "SELECT t.task_id FROM users o JOIN tasks t on o.user_id = t.owner_id JOIN is_picked_for i on i.task_id = t.task_id JOIN bids b on b.bid_id = i.bid_id JOIN users bd on b.bidder_id = bd.user_id WHERE o.user_id = $userid ORDER BY t.due_date LIMIT 1 OFFSET $rownumber");
            $task = pg_fetch_assoc($result);
            $taskno = $task['task_id'];
            $result = pg_query($db, "DELETE FROM tasks WHERE task_id = $taskno;");
            echo "Successfully removed";
            header("Refresh:0");
          }
        echo '</table>';

        echo '<br>';
        echo '<br>';
        echo '<h5>' . 'Tasks you have created without assignees' . '</h5>';
        $result = pg_query($db, "SELECT t.description, t.due_date, t.due_time FROM users o JOIN tasks t on o.user_id = t.owner_id WHERE t.task_id NOT IN (SELECT task_id FROM is_picked_for) AND o.user_id = $userid ORDER BY t.due_date");

        echo '<table width="300%"><tr>';

       
        $i = 0;
        while ($i < pg_num_fields($result))
        {
            $fieldName = str_replace("_"," ",pg_field_name($result, $i));
            $fieldName = str_replace("user","owner",$fieldName);
            echo '<td>' . ucwords($fieldName) . '</td>';
            $i = $i + 1;
        }
        echo '<th> Delete </th>';
        echo '</tr>';

        $counter2 = 0;

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
            echo "<input type='submit' name='accept2' value=$counter2 > ";
            echo "</form>";
            $counter2 = $counter2 + 1;
            echo "</td>";
            echo '</tr>';

        }
        pg_free_result($result);

        if (isset($_POST['accept2'])) {
            $rownumber = $_POST['accept2'];
            echo $rownumber;
            $result = pg_query($db, "SELECT t.task_id FROM users o JOIN tasks t on o.user_id = t.owner_id WHERE t.task_id NOT IN (SELECT task_id FROM is_picked_for) AND o.user_id = $userid ORDER BY t.due_date LIMIT 1 OFFSET $rownumber");
            $task = pg_fetch_assoc($result);
            $taskno = $task['task_id'];
            $result = pg_query($db, "DELETE FROM tasks WHERE task_id = $taskno;");
            echo "Successfully removed";
            header("Refresh:0");
          }
        echo '</table>';


        ?>
</body>
</html>