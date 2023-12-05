<?php
require_once 'database.php'; require_once 'flashmessages.php'; require_once 'auth.php'; authenticate('manager');
$user = current_user();
$created_by=$user['entity']['id'];
$employee = [ 'name' => '', 'email' => '', 'password' => '', 'created_by' => $created_by, 'is_manager'=>false];
$errors = [];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $employeeData = get_employee($_GET['id']);
    if ($employeeData) {
        $employee = $employeeData;
    } else {
        set_flash_message('Employee not found.', 'danger');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $employee['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $employee['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $employee['password'] = $_POST['password'];     $employee['is_manager'] = isset($_POST['is_manager']) ? (bool)$_POST['is_manager'] : false;

    if (!$employee['name']) {
        $errors['name'] = 'Name is required.';
    }

    if (!filter_var($employee['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email is required.';
    }

    if (strlen($employee['password']) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        try {
                        if (!empty($employee['id'])) {
                update_employee($employee);
                set_flash_message('Employee updated successfully!', 'success');
            } else {
                insert_employee($employee);
                set_flash_message('Employee created successfully!', 'success');
            }

            header('Location: employees.php');
            exit;
        } catch (PDOException $e) {
            $errors['database'] = 'Database error: ' . $e->getMessage();
        }
    }
}
include 'header.php'; 
?>

<div class="container">
    <h2>Add Employee</h2>

    <!-- Displaying database errors -->
    <?php if (!empty($errors['database'])): ?>
        <div class="alert alert-danger"><?php echo $errors['database']; ?></div>
    <?php endif; ?>

    <form action="employeeform.php<?php echo !empty($employee['id']) ? '?id=' . $employee['id'] : ''; ?>" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control <?php echo !empty($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>">
            <?php if (!empty($errors['name'])): ?>
                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control <?php echo !empty($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>">
            <?php if (!empty($errors['email'])): ?>
                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control <?php echo !empty($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password">
            <?php if (!empty($errors['password'])): ?>
                <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
        </div>

       

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_manager" name="is_manager" <?php echo $employee['is_manager'] ? 'checked' : ''; ?>>
            <label for="is_manager" class="form-check-label">Is Manager</label>
        </div>


        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<?php
include 'footer.php'; ?>
