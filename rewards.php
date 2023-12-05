<?php
require_once 'database.php'; 
require_once 'auth.php'; authenticate('employee');
$current_user = current_user()['user'];

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$result = get_rewards($page, $searchQuery);

[$rewards, $totalPages] = $result;
include 'header.php'; 

?>

<div class="container mt-2">
<div class="d-flex mt-2 justify-content-between"><h2>Rewards</h2><a href="rewardform.php" class="btn btn-primary">Add Reward</a></div>

<form action="rewards.php" method="get" class="d-flex mb-3 mt-3">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by name" value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <div class="row">
        <?php foreach ($rewards as $reward): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($reward['name']); ?></h5>
                        <p class="card-text">
                            <i class="fas fa-trophy"></i> 
                            <?php echo htmlspecialchars($reward['points']); ?> points
                        </p>
                        <a href="rewardform.php?id=<?php echo $reward['id']; ?>" class="btn btn-primary">Edit</a>
                        <a href="deactivate.php?type=reward&id=<?php echo $reward['id']; ?>" class="btn btn-danger">Deactivate</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

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
