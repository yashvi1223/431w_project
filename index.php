<?php
require("header.php");
require_once("auth.php");
authenticate();
$current_entity= current_user()['entity'];
$entity_type = current_user()['user']['entity_type'];

?>
<div class="container">
    <h2>Welcome,<?php echo $current_entity['name'] ?></h2>

</div>
<?php if($entity_type == "employee"): ?>
    <form action="customers.php" method="get" class="mb-4 mt-2">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search customers..." >
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
    <?php else:?>
        <?php include "customer.php"?>
    <?php endif?>
<?php
require("footer.php");
?>
