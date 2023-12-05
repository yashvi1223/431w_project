<?php
require_once 'database.php'; require_once 'flashmessages.php'; require_once 'auth.php'; authenticate('employee');
$customer = ['id' => '', 'name' => '', 'address' => '', 'email' => '', 'phone' => '', 'password'=>''];
$errors = [];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $customerData = get_customer($_GET['id']);
    if ($customerData) {
        $customer = $customerData;
    } else {
        set_flash_message('Customer not found.', 'danger');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $customer['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $customer['address'] = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $customer['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $customer['phone'] = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $customer['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (!$customer['name']) {
        $errors['name'] = 'Name is required.';
    }
    if(!$customer['password']){
        $errors['password'] = 'Password is required.';
    }

    if (!filter_var($customer['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email is required.';
    }

    if (empty($errors)) {
        try {
            if (!empty($customer['id'])) {
                update_customer($customer);
                set_flash_message('Customer updated successfully!', 'success');
            } else {
                insert_customer($customer);
                set_flash_message('Customer created successfully!', 'success');
            }

            header('Location: customers.php');
            exit;
        } catch (PDOException $e) {
            $errors['database'] = 'Database error: ' . $e->getMessage();
        }
    }
}
include 'header.php'; 
?>

<div class="container">
   <h2>Customer Form</h2>

    
    <?php if (!empty($errors['database'])): ?>
        <div class="alert alert-danger"><?php echo $errors['database']; ?></div>
    <?php endif; ?>

    <form action="customerform.php<?php echo !empty($customer['id']) ? '?id=' . $customer['id'] : ''; ?>" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control <?php echo !empty($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>">
            <?php if (!empty($errors['name'])): ?>
                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($customer['address']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control <?php echo !empty($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>">
            <?php if (!empty($errors['email'])): ?>
                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Password</label>
            <input type="password" class="form-control <?php echo !empty($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" >
            <?php if (!empty($errors['password'])): ?>
                <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
        </div>


        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<?php
include 'footer.php'; ?>
