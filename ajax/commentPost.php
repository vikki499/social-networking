<?php

    include_once "../includes/session.php";

    $session = new Session();

    $jsonMessage = array();

    if (!$session->isLoggedIn()) {
        $jsonMessage = 'Illegal_entry';
        die($jsonMessage);
    }

    $idToUpdate = $_POST['updateId'];
    $postIndex = (string)$_POST['postIndex'];
    $comment = $_POST['comment'];
    
    $id = (string)$_SESSION['id'];
    $mongo_session_id = new MongoDB\BSON\ObjectID($id);

    $rows = db_query(['_id' => $mongo_session_id], ['projection' => ['_id' => 0, 'name' => 1]]);
    $name = '';
    foreach ($rows as $row) {
        $name = $row->name;
        break;
    }

    $mongo_id = new MongoDB\BSON\ObjectID($idToUpdate);
    $mongo_date = new DateTime();
    $notify_time = $mongo_date->getTimestamp();
    $ins = ['commenter_name' => $name,
            'commenter' => $id,
            'comment_txt' => $comment,
            'time' => $notify_time];
    
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['_id' => $mongo_id],
        ['$push' => ['user_post.' . $postIndex . '.comments' => $ins]]
    );
    
    //echo 'user_post.' . $postIndex . 'likes';
    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
    $result = $manager->executeBulkWrite('outerJoin.USERS', $bulk);

    
    echo "Updated";

?>