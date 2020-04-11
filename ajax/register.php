<?php

    include_once "../includes/session.php";

    $session = new Session();

    $jsonMessage = array();

    if ($session->isLoggedIn()) {
		$jsonMessage['status'] = 'Error';
		$jsonMessage['data'] = 'Session_Exists';
        die(json_encode($jsonMessage));
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $passwd = $_POST['password'];
    $confPasswd = $_POST['confPassword'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];

    if ($passwd !== $confPasswd) {
        echo "Password doesn't match";
    }

    $hashedPasswd = sha1($passwd);

    $q1 = $_POST['question1'];
    $a1 = $_POST['answer1'];
    $q2 = $_POST['question2'];
    $a2 = $_POST['answer2'];

    $count = db_count(['email' => $email]);

    if ($count > 0) {
		$jsonMessage['status'] = 'Error';
		$jsonMessage['data'] = 'Email id already registered!';
        die(json_encode($jsonMessage));
    }

	try {
		
		$mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");
		
		$bulk = new MongoDB\Driver\BulkWrite;
		date_default_timezone_set('Asia/Kolkata');
		
		$mongo_date = new DateTime($birthday);
		$birth_date = $mongo_date->getTimestamp();	

		$mongo_date = new DateTime();
		$reg_date = $mongo_date->getTimestamp();

		$mongo_date = new DateTime();
		$notify_time = $mongo_date->getTimestamp();

		$secrets = 	[
						"question_1" => $q1, 
						"answer_1" => $a1, 
						"question_2" => $q2, 
						"answer_2" => $a2
					];

		$doc = 	[
					'_id' => new MongoDB\BSON\ObjectID, 
					'name' => $name , 
					'email' => $email, 
					'password' => $hashedPasswd, 
					'gender' => $gender, 
					'birthday' => $birth_date, 
					"registered" => $reg_date, 
					"secret_quote" => $secrets,
					"user_cover_pic" => [], 
					"user_profile_pic" => [], 
					"user_info" => [],
					"notification" => 	[
											[
												"notify_txt" => "Welcome to outer join!", 
												"notify_time" => $notify_time,
												"seen" => 0
											]
										],
					"user_status" => 1,
					"user_post" => 	[],				
					"friends" => 	[],
					
					"warning" => 	[],
									
					"group_chat" => []

				];
		
		$bulk->insert($doc);
		$mng->executeBulkWrite('outerJoin.USERS', $bulk);
		$jsonMessage['status'] = 'Success';
		$jsonMessage['data'] = 'User added successfully';
		$session->login($email, $passwd);
        die(json_encode($jsonMessage));
			
	} catch (MongoDB\Driver\Exception\Exception $e) {

		$filename = basename(__FILE__);
		
		$errorTxt =  "The " . $filename . "script has experienced an error.\n" . "It failed with the following exception:\n" . "Exception:" . $e->getMessage() . "\n" . "In file:" . $e->getFile() . "\n" . "On line:" . $e->getLine() . "\n";    

		$jsonMessage['status'] = 'Error';
		$jsonMessage['data'] = $errorTxt;
        die(json_encode($jsonMessage));
	}

?>