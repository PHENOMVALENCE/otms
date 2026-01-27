<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>create Task</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input,
        select,
        textarea {
            width: 50%;
            padding: 8px;
            box-sizing: border-box;
        }

        .btn-warning {
            background-color: yellow;
            border: none;
            color: black;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
            width: 10%;

        }

        .btn-warning:hover {
            background-color: gold;
        }

        h3 {
            color: white;
        }

        .form-group label {
            color: lightskyblue;
        }
    </style>
</head>

<body>
    <b>
        <h3>CREATE A NEW TASK HERE.</h3>
    </b>
    <div class="row">
        <div class="col-md-6">
            <form action="admin_dashboard.php" method="post">
                <div class="form-group">
                    <label>Select user:</label>
                    <select class="form-control" name="user_id">
                        <option value="-1">- select -</option>
                        <?php
                        include('../includes/config.php');
                        $query = "SELECT id, username FROM users";
                        $result = mysqli_query($connection, $query);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['id'] . '">' . $row['username'] . '</option>';
                            }
                        }
                        ?>


                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" cols="40" placeholder="Mention the task"></textarea>
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date">
                </div>

                <div class="form-group">
                    <label for="end-date">End Date</label>
                    <input type="date" id="end_date" name="end_date">
                </div>
                <input type="submit" class="btn btn-warning" name="create_task" value="create">
            </form>
        </div>
    </div>

</body>

</html>