<?php

    include_once "../includes/session.php";

    $session = new Session();

    $jsonMessage = array();

    if (!$session->isLoggedIn()) {
        $jsonMessage['status'] = 'Illegal_entry';
        die(json_encode($jsonMessage));
    }

    $school = $_POST['school'];
    $college = $_POST['college'];
    $relation = $_POST['relation'];
    $mobile = $_POST['mobile'];
    $mob_visi = $_POST['mob_visi'];
    $website = $_POST['website'];
    $connector_id = $_POST['connector_id'];
    $home_town = $_POST['home_town'];
    $current_city = $_POST['current_city'];
    $job = $_POST['job'];

    $toInsert = [
        'school' => $school,
        'college' => $college,
        'relationship_status' => $relation,
        'mobile' => $mobile,
        'mobile_visibility' => $mob_visi,
        'website' => $website,
        'connector_id' => $connector_id,
        'home_town' => $home_town,
        'current_city' => $current_city,
        'job' => $job
    ];

    $bulk = new MongoDB\Driver\BulkWrite;
    $mongo_id = new MongoDB\BSON\ObjectID($_SESSION['id']);
    $bulk->update(
        ['_id' => $mongo_id],
        ['$set' => ['user_info' => $toInsert]],
        ['multi' => false, 'upsert' => true]
    );

    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
    $result = $manager->executeBulkWrite('outerJoin.USERS', $bulk);
    echo "Updated successfully";

?>