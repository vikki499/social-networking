<?php

    include_once "../includes/session.php";

    $session = new Session();

    $jsonMessage = array();

    if (!$session->isLoggedIn()) {
        $jsonMessage['status'] = 'Illegal_entry';
        die(json_encode($jsonMessage));
    }

    
    $mongo_id = new MongoDB\BSON\ObjectID($_SESSION['id']);
    
    $count = db_count(['_id' => $mongo_id, 'notification.seen' => 0]);
    echo $count;

    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

    while ($count > 0) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['_id' => $mongo_id, 'notification.seen' => 0],
            ['$set' => ['notification.$.seen' => 1]],
            ['multi' => true, 'upsert' => false]
        );
        $result = $manager->executeBulkWrite('outerJoin.USERS', $bulk);
        $count = db_count(['_id' => $mongo_id, 'notification.seen' => 0]);
    }
    echo $mongo_id;

?>