<?php

require_once 'dbHandler.php';
require_once 'passwordHash.php';
require '.././libs/Slim/Slim.php';


\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;

require_once 'authentication.php';
require_once 'adminHandler.php';
require_once 'bookHandler.php';
require_once 'userHandler.php';
require_once 'prefHandler.php';
require_once 'productHandler.php';
require_once 'orderHandler.php';

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields,$request_params) {
    $error = false;
    $error_fields = "";
    foreach ($required_fields as $field) {
        if (!isset($request_params->$field) || strlen(trim($request_params->$field)) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["status"] = "error";
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(200, $response);
        $app->stop();
    }
}


function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

function sendResetMail($email,$username){

    //Variable
    $sender = "Dove <support@replug.net>";
    $to_email = $email;
    $subject = "Password Reset";
    //build message body
    $msg .= "Hello ".$username.",\n";
    $msg .="You requested for a password reset\n";
    $msg .="Please use this code: ".$code. " to reset your password.\n\n";
    $msg .="Kind Regards\n";
    $msg .="Replug!";

    //headers
    $headers = 'From: '.$sender.'' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    //sending block
    $send_mail = mail($to_email, $subject, $msg, $headers);

    if($send_mail){
      return true;
    }else {
      return false;
    }

}

$app->run();
?>
