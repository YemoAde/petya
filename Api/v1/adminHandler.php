<?php
//Gets all the userss in the database
$app->get('/viewusers', function() {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT * FROM users WHERE 1");

    $response["status"] = "success";
    $response["users"] = array();

    while ($users = $resp->fetch_assoc()) {
                $tmp = array();
                $tmp["_id"] = $users["_id"];
                $tmp["name"] = $users["name"];
                $tmp["phone"] = $users["phone"];
                $tmp["address"] = $users["address"];
                $tmp["email"] = $users["email"];
                $tmp["type"] = $users["type"];
                $tmp["college"] = $users["college"];
                $tmp["level"] = $users["level"];
                $tmp["role"] = $users["role"];
                $tmp["date_added"] = $users["date_added"];
                $tmp["date_modified"] = $users["date_modified"];
                $tmp["active"] = $users["active"];
                array_push($response["users"], $tmp);
            }
    echoResponse(200, $response);
});

$app->post('/register', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('name', 'phone', 'email', 'address',  'type', 'password'),$r);
    require_once 'passwordHash.php';
    $db = new DbHandler();
    $name = $r->name;
    $phone = $r->phone;
    $email = $r->email;
    $address = $r->address;
    $type = $r->type;
    $password = $r->password;
    $r->active = 1;

    $isUserExists = $db->getOneRecord("select 1 from users where phone='$phone' or email='$email'");
    if(!$isUserExists){
            $r->password = passwordHash::hash($password);
            $table_name = "users";
            $column_names = array('name', 'phone', 'email', 'address',  'type', 'password', 'active');
            $result = $db->insertIntoTable($r, $column_names, $table_name);
            if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "User account created";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create User. Please try again";
            echoResponse(201, $response);
        }
    }else{
        $response["status"] = "error";
        $response["message"] = "User account exists!";
        echoResponse(201, $response);
    }
});
//METHODS IN REST: GET, POST, PUT
$app->put('/editusers/:id', function($id) use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    verifyRequiredParams(array('username', 'password', 'email', 'lastname',  'firstname', 'department', 'college', 'matric', 'role', 'level'),$r);
    require_once 'passwordHash.php';
    $db = new DbHandler();
    $username = $r->username;
    $password = $r->password;
    $email = $r->email;
    $lastname = $r->lastname;
    $firstname = $r->firstname;
    $department = $r->department;
    $college = $r->college;
    $matric = $r->matric;
    $role = $r->role;
    $level = $r->level;

            $r->password = passwordHash::hash($password);
            $table_name = "users";
            $column_names = array('username', 'password', 'email', 'lastname', 'firstname', 'department', 'college', 'matric', 'role', 'level');
            $result = $db->updateTable($r,$table_name,$condition);
            if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Update was success";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to edit users. Please try again";
            echoResponse(201, $response);
        }
});

$app->delete('/deleteusers/:id', function($id) use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    $db = new DbHandler();
            $table_name = "users";
            $result = $db->deleteTable($table_name,$condition);
            if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Delete was success";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to delete users. Please try again";
            echoResponse(201, $response);
        }
});

 ?>
