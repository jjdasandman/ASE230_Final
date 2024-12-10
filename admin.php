<?php 
if (!isset($_SESSION)) {
    session_start();
}

include_once 'utils.php';
include_once 'db_connection.php';
include_once 'navbar.php';

// Check if the logged-in user is an admin
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = getUserRole($db, $_SESSION['username']);
if (!$userId || $userId != 2) { // Assuming role_id = 2 is for admin
    echo "Access denied. You do not have the necessary privileges.";
    exit();
}

// Handle row creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table']);
    try {
        $columns = array_keys($_POST['fields']);
        $values = array_values($_POST['fields']);

        $columnList = implode(", ", $columns);
        $placeholders = implode(", ", array_fill(0, count($columns), '?'));

        $stmt = $db->prepare("INSERT INTO $table ($columnList) VALUES ($placeholders)");
        $stmt->execute($values);
        $success = "Row added successfully.";
    } catch (PDOException $e) {
        error_log("Error inserting row into $table: " . $e->getMessage());
        $error = "An error occurred while adding the row.";
    }
}

// Handle row deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table']);
    $recordId = $_POST['id'];

    try {
        $primaryKeyQuery = $db->prepare("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
        $primaryKeyQuery->execute();
        $primaryKeyResult = $primaryKeyQuery->fetch(PDO::FETCH_ASSOC);
        $primaryKey = $primaryKeyResult['Column_name'] ?? null;

        if ($primaryKey) {
            $stmt = $db->prepare("DELETE FROM $table WHERE $primaryKey = :id");
            $stmt->execute([':id' => $recordId]);
            $success = "Row deleted successfully.";
        } else {
            $error = "No primary key found for the table.";
        }
    } catch (PDOException $e) {
        error_log("Error deleting row from $table: " . $e->getMessage());
        $error = "An error occurred while deleting the row.";
    }
}

// Fetch all tables
$tables = ['clothingpost', 'clothingtype', 'colors', 'materials', 'role', 'user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Area</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
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
                    $primaryKeyQuery = $db->prepare("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
                    $primaryKeyQuery->execute();
                    $primaryKeyResult = $primaryKeyQuery->fetch(PDO::FETCH_ASSOC);
                    $primaryKey = $primaryKeyResult['Column_name'] ?? null;

                    $data = $db->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($data as $row): ?>
                        <tr>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row[$primaryKey]); ?>">
                                
                                <?php foreach ($row as $key => $value): ?>
                                    <td>
                                        <input type="text" name="fields[<?php echo htmlspecialchars($key); ?>]" value="<?php echo htmlspecialchars($value); ?>"
                                            class="form-control">
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <button type="submit" name="update" class="btn btn-success btn-sm mt-2">Save</button>
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm mt-2">Delete</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <form method="POST">
                            <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
                            <?php foreach ($columns as $column): ?>
                                <td>
                                    <input type="text" name="fields[<?php echo htmlspecialchars($column); ?>]" class="form-control">
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <button type="submit" name="create" class="btn btn-primary btn-sm mt-2">Add</button>
                            </td>
                        </form>
                    </tr>
                </tbody>
            </table>
        <?php endforeach; ?>

        <a href="posts/index.php" class="btn btn-secondary">Back to Posts</a>
    </div>
</body>
</html>
