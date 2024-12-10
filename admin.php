<?php
if (!isset($_SESSION)) {
    session_start();
}

include_once 'utils.php';
include_once 'db_connection.php'; // Include your database connection
include_once $_SERVER['DOCUMENT_ROOT'] . '/Final2/posts/navbar.php';

// Check if the logged-in user is an admin
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch role of the user
$userId = getUserRole($db, $_SESSION['username']);
if (!$user || $userId != 2) { // Assuming role_id = 2 is for admin
    echo "Access denied. You do not have the necessary privileges.";
    exit();
}

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table']); // Sanitize table name
    $field = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['field']); // Sanitize field name
    echo "Table: " . $table . " Field: " . $field;

    $value = $_POST['value'];
    $recordId = $_POST['id']; // ID from form submission (not always 'id')

    // Fetch the primary key dynamically
    $primaryKeyQuery = $db->prepare("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
    $primaryKeyQuery->execute();
    $primaryKeyResult = $primaryKeyQuery->fetch(PDO::FETCH_ASSOC);
    $primaryKey = $primaryKeyResult['Column_name'] ?? null;

    if ($primaryKey) {
        // Debug: Print primary key and the values being updated
        echo "Primary Key: " . $primaryKey . "<br>";
        echo "Record ID: " . $recordId . "<br>";

        // Fetch current value of the field before updating
        $currentValueQuery = $db->prepare("SELECT $field FROM $table WHERE $primaryKey = :id");
        $currentValueQuery->execute([':id' => $recordId]);
        $currentValue = $currentValueQuery->fetchColumn();
        echo "Current Value: " . $currentValue . "<br>";
        echo "New Value: " . $value . "<br>";

        if ($currentValue != $value) {
            // Construct the update query dynamically
            $query = "UPDATE $table SET $field = :value WHERE $primaryKey = :id";
            echo "Executing query: " . $query . " with value: " . $value . "<br>";

            $stmt = $db->prepare($query);
            $stmt->execute([':value' => $value, ':id' => $recordId]);

            // Check if any rows were updated
            if ($stmt->rowCount() > 0) {
                echo "Record updated successfully.";
            } else {
                echo "No rows updated. The value might be the same as before or no matching record was found.";
            }
        } else {
            echo "The new value is the same as the current value. No update performed.";
        }
    } else {
        echo "No primary key found for the table.";
    }
}

// Fetch all tables
$tables = ['clothingpost', 'clothingtype', 'colors', 'materials', 'role', 'user']; // Add your database table names here
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Area</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Final2/css/styles.css">
</head>

<body>
    <div class="container">
        <h1>Admin Area</h1>

        <?php
        if (isset($success)) {
            echo "<p class='text-success'>$success</p>";
        } elseif (isset($error)) {
            echo "<p class='text-danger'>$error</p>";
        }
        ?>

        <?php foreach ($tables as $table): ?>
            <h2><?php echo htmlspecialchars($table); ?></h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <?php
                        // Fetch table columns
                        $columns = $db->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($columns as $column) {
                            
                            echo "<th>" . htmlspecialchars($column) . "</th>";
                        }
                        ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch primary key of the table
                    $primaryKeyQuery = $db->prepare("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
                    $primaryKeyQuery->execute();
                    $primaryKeyResult = $primaryKeyQuery->fetch(PDO::FETCH_ASSOC);
                    $primaryKey = $primaryKeyResult['Column_name'] ?? null;

                    // Fetch table data
                    $data = $db->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($row as $key => $value): ?>
                                <td>
                                    <form method="POST" style="display:inline-block;">
                                        <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row[$primaryKey]); ?>">
                                        <input type="hidden" name="primary_key"
                                            value="<?php echo htmlspecialchars($primaryKey); ?>">
                                        <input type="hidden" name="field" value="<?php echo htmlspecialchars($key); ?>">
                                        <input type="text" name="value" value="<?php echo htmlspecialchars($value); ?>"
                                            class="form-control">
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <button type="submit" name="update" class="btn btn-success btn-sm">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <a href="posts/index.php" class="btn btn-secondary">Back to Posts</a>
    </div>
</body>

</html>