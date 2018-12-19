<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>MY TASK : Assign Task</title>
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
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>

</head>
<body>
    <div class="menu">
        <?php include 'menu.php';?>
    </div>
    <div class="wrapper">
        <h2> Assign Task</h2>
        <?php
        session_start();
        $userid = $_SESSION['user'];

        echo '<h5>' . 'Tasks To Assign' . '</h5>';
        ob_start();
        include 'login.php';
        ob_end_clean();

        $db     = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=test");
        $result = pg_query($db, "SELECT u.user_name, t.description, t.due_date, t.due_time, b.amount FROM bids b, tasks t, users u WHERE b.bidder_id = u.user_id and b.task_id = t.task_id and t.owner_id = $userid and t.task_id NOT IN (SELECT p.task_id FROM is_picked_for p)");

        $i = 0;
        echo '<table><tr>';
        while ($i < pg_num_fields($result))
        {
            $fieldName = str_replace("_"," ",pg_field_name($result, $i));
            $fieldName = str_replace("user","bidder",$fieldName);
            echo '<th>' . ucwords($fieldName) . '</th>';
            $i = $i + 1;
        }
        echo '<th> Assign to this Bidder </th>';
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
                echo '<td>' . $c_row . '</td>' ;
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
            $result = pg_query($db, "SELECT t.task_id, b.bid_id FROM bids b, tasks t, users u WHERE b.bidder_id = u.user_id and b.task_id = t.task_id and t.owner_id = $userid and t.task_id NOT IN (SELECT p.task_id FROM is_picked_for p) ORDER BY user_id LIMIT 1 OFFSET $rownumber;");
            $bid = pg_fetch_assoc($result);
            $bidder = $bid['bid_id'];
            $task = $bid['task_id'];
            echo $bidder;
            echo $task;
            $result = pg_query($db, "INSERT INTO is_picked_for(task_id, bid_id) VALUES ($task, $bidder);");
            echo $result;
            header("Refresh:0");
          }

        echo '</table>';
        ?>
</body>
</html>