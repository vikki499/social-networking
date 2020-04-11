<?php

    include_once "../includes/session.php";

    $session = new Session();

    $jsonMessage = array();

    if (!$session->isLoggedIn()) {
        $jsonMessage['status'] = 'Illegal_entry';
        die(json_encode($jsonMessage));
    }

    $id = (string)$_SESSION['id'];
    $accept = (string)$_POST['accept_id'];

    if ($id === $accept) {
        die("same user id");
    }

    $mongo_id = new MongoDB\BSON\ObjectID($id);
    $mongo_accept_id = new MongoDB\BSON\ObjectID($accept);

    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['_id' => $mongo_id, 'friends.' . $accept => 'friends'],
        ['$unset' => ['friends.$' => ""]],
        ['multi' => true, 'upsert' => false]
    );
    $result = $manager->executeBulkWrite('outerJoin.USERS', $bulk);

    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['_id' => $mongo_accept_id, 'friends.' . $id => 'friends'],
        ['$unset' => ['friends.$' => ""]],
        ['multi' => true, 'upsert' => false]
    );
    $result = $manager->executeBulkWrite('outerJoin.USERS', $bulk);

    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['_id' => $mongo_id],
        ['$pull' => ['friends' => NULL]]
    );
    $result = $manager->executeBulkWrite('outerJoin.USERS', $bulk);

    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['_id' => $mongo_accept_id],
        ['$pull' => ['friends' => NULL]]
    );
    $result = $manager->executeBulkWrite('outerJoin.USERS', $bulk);
    
    echo "updation done";

?>