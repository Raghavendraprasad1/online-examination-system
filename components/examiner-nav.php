<?php
// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 3600);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(3600);

// If not logged in, redirect to homepage.
session_start();
if(!isset($_SESSION['examiner_id'])){
    header('refresh:0; url= /index.php');
    exit();
}

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) == 'GET'){
$logout = filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING);

if($logout == 1){
    session_destroy();
    header('refresh:0; url= /index.php');
    exit();
}
}

$id = $_SESSION['examiner_id'];
$name = $_SESSION['examiner_name'];
        
echo '
 <nav>
<ul>
<li>Examiner ID: <b>'. $id .'</b></li>
<li>('. $name .'
)</li>
<li><a href="examiner-dashboard.php">Dashboard</a></li>
<li><a href="?logout=1;">Logout</a></li>
</ul>
</nav>
<br />
';