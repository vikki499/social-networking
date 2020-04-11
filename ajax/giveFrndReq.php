<?php

    include_once "../includes/session.php";

    $session = new Session();

    $jsonMessage = array();

    if (!$session->isLoggedIn()) {
        $jsonMessage['status'] = 'Illegal_entry';
        die(json_encode($jsonMessage));
    }

    $id = (string)$_POST['updateId'];
    $sender_id = (string)$_SESSION['id'];

    if ($id === $sender_id) {
        die("same user id");
    }
    
    $mongo_id = new MongoDB\BSON\ObjectID($id);
    $mongo_send_id = new MongoDB\BSON\ObjectID($sender_id);
    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

    // Update on the requested account    
    $bulk = new MongoDB\Driver\BulkWrite;
    $ins = [$sender_id => "gave request"];
    $bulk->update(
        ['_id' => $mongo_id],
        ['$push' => ['friends' => $ins]]
    );
    $result = $manager->executeBulkWrite('outerJoin.USERS', $bulk);

    // Update on the requesters account    
    $bulk = new MongoDB\Driver\BulkWrite;
    $ins = [$id => "waiting for response"];
    $bulk->update(
        ['_id' => $mongo_send_id],
        ['$push' => ['friends' => $ins]]
    );
    //$manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
    $result = $manager->executeBulkWrite('outerJoin.USERS', $bulk);

    // Notify the requested user to respond
    $mongo_date = new DateTime();
    $notify_time = $mongo_date->getTimestamp();
    $rows = db_query(['_id' => $mongo_send_id], ['projection' => ['_id' => 0, 'name' => 1]]);
    $name = '';
    foreach ($rows as $row) {
        $name = $row->name;
        break;
    }
    $message = $name . " gave you friend request!";
    $ins = ['notify_txt' => $message, 'notify_time' => $notify_time, 'seen' => 0];
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['_id' => $mongo_id],
        ['$push' => ['notification' => $ins]]
    );
    //$manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
    $result = $manager->executeBulkWrite('outerJoin.USERS', $bulk);

    echo "Updated successfully";

?>