<?php

require_once 'constants.php';

function get_connection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=' . DB_PORT;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    } catch (PDOException $e) {
        die('Database Connection Failed: ' . $e->getMessage());
    }
}
/**
 * Get employees from the database
 * 
 */
function get_employees($page = 1, $query = '') {
    $pdo = get_connection(); 
        $itemsPerPage = 10; 
    $offset = ($page - 1) * $itemsPerPage;

        $countSql = "SELECT COUNT(*) FROM employees WHERE inactive<>true and (name LIKE :query OR email LIKE :query)";
    $countStmt = $pdo->prepare($countSql);
    $searchQuery = '%' . $query . '%';
    $countStmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
    $countStmt->execute();
    $totalEmployees = $countStmt->fetchColumn();
    $totalPages = ceil($totalEmployees / $itemsPerPage);

        $sql = "SELECT * FROM employees WHERE inactive<>true and (name LIKE :query OR email LIKE :query) LIMIT :offset, :itemsPerPage";
    $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

    $stmt->execute();

        //return ['employees' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'totalPages' => $totalPages];
    return [$stmt->fetchAll(PDO::FETCH_ASSOC), $totalPages];
}

function get_customers($page = 1, $query = '') {
    $pdo = get_connection();

    $itemsPerPage = 10;
    $offset = ($page - 1) * $itemsPerPage;

        $countSql = "SELECT COUNT(*) FROM customers WHERE inactive<>true and (name LIKE :query OR email LIKE :query)";
    $countStmt = $pdo->prepare($countSql);
    $searchQuery = '%' . $query . '%';
    $countStmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
    $countStmt->execute();
    $totalCustomers = $countStmt->fetchColumn();
    $totalPages = ceil($totalCustomers / $itemsPerPage);

        $sql = "SELECT * FROM customers WHERE inactive<>true and (name LIKE :query OR email LIKE :query) LIMIT :offset, :itemsPerPage";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    return [ $stmt->fetchAll(PDO::FETCH_ASSOC),  $totalPages];
}

function get_rewards($page = 1, $query = '') {
    $pdo = get_connection();

    $itemsPerPage = 9;
    $offset = ($page - 1) * $itemsPerPage;

        $countSql = "SELECT COUNT(*) FROM rewards WHERE inactive<>true and name LIKE :query";
    $countStmt = $pdo->prepare($countSql);
    $searchQuery = '%' . $query . '%';
    $countStmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
    $countStmt->execute();
    $totalRewards = $countStmt->fetchColumn();
    $totalPages = ceil($totalRewards / $itemsPerPage);

        $sql = "SELECT * FROM rewards WHERE inactive<>true and name LIKE :query LIMIT :offset, :itemsPerPage";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    return [ $stmt->fetchAll(PDO::FETCH_ASSOC),  $totalPages];
}

function get_visits($page = 1) {
    $pdo = get_connection();

    $itemsPerPage = 10;
    $offset = ($page - 1) * $itemsPerPage;

        $countSql = "SELECT COUNT(*) FROM visits ";
    $countStmt = $pdo->prepare($countSql);
    $searchQuery = '%' . $query . '%';
    $countStmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
    $countStmt->execute();
    $totalVisits = $countStmt->fetchColumn();
    $totalPages = ceil($totalVisits / $itemsPerPage);

        $sql = "SELECT * FROM visits LIMIT :offset, :itemsPerPage";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    return [$stmt->fetchAll(PDO::FETCH_ASSOC),  $totalPages];
}

