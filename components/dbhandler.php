<?php
$error = '';

// PDO Database Connectivity
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'mydb_exam1';

$dsn = "mysql:host=$servername;dbname=$dbname";

$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

// Save connection data in $conn
$conn = new PDO($dsn, $username, $password, $opt);

// Query information from table
function a_query($sql, $values=null){  // string, array/empty_string
    GLOBAL $conn, $error;
    try{
        if($values != null){
            $obj = $conn->prepare($sql);
            $obj->execute($values);
        }else{
            $obj = $conn->query($sql);
        }
        return $obj;
    } catch (PDOException $e) {
        $error .= '<hr />' . $sql;
        $error .= '<hr />' . $e;
        echo $error;
    }
}


// Execute SQL
function a_exec($sql, $values=null){  // string, array/empty_string
    GLOBAL $conn, $error;
    try{
        if($values != null){
            $result = $conn->prepare($sql);
            $result->execute($values);
        }else{
            $result = $conn->exec($sql);
        }
        if(!empty($conn->lastInsertId())){
            return $conn->lastInsertId();   // Return only if Insert command
        }else{
            return $result; // Return if not Insert command
        }
    } catch (PDOException $e) {
        $error .= '<hr />' . $sql;
        $error .= '<hr />' . $e;
        echo $error;
    }
}