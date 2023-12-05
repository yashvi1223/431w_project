<?php
require_once 'database.php'; require_once 'flashmessages.php'; require_once 'auth.php'; authenticate('employee');

$employee = current_user()['entity'];
$customerId = $_GET['customer_id'] ?? null;
$customer = null;
$errors = [];

$visitDate = date('Y-m-d');
if ($customerId) {
    $customer = get_customer($customerId);

    if (!$customer) {
        set_flash_message('Customer not found.', 'danger');
        header('Location: customers.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employeeId = $employee['id'];
    $billAmount = filter_input(INPUT_POST, 'bill_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $visitDate = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $visitDate = $visitDate ?? date('Y-m-d'); 


    try {
                insert_visit(['customer_id' => $customerId, 'employee_id' => $employeeId, 'bill_amount' => $billAmount, 'date' => $visitDate]);
        set_flash_message('Visit recorded successfully.', 'success');
        header('Location: customers.php');
        exit;
    } catch (PDOException $e) {
        $errors['database'] = 'Database error: ' . $e->getMessage();
    }
}
include 'header.php'; 
?>

<div class="container">
    <?php echo get_flash_message(); ?>

    <?php if ($customer): ?>
        <!-- Customer Card -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($customer['name']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($customer['address']); ?></p>
                <p class="card-text"><?php echo htmlspecialchars($customer['email']); ?></p>
                <p class="card-text"><?php echo htmlspecialchars($customer['phone']); ?></p>
            </div>
        </div>

        <!-- Visit Form -->
        <form action="record_visit.php?customer_id=<?php echo $customerId; ?>" method="post">

            <div class="mb-3">
                <label for="bill_amount" class="form-label">Bill Amount</label>
                <input type="number" class="form-control" id="bill_amount" name="bill_amount" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">Visit Date</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo $visitDate ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Record Visit</button>
        </form>
    <?php else: ?>
        <p>Customer not found.</p>
    <?php endif; ?>

    <?php if (!empty($errors['database'])): ?>
        <div class="alert alert-danger"><?php echo $errors['database']; ?></div>
    <?php endif; ?>
</div>

<?php
include 'footer.php'; ?>
