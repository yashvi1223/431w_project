<?php
require_once 'database.php'; require_once 'flashmessages.php'; require_once 'auth.php'; authenticate('employee');

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$result = get_customers($page, $searchQuery);
[$customers, $totalPages] = $result;

include 'header.php'; 
?>

<div class="container mt-3">
    <div class="d-flex  justify-content-between"><h2>Customers</h2><a href="customerform.php" class="btn btn-primary">Add Customer</a></div>

    <!-- Search Form -->
    <form action="customers.php" method="get" class="mb-4 mt-2">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search customers..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
    

    <!-- Customers Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><a href="customer.php?customer_id=<?php echo $customer['id']?>"><?php echo htmlspecialchars($customer['name']); ?></a></td>
                    <td><?php echo htmlspecialchars($customer['address']); ?></td>
                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                    <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                    <td>
                        <a href="customerform.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="record_visit.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-primary">Record Visit</a>
                        <a href="redeem.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-primary">Redeem</a>
                        <a href="deactivate.php?type=customer&id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-danger">Deactivate</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<?php
include 'footer.php'; ?>
