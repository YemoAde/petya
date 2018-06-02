<?php

class DbHandler {

    private $conn;

    function __construct() {
        require_once 'dbConnect.php';
        // opening db connection
        $db = new dbConnect();
        $this->conn = $db->connect();
    }
    public function maximumTest($id, $toStock){
        $res = $this->conn->query("SELECT products.max_quantity, products.quantity_in_stock FROM products WHERE products._id = '$id';");
        $result = $res->fetch_assoc();
        $max_quantity = $result['max_quantity'];
        $quantity_in_stock = $result['quantity_in_stock'];
        if(($quantity_in_stock + $toStock) > $max_quantity)
            return false;
        else
            return true;
        
    }

    public function minimumTest($id){
        $res = $this->conn->query("SELECT products.min_quantity, products.quantity_in_stock FROM products WHERE products._id = '$id';");
        $result = $res->fetch_assoc();
        if($result['quantity_in_stock'] <= $result['min_quantity'])
            return true;
        else
            return false;
        
    }
    /**
     * Fetching single record
     */
    public function getOneRecord($query) {
        $r = $this->conn->query($query.' LIMIT 1') or die($this->conn->error.__LINE__);
        return $result = $r->fetch_assoc();
    }
    /**
     * Execute a query
     */
    public function MeDo($query) {
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);

        if ($r) {
            $new_row_id = $this->conn->insert_id;
            return $new_row_id;
            } else {
            return NULL;
        }
    }
    public function forQue($query){
        if($this->conn->query($query) === TRUE){
            return 1;
        }
        else{
            return 0;
        }
    }
    
    /**
     * Fetching all record
     */
    public function getAllRecords($query) {
        $r = $this->conn->prepare($query);
        $r->execute();
        $tasks = $r->get_result();
        $r->close();
        return $tasks;
    }
    /**
     * Creating new record
     */
    public function insertIntoTable($obj, $column_names, $table_name) {

        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        $values = '';
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
           if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                $$desired_key = $c[$desired_key];
            }
            $columns = $columns.$desired_key.',';
            $values = $values."'".$$desired_key."',";
        }
        $query = "INSERT INTO ".$table_name."(".trim($columns,',').") VALUES(".trim($values,',').")";

        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);

        if ($r) {
            $new_row_id = $this->conn->insert_id;
            return $new_row_id;
            } else {
            return NULL;
        }
    }
    /**
     * Update  a record
     */
    public function updateTable($columnsArray, $table, $where) {

            $a = array();
            $w = "";
            $c = "";
            foreach ($where as $key => $value) {
                $w .= " and " .$key. " = '".$value."' ";
            }
            foreach ($columnsArray as $key => $value) {
                $c .= $key. " = '".$value."', ";
            }
                $c = rtrim($c,", ");


            $query = "UPDATE $table SET $c WHERE 1=1 ".$w;

            $r = $this->conn->query($query) or die($this->conn->error.__LINE__);

            if($r){
                $response = "success";
            }else{
                $response = NULL;
            }

        return $response;
    }

    /**
     * Delete  a record
     */
    public function deleteTable($table, $where) {

            $w = "";
            foreach ($where as $key => $value) {
                $w .= " and " .$key. " = '".$value."' ";
            }

            $query = "DELETE FROM $table WHERE 1=1 ".$w;

            $r = $this->conn->query($query) or die($this->conn->error.__LINE__);

            if($r){
                $response = "success";
            }else{
                $response = NULL;
            }

        return $response;
    }
    /**
     * Function to store admin details when logged in
     */
public function getSession(){
    if (!isset($_SESSION)) {
        session_start();
    }
    $sess = array();
    if(isset($_SESSION['_id']))
    {
        $sess["_id"] = $_SESSION['_id'];
        $sess["username"] = $_SESSION['username'];
        $sess["email"] = $_SESSION['email'];
        $sess["firstname"] = $_SESSION['firstname'];
        $sess["lastname"] = $_SESSION['lastname'];
        $sess["createdAt"] = $_SESSION['createdAt'];
    }
    else
    {
        $sess["_id"] = '';
        $sess["username"] = 'Guest';
        $sess["email"] = '';
        $sess["firstname"] = '';
        $sess["lastname"] = '';
        $sess["createdAt"] = '';
    }
    return $sess;
}
public function destroySession(){
    if (!isset($_SESSION)) {
    session_start();
    }
    if(isSet($_SESSION['_id']))
    {
        unset($_SESSION['_id']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        unset($_SESSION['firstname']);
        unset($_SESSION['lastname']);
        unset($_SESSION['createdAt']);
        $info='info';
        if(isSet($_COOKIE[$info]))
        {
            setcookie ($info, '', time() - $cookie_time);
        }
        $msg="Logged Out Successfully...";
    }
    else
    {
        $msg = "Not logged in...";
    }
    return $msg;
}

}

?>