function insert_customer($data) {
    $pdo = get_connection();
    try {
        $pdo->beginTransaction();         
                $customerSql = "INSERT INTO customers (name, address, email, phone) VALUES (:name, :address, :email, :phone)";
        $customerStmt = $pdo->prepare($customerSql);
        $customerStmt->execute([
            'name' => $data['name'],
            'address' => $data['address'],
            'email' => $data['email'],
            'phone' => $data['phone']
        ]);
        $customerId = $pdo->lastInsertId();

                $userData = [
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),             'entity_type' => 'customer',
            'entity_id' => $customerId
        ];

                $userSql = "INSERT INTO users (email, password, entity_type, entity_id) VALUES (:email, :password, :entity_type, :entity_id)";
        $userStmt = $pdo->prepare($userSql);
        $userStmt->execute($userData);

        $pdo->commit();         return $customerId;
    } catch (PDOException $e) {
        $pdo->rollBack();         throw $e;     }
}

function update_customer($data) {
    $pdo = get_connection();
    unset($data['password']);
    unset($data['inactive']);

    $sql = "UPDATE customers SET name = :name, address = :address, email = :email, phone = :phone  WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    return $stmt->rowCount();
}

function insert_employee($data) {
    $pdo = get_connection();
    try {
        $pdo->beginTransaction(); 
                $employeeSql = "INSERT INTO employees (name, email, password, created_by) VALUES (:name, :email, :password, :created_by)";
        $employeeStmt = $pdo->prepare($employeeSql);
        $employeeStmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),             'created_by' => $data['created_by']
        ]);
        $employeeId = $pdo->lastInsertId();

                $userData = [
            'email' => $data['email'],
            'password' => $data['password'],             'entity_type' => 'employee',
            'entity_id' => $employeeId
        ];

                $userSql = "INSERT INTO users (email, password, entity_type, entity_id) VALUES (:email, :password, :entity_type, :entity_id)";
        $userStmt = $pdo->prepare($userSql);
        $userStmt->execute($userData);

        $pdo->commit();         return $employeeId;
    } catch (PDOException $e) {
        $pdo->rollBack();         throw $e;     }
}

function update_employee($data) {
    unset($data['password']);
    unset($data['inactive']);
    unset($data['created_by']);
    $pdo = get_connection();
    $sql = "UPDATE employees SET name = :name, email = :email, is_manager = :is_manager WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    return $stmt->rowCount();
}

function insert_reward($data) {
    $pdo = get_connection();
    unset($data['id']);
    $sql = "INSERT INTO rewards (name, points, added_by) VALUES (:name, :points, :added_by)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    return $pdo->lastInsertId();
}

function update_reward($data) {
    $pdo = get_connection();
    $sql = "UPDATE rewards SET name = :name, points = :points, added_by = :added_by WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    return $stmt->rowCount();
}

function insert_visit($data) {
    $pdo = get_connection();
    try{
    $pdo->beginTransaction();      unset($data['id']);
    $sql = "INSERT INTO visits (customer_id, employee_id, bill_amount, date) VALUES (:customer_id, :employee_id, :bill_amount, :date)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
   
    $visitId = $pdo->lastInsertId();

    $rewardPoints = $data['bill_amount'] * BILL_REWARD_RATIO;
    $rewardSql = "INSERT INTO reward_points (visit_id, points, customer_id, added_by) VALUES (:visit_id, :points, :customer_id, :added_by)";
    $rewardStmt = $pdo->prepare($rewardSql);
    $rewardStmt->execute(['visit_id' => $visitId, 'points' => $rewardPoints, 'customer_id' => $data['customer_id'], 'added_by'=>$data['employee_id']]);
    $pdo->commit();
   
    return $visitId;
}catch(Exception $e){
    $pdo->rollback();
    throw $e;
}
}

function update_visit($data) {
    $pdo = get_connection();
    $sql = "UPDATE visits SET customer_id = :customer_id, employee_id = :employee_id, bill_amount = :bill_amount WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    return $stmt->rowCount();
}

function get_customer($id) {
    try {
        $pdo = get_connection();
        $sql = "SELECT * FROM customers WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);     } catch (PDOException $e) {
        die('Error fetching customer: ' . $e->getMessage());
    }
}

