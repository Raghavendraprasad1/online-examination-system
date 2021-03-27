<?php
// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 3600);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(3600);

// If not logged in, redirect to homepage.
session_start();
if(!isset($_SESSION['candidate_id'])){
    //header('refresh:0; url= /index.php');
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

$id = $_SESSION['candidate_id'];
$name = $_SESSION['candidate_name'];
$exam_roll_number = '';
if (isset($_SESSION['exam_roll_number'])){
    $exam_roll_number = $_SESSION['exam_roll_number'];
}

echo '
    <nav>
    <ul>
    <li>Candidate ID: <b>'. $id .'</b></li>
    <li>';

if (!empty($exam_roll_number)){
    echo '<b>'. $exam_roll_number .'</b><br />';
}

echo '('. $name .')</li>';

if (filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING) != '/candidate-exam-window.php'){
    echo '
    <li><a href="candidate-dashboard.php">Dashboard</a></li>
    <li><a href="?logout=1;">Logout</a></li>
    ';
}

echo '
    </ul>
    </nav>
    <br />
    ';