<?php
$app->get('/session', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["_id"] = $session['_id'];
    $response["username"] = $session['username'];
    $response["email"] = $session['email'];
    $response["firstname"] = $session['firstname'];
    $response["lastname"] = $session['lastname'];
    $response["createdAt"] = $session['createdAt'];
    echoResponse(200, $session);
});

$app->post('/login', function() use ($app) {
    require_once 'passwordHash.php';
    $r = json_decode($app->request->getBody());
    require_once 'passwordHash.php';
    verifyRequiredParams(array('email', 'password'),$r);
    $response = array();
    $db = new DbHandler();
    $password = $r->password;
    $email = $r->email;
    $user = $db->getOneRecord("select _id, name, email,phone, address, type,password,active, date_added from users where email='$email'");
    if ($user != NULL) {
        if(passwordHash::check_password($user['password'],$password)){
          if($user['active'] == 1){
            $response['status'] = "success";
            $response['message'] = 'Login was successful';

            $response['user'] = array();

            $tmp= array();
            $tmp['_id'] = $user['_id'];
            $tmp['name'] = $user['name'];
            $tmp['email'] = $user['email'];
            $tmp['phone'] = $user['phone'];
            $tmp['type'] = $user['type'];
            $tmp['date_added'] = $user['date_added'];
            array_push($response["user"], $tmp);


            echoResponse(200, $response);
          }else{
            $response['status'] = "error";
            $response['message'] = 'Login failed. Your are not authorized to access';
            echoResponse(201, $response);
          }
            }
         else {
            $response['status'] = "error";
            $response['message'] = 'Login failed. Incorrect credentials';
            echoResponse(201, $response);
        }
    }else {
            $response['status'] = "error";
            $response['message'] = 'No such User is registered';
            echoResponse(201, $response);
        }

});

$app->post('/forgotPass', function() use ($app) {
    require_once 'passwordHash.php';
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email'),$r);
    $response = array();
    $db = new DbHandler();
    $email = $r->email;
    $user = $db->getOneRecord("select _id, username, email from admin where email='$email' or username='$email'");
    if ($user != NULL && sendResetMail($user['username'],$user['email'])) {
        $response['status'] = "success";
        $response['message'] = 'Password Reset Code has been sent to your email';
    }else {
            $response['status'] = "error";
            $response['message'] = 'Email address does not exist';
        }
    echoResponse(200, $response);
});

//CREATE ACCOUNT NOT COMPLETED YET
// $app->post('/createAcct', function() use ($app) {
//     $response = array();
//     $r = json_decode($app->request->getBody());





$app->get('/logout', function() {
    $db = new DbHandler();
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    echoResponse(200, $response);
});
?>
