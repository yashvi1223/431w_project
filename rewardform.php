<?php
require_once 'database.php'; 
require_once 'flashmessages.php';
require_once 'auth.php'; authenticate('employee');


$employee = current_user()['entity'];
$reward = ['id' => '', 'name' => '', 'points' => '', 'added_by' => $employee['id']];
$errors = [];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $rewardData = get_reward($_GET['id']);
    if ($rewardData) {
        $reward = $rewardData;
    } else {
        set_flash_message('Reward not found.', 'danger');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $reward['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $reward['points'] = filter_input(INPUT_POST, 'points', FILTER_VALIDATE_INT);

    if (!$reward['name']) {
        $errors['name'] = 'Name is required.';
    }

    if (!$reward['points']) {
        $errors['points'] = 'Points must be a valid number.';
    }

    if (empty($errors)) {
        try {
            if (!empty($reward['id'])) {
                update_reward($reward);
                set_flash_message('Reward updated successfully!', 'success');
            } else {
                insert_reward($reward);
                set_flash_message('Reward added successfully!', 'success');
            }

            header('Location: rewards.php');
            exit;
        } catch (PDOException $e) {
            $errors['database'] = 'Database error: ' . $e->getMessage();
        }
    }
}
include 'header.php'; 
?>

<div class="container">
    

    
    <?php if (!empty($errors['database'])): ?>
        <div class="alert alert-danger"><?php echo $errors['database']; ?></div>
    <?php endif; ?>

    <form action="rewardform.php<?php echo !empty($reward['id']) ? '?id=' . $reward['id'] : ''; ?>" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Reward Name</label>
            <input type="text" class="form-control <?php echo !empty($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($reward['name']); ?>">
            <?php if (!empty($errors['name'])): ?>
                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="points" class="form-label">Points</label>
            <input type="number" class="form-control <?php echo !empty($errors['points']) ? 'is-invalid' : ''; ?>" id="points" name="points" value="<?php echo htmlspecialchars($reward['points']); ?>">
            <?php if (!empty($errors['points'])): ?>
                <div class="invalid-feedback"><?php echo $errors['points']; ?></div>
            <?php endif; ?>
        </div>

       

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<?php
include 'footer.php'; ?>