function get_employee($id) {
    try {
        $pdo = get_connection();
        $sql = "SELECT * FROM employees WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);     } catch (PDOException $e) {
        die('Error fetching employee: ' . $e->getMessage());
    }
}

function get_reward($id) {
    try {
        $pdo = get_connection();
        $sql = "SELECT * FROM rewards WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);     } catch (PDOException $e) {
        die('Error fetching reward: ' . $e->getMessage());
    }
}
function get_customer_points($customerId) {
        $pdo = get_connection();

                $earnedSql = "SELECT SUM(points) FROM reward_points WHERE customer_id = :customer_id";
        $earnedStmt = $pdo->prepare($earnedSql);
        $earnedStmt->execute(['customer_id' => $customerId]);
        $pointsEarned = $earnedStmt->fetchColumn();

                $usedSql = "SELECT SUM(points) FROM redemptions WHERE customer_id = :customer_id";
        $usedStmt = $pdo->prepare($usedSql);
        $usedStmt->execute(['customer_id' => $customerId]);
        $pointsUsed = $usedStmt->fetchColumn();

                $netPoints = (int)$pointsEarned - (int)$pointsUsed;
        return $netPoints;
    
}

function redeem_reward($rewardId, $customerId, $employeeId) {
    $pdo = get_connection();

    try {
                $pdo->beginTransaction();

                $rewardSql = "SELECT points FROM rewards WHERE id = :reward_id";
        $rewardStmt = $pdo->prepare($rewardSql);
        $rewardStmt->execute(['reward_id' => $rewardId]);
        $reward = $rewardStmt->fetch(PDO::FETCH_ASSOC);

        if (!$reward) {
            throw new Exception("Reward not found.");
        }

                $currentPoints = get_customer_points($customerId);
        if ($currentPoints < $reward['points']) {
            throw new Exception("Insufficient points to redeem reward.");
        }

                $redemptionSql = "INSERT INTO redemptions (name, points, reward_id, customer_id) VALUES ('Redemption', :points, :reward_id, :customer_id)";
        $redemptionStmt = $pdo->prepare($redemptionSql);
        $redemptionStmt->execute(['points' => $reward['points'], 'reward_id' => $rewardId, 'customer_id' => $customerId]);

                $pdo->commit();
    } catch (Exception $e) {
                $pdo->rollBack();
        throw $e;
    }
}

function get_recent_rewards($customerId, $limit = 5) {
    $pdo = get_connection();

    $sql = "SELECT rp.points, rp.visit_id, v.bill_amount, v.employee_id, v.customer_id, v.date 
            FROM reward_points rp
            INNER JOIN visits v ON rp.visit_id = v.id
            WHERE rp.customer_id = :customer_id
            ORDER BY v.date DESC
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_recent_redemptions($customerId, $limit = 5) {
    $pdo = get_connection();

    $sql = "SELECT r.name, rd.points, rd.customer_id
            FROM redemptions rd
            INNER JOIN rewards r ON rd.reward_id = r.id
            WHERE rd.customer_id = :customer_id
            ORDER BY r.id DESC
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_recent_visits($customerId, $limit= 5){
    $pdo = get_connection();

    $sql = "SELECT visits.*, feedback.comment, feedback.rating
            FROM visits
            LEFT OUTER JOIN feedback on visits.id = feedback.visit_id
            WHERE customer_id = :customer_id
            ORDER BY date desc
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_visit($visitId) {
    $pdo = get_connection();

    $sql = "SELECT * FROM visits WHERE id = :visit_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':visit_id', $visitId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function insert_feedback($data) {
    $pdo = get_connection();

    $sql = "INSERT INTO feedback (comment, rating, visit_id) VALUES (:comment, :rating, :visit_id)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':comment', $data['comment'], PDO::PARAM_STR);
    $stmt->bindParam(':rating', $data['rating'], PDO::PARAM_INT);
    $stmt->bindParam(':visit_id', $data['visit_id'], PDO::PARAM_INT);

    return $stmt->execute();
}

?>
