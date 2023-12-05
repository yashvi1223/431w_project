<?php
require_once 'database.php'; require_once 'auth.php'; authenticate();
$current_user = current_user();
$current_entity = $current_user['entity'];
$entity_type = $current_user['user']['entity_type'];

$customerId = $_GET['customer_id'] ?? null;

if (!$customerId && $entity_type=='employee') {
    echo "<p>No customer ID provided.</p>";
    include 'footer.php';     exit;
}
$customerId = $customerId ?? $current_entity['id'];

$customer = get_customer($customerId);
if (!$customer) {
    echo "<p>Customer not found.</p>";
    include 'footer.php';     exit;
}

$points = get_customer_points($customerId);
$recentRewards = get_recent_rewards($customerId);
$recentRedemptions = get_recent_redemptions($customerId);
$recentVisits = get_recent_visits($customerId); if($entity_type== "employee")
    include 'header.php'; 
?>

<div class="container">
        <h1>Customer Details</h1>
        <p>Name: <?php echo htmlspecialchars($customer['name']); ?></p>
        <p>Email: <?php echo htmlspecialchars($customer['email']); ?></p>
        <p>Address: <?php echo htmlspecialchars($customer['address']); ?></p>
        <p>Points: <?php echo $points; ?></p>
        <?php if($entity_type=='employee'): ?><div>
            <a href="customerform.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="record_visit.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-primary">Record Visit</a>
                        <a href="redeem.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-primary">Redeem</a>
                        <a href="deactivate.php?type=customer&id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-danger">Deactivate</a>
        </div>
        <?php endif;?>

        <h2>Recent Rewards</h2>
        <table class="table">
            <thead><th>Reward Points</th><th>Date</th></thead>
        <?php foreach ($recentRewards as $reward): ?>
            <tr><td><?php echo $reward['points']; ?></td><td><?php echo $reward['date']; ?></td></tr>
        <?php endforeach; ?>
        <?php if ($recentRewards == null)
            echo("<tr><td colspan='2'>No rewards found.</td></tr>")
        ?>
        </table>

        <h2>Recent Redemptions</h2>
        <table class="table">
            <thead><th>Reward Name</th><th>Points Used</th></thead>
        <?php foreach ($recentRedemptions as $redemption): ?>
            <tr><td><?php echo $redemption['name']; ?></td><td><?php echo $redemption['points']; ?></td></tr>
        <?php endforeach; ?>
        <?php if ($recentRewards == null)
            echo("<tr><td colspan='2'>No redemptions found.</td></tr>")
        ?>
        </table>

        <h2>Recent Visits</h2>
        <table class="table">
            <thead><th>Date</th><th>Bill Amount</th><th>Feedback</th></thead>
        <?php foreach ($recentVisits as $visit): ?>
           <tr><td><?php echo $visit['date']; ?></td><td><?php echo $visit['bill_amount']; ?></td>
            <?php if (@$visit['rating'] == null){
                if($entity_type== "customer")
                echo("<td><a href='feedbackform.php?visit_id=".$visit['id']."' class='btn btn-primary btn-sm'>Leave Feedback</a></td>");
                else   
                echo("<td>No feedback given.</td>");
            }
                else{
            ?>
        <td><?php echo $visit['rating'];?>/5 <?php echo $visit['comment']; ?></td>
        <?php };
            ?>
           
        </tr>
        <?php endforeach; ?>
        <?php if ($recentRewards == null)
            echo("<tr><td colspan='3'>No visits found.</td></tr>")
        ?>
    </table>

    </div>


<?php
if($entity_type== "employee")

    include 'footer.php'; ?>
