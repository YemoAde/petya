<?php

$app->get('/viewResource', function(){
        $db = new DbHandler();
        $response = array();
        $resp = $db->getAllrecords("SELECT * from pref_resource WHERE 1");
        
        $response["status"] = "success";
        $response["pref_resource"] = array();
        while($pref_resource = $resp->fetch_assoc()) {
            $tmp = array();
            $tmp["name"] = $pref_resource["name"];
            $tmp["type"] = $pref_resource["type"];
            $tmp["content"] = $pref_resource["content"];
            $tmp["extras"] = $pref_resource["extras"];
            
            array_push($response["pref_resource"], $tmp);
        }
        echoResponse(200, $response);
    });


$app->post('/addResource', function() use ($app){
        $response = array();
        $r = json_decode($app->request->getBody());
        verifyRequiredParams(array('name', 'type', 'content'),$r);
        $db = new DbHandler();
        $name = $r->name;
        $type = $r->type;
        $extras = $r->extras;
        $content = $r->content;
           
        $isPrefExist = $db->getOneRecord("SELECT 1 from pref_resource WHERE name='$name' or type='$type' or content='$content'");
            if(!$isPrefExist){
                $table_name = "pref_resource";
                $coloum_name = array('name', 'type', 'content', 'extras');
                $result = $db->InsertIntoTable($r, $coloum_name, $table_name);
                if($result != null){
                    $response["status"] = "success";
                    $response["message"] = "Resource added succesful"; 
                    echoResponse(200, $response);
                }
                else{
                    $response["status"] = "error";
                    $response["message"] = "Resource exists";
                    echoResponse(201, $response);
                }}
                else{
                    $response["status"] = "error";
                    $response["message"] = "Error trying to creat resource";
                    echoResponse(202, $response);
                }
});

$app->put('/editResource/:id', function($id) use ($app){
        $response = array();
        $r = json_decode($app->request->getBody());
        $condition = array('_id'=>$id);
        verifyRequiredParams(array('name', 'type', 'content', 'extras'),$r);
        $db = new DbHandler();
        $name = $r->name;
        $type = $r->type;
        $content = $r->content;
        $extras = $r->extras;
        
        $table_name = "pref_resource";
        $coloum_name = array('name', 'type', 'content', 'extras');
        $result = $db->UpdateTable($r, $table_name, $condition);
        if($result != null){
            $response["status"] = "success";
            $response["message"] = "Resource update succesful";
            echoResponse(200, $response);
        }
        else{
            $response["status"] = "error";
            $response["message"] = "Error occured with updates";
        }
        
    });


$app->delete('/deleteResource/:id', function($id) use ($app){
        $response = array();
        $r = json_decode($app->request->getBody());
        $condition = array('_id'=>$id);
        
        $db = new DbHandler();
        $table_name = "pref_resource";
        $result = $db->deleteTable($table_name, $condition);
        if($result != null){
            $response["status"] = "success";
            $response["message"] = "Delete successful";
            echoResponse(200, $response);
        }
        else{
            $response["status"] = "error";
            $response["message"] = "Error occured";
        }
    });
?>