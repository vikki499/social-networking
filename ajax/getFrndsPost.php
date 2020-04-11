<?php

    include_once "../includes/session.php";

    $session = new Session();

    $jsonMessage = array();

    if (!$session->isLoggedIn()) {
        $jsonMessage['status'] = 'Illegal_entry';
        die(json_encode($jsonMessage));
    }

    $current_user = $_SESSION['id'];
    $mongo_id = new MongoDB\BSON\ObjectID($current_user);

    $option = ["projection" => ['_id' => 0, 'friends' => 1]];
        $rows = db_query(['_id' => $mongo_id], $option);

        $printer = array();
        foreach ($rows as $row) {
            array_push($printer, $row->friends[0]);
        }

        $final_posts = array();

        while ($fruit_name = current($printer[0])) {
            if ($fruit_name == 'friends') {
                $friend = key($printer[0]);
                $mongo_friend_id = new MongoDB\BSON\ObjectID($friend);
                $option = ["projection" => ['_id' => 1, 
                            "name" => 1, 'user_post' => 1]];
                $rows = db_query(['_id' => $mongo_friend_id], $option);

                $post_printer = array();
                foreach ($rows as $row) {
                    array_push($post_printer, $row);
                }
                array_push($final_posts, $post_printer[0]);
            }
            next($printer[0]);
        }

        die(json_encode($final_posts));

?>