<?php @session_start();?>
<?php
require_once("flashmessages.php");
require_once("auth.php");
$flashMessage = get_flash_message();
function get_active_link($link) {
    $currentFile = basename($_SERVER['SCRIPT_NAME']);

    if (strpos($currentFile, $link) !== false) {
        return 'active';
    } else {
        return '';
    }
}
authenticate('any');
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Reward Management</title>

    <link href="cosmo.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Restaurant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                   <?php if($user['user']['entity_type'] == 'employee') : ?>
                    <?php if($user['entity']['is_manager']): ?>
                    <li class="nav-item ">
                        <a class="nav-link <?php echo get_active_link('employee')?>" href="employees.php">Employees</a>
                    </li>
                    <?php endif?>
                    <li class="nav-item">
                        <a class="nav-link  <?php echo get_active_link('reward')?>" href="rewards.php">Rewards</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  <?php echo get_active_link('customer')?>" href="customers.php">Customers</a>
                    </li>
                    <?php endif;?>
               
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $user['entity']['name']?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                           
                        </ul>
                    </li>

          </ul>

                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
    
        <?php if ($flashMessage): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flashMessage['type']); ?>">
                <?php echo htmlspecialchars($flashMessage['message']); ?>
            </div>
        <?php endif; ?>

    </div>

