<?php
require_once 'database.php'; require_once 'flashmessages.php'; require_once 'auth.php'; authenticate('employee');

$type = $_GET['type'] ?? null;
$id = $_GET['id'] ?? null;

if (!$type || !$id || !in_array($type, ['employee', 'reward', 'customer'])) {
    set_flash_message('Invalid request.', 'danger');
    header('Location: index.php');     exit;
}

try {
    $pdo = get_connection();
    $sql = "UPDATE {$type}s SET inactive = TRUE WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        set_flash_message(ucfirst($type) . ' deactivated successfully.', 'success');
    } else {
        set_flash_message(ucfirst($type) . ' not found.', 'danger');
    }
} catch (PDOException $e) {
    set_flash_message('Database error: ' . $e->getMessage(), 'danger');
}

switch ($type) {
    case 'employee':
        header('Location: employees.php');
        break;
    case 'reward':
        header('Location: rewards.php');
        break;
    case 'customer':
        header('Location: customers.php');
        break;
    default:
        header('Location: index.php');
        break;
}

exit;
?>
