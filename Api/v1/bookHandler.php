<?php
$app->get('/viewBook', function() {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT * FROM books WHERE 1");

    $response["status"] = "success";
    $response["books"] = array();

    while ($book = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["title"] = $book["title"];
        $tmp["author"] = $book["author"];
        $tmp["quantity"] = $book["quantity"];
        $tmp["publisher"] = $book["publisher"];
        $tmp["isbn"] = $book["isbn"];
        $tmp["ws_no"] = $book["ws_no"];
        $tmp["added_by"] =$book["added_by"];
        $tmp["date_created"] = $book["date_created"];
        $tmp["date_modified"] = $book["date_modified"];
        array_push($response["books"], $tmp);
    }
    echoResponse(200, $response);
});

$app->post('/addBook', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('title', 'author', 'quantity', 'publisher', 'isbn', 'ws_no', 'added_by', 'ip_address'),$r);
    $db = new DbHandler();
    $title = $r->title;
    $author = $r->author;
    $quantity = $r->quantity;
    $publisher = $r->publisher;
    $isbn = $r->isbn;
    $ws_no = $r->ws_no;
    $added_by =$r->added_by;
    $ip_address = $r->ip_address;
    
    $isBookExist = $db->getOneRecord("SELECT 1 from books WHERE title='$title' or isbn='$isbn' or ws_no='$ws_no'");
    if(!$isBookExist){
        $table_name = "books";
        $coloum_names = array('title',  'author', 'quantity', 'publisher', 'isbn', 'ws_no', 'ip_address');
        $result = $db->InsertIntoTable($r, $coloum_names, $table_name);
        if($result != null){
            $response["status"] = "success";
            $response["message"] = "Book Added";
            echoResponse(200, $response);
        }
        else {
            $response["status"] = "error";
            $respnse["message"] = "Error Trying to Add Book TryAgain";
            echoResponse(201, $response);
        }}
        else{
            $response["status"] = "error";
            $response["message"] = "Book Exist Already";
            echoResponse(201, $response);        
        }
    });

// This specific function is to Edit the Book

$app->put('/editBook/:id', function($id) use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    verifyRequiredParams(array('title', 'author', 'quantity', 'publisher', 'isbn', 'ws_no', 'added_by', 'ip_address'),$r);
    $db = new DbHandler();
    $title = $r->title;
    $author = $r->author;
    $quantity = $r->quantity;
    $publisher = $r->publisher;
    $isbn = $r->isbn; 
    $ws_no = $r->ws_no;
    $added_by = $r->added_by;
    $ip_address = $r->ip_address;
    
    $table_name ="books";
    $coloum_names = array('title', 'author', 'quantity', 'publisher', 'isbn', 'ws_no', 'added_by', 'ip_address');
    $result = $db->UpdateTable($r, $coloum_names, $table_name);
    if($result != null) {
        $response["status"] = "Success";
        $response["message"] = "Book Updated";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Book Update Failed";
        echoResponse(201, $response);
    }
});

$app->delete('/deleteBook/:id', function($id) use($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    
    $db = new DbHandler();
    $table_name = "books";
    $result = $db->deleteTable($table_name, $condition);
    if ($result != null) {
        $response["status"] = "Success";
        $response["message"] = "Delete Successful";
        echoResponse(200, $response);
    }
    else {
        $response["status"] = "error";
        $response["message"] = "Error Occured";
        echoResponse(201, $response);
    }
});

$app->post('/addAuthor', function() use($app){
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('name', 'misc'),$r);
    $db = new DbHandler();
    
    $name = $r->name;
    $misc = $r->misc;
    
    $isAuthorExist = $db->getOneRecord("select 1 from bk_authors WHERE name='$name' or misc='$misc'");
    if(!$isAuthorExist){
    $table_name = "bk_authors";
    $coloum_name = (array('name', 'misc'));
    $result = $db->insertIntoTable($r, $coloum_name, $table_name);
    
    if($result != null){
        $response["status"] = "Sucess";
        $response["Message"] = "Author Added";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Error Occured";
        echoResponse(201, $response);
    }}
    else{
        $response["status"] = "error";
        $response["message"] = "Author Exist";
        echoResponse(201, $response);
    }
});

$app->put('/editAuthor/:id', function($id) use($app){
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    verifyRequiredParams(array('name', 'misc'),$r);
    $db = new DbHandler();
    $name = $r->name;
    $misc = $r->misc;
    
    $table_name = "bk_authors";
    $coloum_name = (array('name', 'misc'));
    $result = $db->UpdateTable($r, $coloum_name, $table_name);
    
    if($result != null){
        $response["status"] = "Success";
        $response["message"] = "Author Updated";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "Error";
        $response["message"] = "Author Update Failed";
        echoResponse(201, $response);
    }                
});
    
$app->delete('/deleteAuthor/:id', function($id) use($app){
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
   
    $db = new DbHandler();
    $table_name = "bk_authors";
    $result = $db->deleteTable($table_name, $condition);
    
    if($result != null){
        $response["status"] = "success";
        $response["messgae"] = "Author Deleted";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Error Occured";
        echoResponse(201, $response);
    }
});
    
$app->post('/addPublisher', function() use($app){
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('name', 'country', 'website'),$r);
    $db = new DbHandler();
    
    $name = $r->name;
    $country = $r->country;
    $website = $r->website;
    
    $isPublisherExist = $db->getOneRecord("select 1 from bk_publishers where name='$name' or country='$country' or website='$website'");
    if(!$isPublisherExist){
        $table_name = "bk_publishers";
        $coloum_name = (array('name', 'country', 'website'));
        $result = $db->InsertIntoTable($r, $coloum_name, $table_name);
    if($result != null){
        $response["status"] = "success";
        $response["message"] = "Publisher Addded";
        echoResponse(200, $response);
        
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Error Occured";
        echoResponse(201, $response); 
        }}
    else{
        $response["status"] = "error";
        $response["message"] = "Publisher Exist";
        echoResponse(201, $response);
    }
});
    
$app->put('/editPublisher/:id', function($id) use($app){
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    verifyRequiredParams(array('name', 'country', 'website'),$r);
    $db = new DbHandler();
    $name = $r->name;
    $country = $r->country;
    $website = $r->website;
    
    $table_name = "bk_publishers";
    $coloum_name = (array('name', 'country', 'website'));
    $result = $db->UpdateTable($r, $table_name, $condition);
    if($result != null){
        $response["status"] = "success";
        $response["message"] = "Publisher Updated";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Error Occured";
        echoResponse(201, $response);
    }
});
    
$app->delete('/deletePublisher/:id', function($id) use($app){
    $response = array();
    $condition = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    
    $db = new DbHandler();
    $table_name = "bk_publishers";
    $result = $db->deleteTable($table_name, $condition);
    
    if($result != null){
        $response["status"] = "success"; 
        $response["message"] = "Delete Success";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Delete Unsuccesful";
        echoResponse(201, $response);
    }
});        
?>