<?php

require_once 'database.php'; 
require_once 'auth.php'; authenticate('employee');


$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

[$employees, $total_pages] = get_employees($page, $searchQuery);
include 'header.php';

?>

<div class="container mt-4">
    <div class="d-flex  justify-content-between"><h2>Employees</h2><a href="employeeform.php" class="btn btn-primary">Add Employee</a></div>
    <form action="employees.php" method="get" class="d-flex mb-3 mt-3">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by name or email" value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?php echo htmlspecialchars($employee['name']); ?></td>
                    <td><?php echo htmlspecialchars($employee['email']); ?></td>
                    <td>
                        <a href="employeeform.php?id=<?php echo $employee['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="deactivate.php?type=employee&id=<?php echo $employee['id']; ?>" class="btn btn-sm btn-danger">Deactivate</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <nav>
        <ul class="pagination">
            <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchQuery); ?>">Previous</a>
            </li>
            
            <li class="page-item <?php if ($page >= $total_pages ) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchQuery); ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

<?php
include 'footer.php';
?>
