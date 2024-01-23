<?php
    // Exercise 8
    include_once('MysqliDb.php');
    error_reporting(E_ALL);

    $db = new MysqliDb('localhost', 'root', '', 'p8_exercise_backend');
    $data = array(); 

    $searchQuery = isset($_POST['search']) ? $_POST['search'] : '';
    function printEmployees($searchQuery) {
        global $db;
        $db->where('1', '1');
        
        // Retrieve employees data by either by First Name, Last Name or Middle Name
        if (!empty($searchQuery)) {
            $db->where('first_name', '%' . $searchQuery . '%', 'like');
            $db->orWhere('last_name', '%' . $searchQuery . '%', 'like');
            $db->orWhere('middle_name', '%' . $searchQuery . '%', 'like');
        }

        $employees = $db->get('employee');

        if ($db->count == 0) {
            echo "<p>No records match</p>";
            return;
        }

        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>First Name</th>";
        echo "<th>Last Name</th>";
        echo "<th>Middle Name</th>";
        echo "<th>Birthday</th>";
        echo "<th>Address</th>";
        echo "<th>Action</th>";
        echo "</tr>";

        foreach ($employees as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['first_name']}</td>";
            echo "<td>{$row['last_name']}</td>";
            echo "<td>{$row['middle_name']}</td>";
            echo "<td>{$row['birthday']}</td>";
            echo "<td>{$row['address']}</td>";
            echo "<td>
                    <form method='POST'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <button type='submit' name='action' value='update'>Update</button>
                        <button type='submit' name='action' value='delete'>Delete</button>
                    </form>
                </td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add'])) {
            $data = array (
                'first_name' => $_POST['firstname'],
                'last_name' => $_POST['lastname'],
                'middle_name' => $_POST['middlename'],
                'birthday' => $_POST['birthday'],
                'address' => $_POST['address'],
            );

            $id = $db->insert('employee', $data);

            if ($id) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $db->getLastError();
            }
        } elseif (isset($_POST['action'])) {
            $id = $_POST['id'];

            if ($_POST['action'] === 'update') {
                $employee = $db->where('id', $id)->getOne('employee');
                $updateId = $employee['id'];
            } elseif ($_POST['action'] === 'delete') {
                $db->where('id', $id);
                if ($db->delete('employee')) {
                    echo "Record deleted successfully";
                } else {
                    echo "Error deleting record: " . $db->getLastError();
                }
            }
        } elseif (isset($_POST['update_record'])) {
            $updateId = $_POST['update_id'];

            $data = array (
                'first_name' => $_POST['firstname'],
                'last_name' => $_POST['lastname'],
                'middle_name' => $_POST['middlename'],
                'birthday' => $_POST['birthday'],
                'address' => $_POST['address'],
            );

            $db->where('id', $updateId);
            if ($db->update('employee', $data)) {
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $db->getLastError();
            }
        }
    }   

?>
<!DOCTYPE html>
    <html>
    
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie-edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Simple CRUD</title>
        <style>
            h1 {
                text-align: center;
            }
            table {
                width: 90%;
                margin: 0 auto;
                border-collapse: collapse;
                margin-top: 5px;
            }

            th, td {
                border: 1px solid black;
                text-align: center;
                padding: 8px;
            }

            th {
                color: white;
                background-color: #606C5D;
            }

            tr:hover {
                background-color: #f5f5f5;
            }

            form {
                margin-bottom: 10px;
            }

            input[type="text"],
            input[type="date"] {
                width: 200px;
                padding: 8px;
                margin: 5px 0;
                box-sizing: border-box;
            }

            button {
                background-color: #739072;
                color: white;
                padding: 10px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            button:hover {
                background-color: #65B741;
            }

            #firstname, #lastname, #birthday, #middlename, #address {
                display: inline-block;
                text-align: right;
                margin-left: 75px;
            }

            #submit {
                margin-top: 10px;
                margin-left: 220px;
            }

            #middlename-style {
                margin-left: 55px;
            }
            
            #birthday-style {
                margin-left: 88px;
            }

            #address-style {
                margin-left: 90px;
            }

            #search-form {
                display: inline-block;
                text-align: right;
                margin-left: 75px;
            }

            #search, #search-button {
                margin-right: 5px;
            }
        </style>
    </head>
    <body>
        <h1>Exercise 8: Usage of ThingEngineer</h1>
        <?php
        if (isset($updateId)) {
            $sql = "SELECT * FROM employee WHERE id = $updateId";
            $result = $conn->query($sql);
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $empFirstName = $row['first_name'];
                $empLastName= $row['last_name'];
                $empMiddleName = $row['middle_name'];
                $empBirthday = $row['birthday'];
                $empAddress = $row['address'];
            }
        } else {
            $empFirstName = $empLastName = $empMiddleName = $empBirthday = $empAddress = "";
        }
        ?>
        <form method="POST">
            <label for="firstname" id="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo $empFirstName; ?>"><br>
            <label for="lastname" id="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo $empLastName; ?>"><br>
            <label for="middlename" id="middlename">Middle Name:</label>
            <input type="text" id="middlename-style" name="middlename" value="<?php echo $empMiddleName; ?>"><br>
            <label for="birthday" id="birthday">Birthday:</label>
            <input type="date" id="birthday-style" name="birthday" value="<?php echo $empBirthday; ?>"><br>
            <label for="address" id="address">Address:</label>
            <input type="text" id="address-style" name="address" value="<?php echo $empAddress; ?>"><br>
            <?php
            if (isset($updateId)) {
                echo "<input type='hidden' name='update_id' value='{$updateId}'>";
                echo "<button type='submit' name='update_record'>Update</button>";
            } else {
                echo "<button type='submit' id='submit' name='add'>Submit</button>";
            }
            ?>
        </form>
        <br>
        <form method="POST" id="search-form">
            <input type="text" id="search" name="search" placeholder="Enter your search query" value="<?php echo $searchQuery; ?>">
            <button type="submit" id="search-button">Search</button>
        </form>
        <br>
        <?php printEmployees($searchQuery); ?>
    </body>
</html>