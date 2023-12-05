<?php

require_once 'database.php'; 
require_once 'flashmessages.php'; 

@session_start();

function login($email, $password, $entityType) {
    $pdo = get_connection();

   
                session_unset();

                $sql = "SELECT users.* FROM users inner join ". $entityType."s entity on entity.id = users.entity_id WHERE users.email = :email AND users.entity_type = :entity_type and entity.inactive <> true";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email, 'entity_type' => $entityType]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password == $user['password']) {
                        $_SESSION['user'] = $user;
            return true;
        }
        return false;
    
}


function logout() {
    session_unset();
    session_destroy();
}

function current_user() {
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        $pdo = get_connection();

                $entityTable = $user['entity_type'] === 'customer' ? 'customers' : 'employees';
        $sql = "SELECT * FROM {$entityTable} WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $user['entity_id']]);
        $entity = $stmt->fetch(PDO::FETCH_ASSOC);

        return ['user' => $user, 'entity' => $entity];
    }
    return null;
}

function authenticate($entityType='any') {
    if (!isset($_SESSION['user'])) {
                header('Location: login.php');
        exit;
    }

        $currentUserDetails = current_user();
    if (!$currentUserDetails) {
                header('Location: login.php');
        exit;
    }
    //write code to check if entityi is inactive
    $entity = $currentUserDetails['entity'];
    if($entity['inactive']){
        set_flash_message("Your account was deactivated. Contact a manager to reactivate acfount", 'danger');
        session_destroy();
        header('Location: index.php');
        exit;
    }

    $currentUser = $currentUserDetails['user'];
    $entity = $currentUserDetails['entity'];

    if ($entityType === 'any') {
                return;
    }

    if ($entityType === 'employee' && ($currentUser['entity_type'] === 'employee' )) {
                return;
    }

    if ($entityType === 'manager' && ($currentUser['entity_type'] === 'employee' && ($entity['is_manager']))) {
                return;
    }

    if ($entityType === 'customer' && $currentUser['entity_type'] === 'customer') {
                return;
    }

        set_flash_message("You're not authorized to perform that action.", 'danger');
    header('Location: index.php');
    exit;
}

?>