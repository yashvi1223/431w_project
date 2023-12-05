<?php
require_once 'database.php'; require_once 'flashmessages.php'; require_once 'auth.php'; authenticate('employee');



$employeeId = current_user()['entity']['id'];
$customerId = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : null;
$rewardId = isset($_GET['reward_id']) ? intval($_GET['reward_id']) : null;


$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$query = isset($_GET['search']) ? $_GET['search'] : '';

$customer = $customerId ? get_customer($customerId) : null;
$customerPoints = $customer ? get_customer_points($customerId) : 0;

if (!$customerId || !$customer || $customer['inactive']) {
    set_flash_message('Please select a valid customer from the customer page.', 'danger');
    header('Location: customers.php');
    exit;
}

$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $rewardId) {
    try {
        redeem_reward($rewardId, $customerId, $employeeId);         set_flash_message('Reward redeemed successfully.', 'success');
        header('Location: customers.php');
        exit;
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();     }
}

[ $rewards, $totalPages ] = get_rewards($page, $query);
$rewardSelected = $rewardId ? get_reward($rewardId) : null;

include 'header.php'; 
?>

<div class="container mt-1">

    <h2>Customer</h2>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($customer['name']); ?></h5>
            <p class="card-text">Points Available: <?php echo $customerPoints; ?></p>
        </div>
    </div>

    
    <?php if (!$rewardId) : ?>
        <h2>Select a reward</h2>

        <form action="redeem.php" method="get" class="d-flex mb-3 mt-3">
        <input type="hidden" name="customer_id" value="<?php echo $customerId; ?>">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by name" value="<?php echo htmlspecialchars($query); ?>">
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
                        <?php 
                        if($customerPoints>=$reward['points']): 
                        ?>
                            <a href="redeem.php?customer_id=<?php echo $customerId?>&reward_id=<?php echo $reward['id'] ?>" class="btn btn-primary">Select</a>
                        <?php else: ?>
                            <span class="text-danger">Not enough points</span>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($query); ?>&customer_id=<?php echo $customerId?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

        <?php endif;?>
    
    <?php if ($rewardId): ?>

        <h2>Redeem Reward</h2>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($rewardSelected['name']); ?></h5>
                        <p class="card-text">
                            <i class="fas fa-trophy"></i> 
                            <?php echo htmlspecialchars($rewardSelected['points']); ?> points
                        </p>
                        
                       
                            <a href="redeem.php?customer_id=<?php echo $customerId?>" class="btn btn-warning">Change</a>
                        
                       
                    </div>
                </div>
        
        <form action="redeem.php?customer_id=<?php echo $customerId; ?>&reward_id=<?php echo $rewardId; ?>" method="post">
            <input type="hidden" name="reward_id" value="<?php echo $rewardId; ?>">
            <input type="hidden" name="customer_id" value="<?php echo $customerId; ?>">
            <input type="hidden" name="employee_id" value="<?php echo $employeeId; ?>">
            <?php echo $customerPoints<$rewardSelected['points'] ? "<div class='text-danger'>Not Enough Points</div>" : ""?> 
            <button type="submit" <?php echo $customerPoints>=$rewardSelected['points'] ? "" : "disabled=disabled"?> class="btn btn-primary mt-1">Redeem Reward (- <?php echo $rewardSelected['points'] ?>)</button>
        </form>
    <?php endif; ?>

  
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
</div>

<?php
include 'footer.php'; ?>
