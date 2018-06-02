<?php

$app->get('/viewUser', function() {
    $db = new dbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT * FROM users");
    
    $response["status"] = "success";
    $response["users"] = array();
    
    while($user = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $user["_id"];
        $tmp["username"] = $user["username"];
        $tmp["lastname"] = $user["lastname"];
        $tmp["other_names"] = $user["other_names"];
        $tmp["role"] = $user["role"];
        $tmp["active"] = $user["active"];
        $tmp["password"] = $user["password"];
        $tmp["email"] = $user["email"];
        $tmp["phone"] = $user["phone"];
        $tmp["last_activity"] = $user["last_activity"];
        $tmp["date_added"] = $user["date_added"];
        $tmp["date_modified"] = $user["date_modified"];
        array_push($response["users"], $tmp);
    }
    echoResponse(200, $response);
});

$app->post('/addUser', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('username','lastname', 'other_names', 'role', 'password', 'email', 'phone'),$r);
    $db = new DbHandler();
    $r->active = 1;
    $username = $r->username;
    $lastname = $r->lastname;
    $other_names = $r->other_names;
    $role = $r->role;
    $active = 1;
    $password = $r->password;
    $email = $r->email;
    $phone = $r->phone;

    $isUserExists = $db->getoneRecord("SELECT 1 from users where username='$username' or email='$email'");
    if(!$isUserExists){
        $table_name = "users";
        $coloum_name = array('username','lastname', 'other_names', 'role', 'active', 'password', 'email', 'phone');
        $result = $db->insertIntoTable($r, $coloum_name, $table_name);
        if($result != null){
            $response["status"] = "success";
            $response["message"] = "User Added";
            echoResponse(200, $response);
        }
        else{
            $response["status"] = "error";
            $response["message"] = "Error Trying To Add User";
            echoResponse(201, $response);
        }}
        else {
            $response["status"] = "error";
            $response["message"] = "User Already Exist";
            echoResponse(201, $response);
        }
});
$app->post('/changePass', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('username','oldPass', 'newPass', 'conPass'),$r);
    $db = new DbHandler();
    $r->active = 1;
    $username = $r->username;
    $oldPass = $r->oldPass;
    $new = $r->newPass;

    $isUserExists = $db->getoneRecord("SELECT 1 from users where username='$username' and password='$oldPass'");
    if($isUserExists){
        $table_name = "users";
        $coloum_name = array('username','lastname', 'other_names', 'role', 'active', 'password', 'email', 'phone');
        $result = $db->forQue("UPDATE users SET password = '$new' WHERE username= '$username'");
        if($result != null){
            $response["status"] = "success";
            $response["message"] = "Done";
            echoResponse(200, $response);
        }
        else{
            $response["status"] = "error";
            $response["message"] = "Error";
            echoResponse(201, $response);
        }}
        else {
            $response["status"] = "error";
            $response["message"] = "Please Check Details";
            echoResponse(201, $response);
        }
});

$app->post('/editUser/:id', function($id) use($app){
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    verifyRequiredParams(array('lastname', 'other_names','email', 'phone'),$r);
    $db = new DbHandler();
    
    $lastname = $r->lastname;
    $other_names = $r->other_names;
    $email = $r->email;
    $phone = $r->phone;

    $table_name = "users";
    $coloum_name = array('lastname', 'other_names','email', 'phone');
    $result = $db->updateTable($r, $table_name, $condition);
    if($result != null){
        $response["status"] = "success";
        $response["message"] = "Update successfull";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Update Failed";
        echoResponse(201, $response);
    }
});

$app->get('/deleteUser/:email', function($email) use($app){
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('email'=>$email);

    $db = new dbHandler();
    $table_name = "users";
    $result = $db->deleteTable($table_name, $condition);
    if($result != null){
        $response["status"] = "success";
        $response["message"] = "Delete Successfull";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Delete Unsuccessfull";
        echoResponse(201, $response);
    }
});
?>