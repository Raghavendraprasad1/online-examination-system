<?php
require('components/header.php');
require('components/examiner-nav.php');

require('components/formhandler.php');
require('components/dbhandler.php');

$expired = False;
// Purpose pagination of records.
$offset = 0;
$rec_limit = 10;
$page = 1;
if(a_server('REQUEST_METHOD') == 'GET'){
    // Fetch data into variables from GET method.
    
    // Get exam id
    $exam_id = a_get('id');

    // Get page number of records - pagination.
    $pge = a_get('page');
    if(!empty($pge)){
        $page = $pge;
        $offset = ($page - 1) * $rec_limit;
    }
    
    // If request to suspend a record
    $rec_suspend = filter_input(INPUT_GET, 'suspend', FILTER_SANITIZE_STRING);
    if(!empty($rec_suspend)){
        // Database Operations - Suspend a record from current_exams.
        $result = a_exec('UPDATE current_exams SET Suspended_Result=True WHERE ID=?', [$rec_suspend]);
        if($result){
            echo '<div id="successMsg">Candidate Supended successfully. <br /></div>';
        }
    }
    
    // If request to sustain a record
    $rec_sustain = a_get('sustain');
    if(!empty($rec_sustain)){
        // Database Operations - Sustain a record from current_exams.
        $result = a_exec('UPDATE current_exams SET Suspended_Result=False WHERE ID=?',[$rec_sustain]);
        if($result){
            echo '<div id="successMsg">Candidate Sustained successfully. <br /></div>';
        }
    }
    
}

// Showing details of exam for which GET request has come.
echo '
<h1>Examiner - Selected Exam ('. $exam_id .')</h1>
<a href="'. a_server('PHP_SELF') .'?id='.$exam_id.'" id="reloadBtn">Reload</a>

<div id="content1">
<div id="tabs">
<ul>
<li><a href="#tabs-1">Selected Exam</a></li>
<li><a href="#tabs-2">Active Candidates in Current Exam</a></li>
<li><a href="#tabs-3">Suspended Candidates in Current Exam</a></li>
</ul>

<div id="tabs-1"><br /><br />
';

$obj = a_query('SELECT ID, Exam_Name, Exam_Password, Duration, Additional_Instructions, '
                . 'Question_Bank_IDs, Max_Questions, Start_Time, Candidates_Enrolled FROM exams '
        . 'WHERE Examiner_ID=? AND ID=?'
        . " ORDER BY ID DESC LIMIT $offset, $rec_limit ", [$id, $exam_id]);
if($obj->rowCount() > 0){
   echo '
       <table class="tbl1">
       <tr>
       <th>ID</th>
       <th>Exam Name</th>
       <th>Examiner ID</th>
       <th>Exam Password</th>
       <th>Duration</th>
       <th>Additional Instructions</th>
       <th>Question Bank IDs</th>
       <th>Max Questions</th>
       <th>Start Time</th>
       <th>End Time</th>
       <th>Candidates Enrolled</th>
       </tr>    
   ';
   while($row = $obj->fetch()){
       $rowId = $row['ID'];
       $duration = $row['Duration'];
       $start_time = $row['Start_Time'];

       $s_time = (!empty($start_time)?(date('Y-M-d H:i:s', strtotime($start_time))):'');
       $e_time = (!empty($start_time)?(date('Y-M-d H:i:s', strtotime('+'.$duration.' minutes', strtotime($start_time)))):'');
       // Check if exam has expired
       if (strtotime($e_time) < strtotime(date('Y-M-d H:i:s'))){
           $expired = True;
           echo '<div id="successMsg">Exam has been Completed.<br /><br />'
           . '<a class="btn1" href="/examiner-results.php?result='. $exam_id .'">Go To Results Page</a></div>';
       }
       echo '<tr>'
                 . '<td>'
                 .$rowId.'</td>'   
               . '<td>'
               .'<span><b>'
               .$row['Exam_Name'].'</b></span></td>'
               . '<td>'.$id.'</td>'
               . '<td>'.$row['Exam_Password'].'</td>'
               . '<td>'.$row['Duration'].'</td>'
               . '<td>'.$row['Additional_Instructions'].'</td>'
               . '<td>'.$row['Question_Bank_IDs'].'</td>'
               . '<td>'.$row['Max_Questions'].'</td>'
               . '<td>'
               . (!empty($s_time)?date('Y-M-d <\b\\r /><\b>h:i:s a</\b>', strtotime($s_time)):'')
               .'</td>'
               . '<td>'
               . (!empty($s_time)?date('Y-M-d <\b\\r /><\b>h:i:s a</\b>', strtotime($e_time)):'')
               .'</td>'
               . '<td>'.$row['Candidates_Enrolled'].'</td>'
               . '</tr>';
   }
   echo '</table>';

   echo '<hr />';
}else{
   echo 'No Exams created yet.';
}
        
   
 //Showing details of candidates which have enrolled in current exam and are active.
 echo '
     </div>
     <div id="tabs-2"><br /><br />

<a href="#" ID="btnSuspend" class="btn1">Suspend</a>
<br />

<div id="dialogSuspend">
<div>
</div>
<a ID="btnSus" class="btn1" href="#">Suspend</a> 
<a ID="btnSusCancel" class="btn1" href="#">Cancel</a>
</div><br /><br />
';

$obj = a_query("SELECT a.ID, a.Candidate_ID, a.Exam_Roll_Number, "
        . "b.First_Name, b.Last_Name, b.Sex, b.Contact_Number, b.Email, a.Interference "
                . "FROM current_exams AS a "
        . "INNER JOIN candidate_info AS b ON a.Candidate_ID=b.ID"
        . " WHERE a.Examiner_ID=? AND a.Exam_ID=? AND a.Suspended_Result=False"
        . " ORDER BY Interference DESC LIMIT $offset, $rec_limit ", [$id, $exam_id]);
if($obj->rowCount() > 0){
   echo '
       <table class="tbl1">
       <tr>
       <th>ID</th>
       <th>Candidate ID</th>
       <th>Exam Roll Number</th>
       <th>Name</th>
       <th>Sex</th>
       <th>Contact Number</th>
       <th>Email</th>
       <th>Interference</th>
       </tr>    
   ';
   while($row = $obj->fetch()){
       $rowId = $row['ID'];
       echo '<tr ID="row'. $rowId .'">'
                 . '<td><input type="radio" name="selection" value="'. $rowId .'" />'
               . '<span ID="rowC1'. $rowId .'">'
                 .$rowId.'</span></td>'   
               . '<td ID="rowC2'. $rowId .'">'.$row['Candidate_ID'].'</td>'
               . '<td ID="rowC3'. $rowId .'">'.$row['Exam_Roll_Number'].'</td>'
               . '<td ID="rowC4'. $rowId .'">'.$row['First_Name']. ' ' .$row['Last_Name'] . '</td>'
               . '<td ID="rowC5'. $rowId .'">'.$row['Sex'].'</td>'
               . '<td ID="rowC6'. $rowId .'">'.$row['Contact_Number'].'</td>'
               . '<td ID="rowC7'. $rowId .'">'.$row['Email'].'</td>'
               . '<td ID="rowC8'. $rowId .'">'.$row['Interference'].'</td>'
               . '</tr>';
   }
   echo '</table>';

   $obj = a_query('Select COUNT(ID) AS Total FROM exams');
   $row = $obj->fetch();
   $total_records = $row['Total'];
   $total_pages = ceil($total_records / $rec_limit);
   echo '<hr />';
   for($i=1; $i<=$total_pages; $i++){
        if ($i == $page){
            echo '<span class="paging">' .
            $i . '</span> ';
        }else{
            echo '<a class="paging1" href="'. a_server('PHP_SELF') .
            '?page='. $i .'">'. $i . '</a> ';
        }
   }
}else{
   if($page > 1){
       // Goto previous page if table is not empty.
       $page--;
       // Release Connection - Before Refresh.
       $conn = null;
       header('refresh:0; url= '. 
               a_server('PHP_SELF'). '?page=' .$page);
       exit();
   }else{
       // Means table is empty.
       echo 'No Active Candidates.';
   }
}
 
//Showing details of candidates which have enrolled in current exam and are suspended.
 echo '
     </div><div id="tabs-3"><br /><br />

<a href="#" ID="btnSustain" class="btn1">Sustain</a>
<br />

<div id="dialogSustain">
<div>
</div>
<a ID="btnSust" class="btn1" href="#">Sustain</a> 
<a ID="btnSustCancel" class="btn1" href="#">Cancel</a>
</div><br /><br />
';

$obj = a_query('SELECT a.ID, a.Candidate_ID, a.Exam_Roll_Number, '
        . 'b.First_Name, b.Last_Name, b.Sex, b.Contact_Number, b.Email, a.Interference '
                . 'FROM current_exams AS a '
        . 'INNER JOIN candidate_info AS b ON a.Candidate_ID=b.ID'
        . ' WHERE a.Examiner_ID=? AND a.Exam_ID=? AND a.Suspended_Result=True'
        . ' ORDER BY Interference DESC', [$id, $exam_id]);
if($obj->rowCount() > 0){
   echo '
       <table class="tbl1">
       <tr>
       <th>ID</th>
       <th>Candidate ID</th>
       <th>Exam Roll Number</th>
       <th>Name</th>
       <th>Sex</th>
       <th>Contact Number</th>
       <th>Email</th>
       <th>Interference</th>
       </tr>    
   ';
   while($row = $obj->fetch()){
       $rowId = $row['ID'];
       echo '<tr ID="row'. $rowId .'">'
                 . '<td><input type="radio" name="selection" value="'. $rowId .'" />'
               . '<span ID="rowC1'. $rowId .'">'
                 .$rowId.'</span></td>'   
               . '<td ID="rowC2'. $rowId .'">'.$row['Candidate_ID'].'</td>'
               . '<td ID="rowC3'. $rowId .'">'.$row['Exam_Roll_Number'].'</td>'
               . '<td ID="rowC4'. $rowId .'">'.$row['First_Name']. ' ' .$row['Last_Name'] . '</td>'
               . '<td ID="rowC5'. $rowId .'">'.$row['Sex'].'</td>'
               . '<td ID="rowC6'. $rowId .'">'.$row['Contact_Number'].'</td>'
               . '<td ID="rowC7'. $rowId .'">'.$row['Email'].'</td>'
               . '<td ID="rowC8'. $rowId .'">'.$row['Interference'].'</td>'
               . '</tr>';
   }
   echo '</table>';
   echo '<hr />';
}else{
   echo 'No Suspended Candidates.';
}
 

echo'
    </div>
</div>';

if($expired){
    echo '<div class="noticeMsg">Exam has been Completed.<br /><br />'
                    . '<a class="btn1" href="/examiner-results.php?result='. $exam_id .'">'
            . 'Go To Results Page</a><br /><br /></div>';
}

echo '</div>
';

require('components/footer.php');
?>

<script>
$(document).ready(function(){
    
    // Environment setup for record suspension
    $("#btnSuspend").click(function(){
            var selection = $("input[name=selection]:checked").val();
            if(selection){
                var str = "Are you sure you want to suspend this record ? <br /><br /><br />" + 
                                "<table class=tbl1>" +
                                "<tr><th>ID</th><th>Candidate ID</th><th>Exam Roll Number</th><th>Interference</th></tr>"+
                                "<tr><td>" +
                                $("#rowC1"+selection).html() + "</td><td>" +
                                $("#rowC2"+selection).html() + "</td><td>" +
                                $("#rowC3"+selection).html() + "</td><td>" +
                                $("#rowC4"+selection).html() + "</td></tr></table><br /><br />";
                $("#dialogSuspend").children("div").html(str);
                
                $("#btnSus").attr("href", "<?php 
                echo filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING) 
                        . '?id='. $exam_id .'&page=' .$page ?>&suspend=" + selection);
                        
                $("#dialogSuspend").show();
                
            }
    });
    // Cancel Suspend Operation
    $("#btnSusCancel").click(function(){
        $("#dialogSuspend").hide();
    } );
    
    // Environment setup for sustaining a record
    $("#btnSustain").click(function(){
            var selection = $("input[name=selection]:checked").val();
            if(selection){
                var str = "Are you sure you want to sustain this record ? <br /><br /><br />" + 
                                "<table class=tbl1>" +
                                "<tr><th>ID</th><th>Candidate ID</th><th>Exam Roll Number</th><th>Interference</th>" +
                                "<th>Start Time</th> " + 
                                "</tr><tr><td>" + 
                                selection + "</td><td>" +
                                $("#rowC1"+selection).html() + "</td><td>" +
                                $("#rowC2"+selection).html() + "</td><td>" +
                                $("#rowC3"+selection).html() + "</td><td>" +
                                $("#rowC4"+selection).html() + "</td></tr></table><br /><br />";
                $("#dialogSustain").children("div").html(str);
                
                $("#btnSust").attr("href", "<?php 
                echo filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING) 
                        . '?id='. $exam_id .'&page=' .$page ?>&sustain=" + selection);
                        
                $("#dialogSustain").show();
                
            }
    });
    // Cancel Suspend Operation
    $("#btnSustCancel").click(function(){
        $("#dialogSustain").hide();
    } );
    
    // Separation of divisions into tabs
$( "#tabs" ).tabs();

// *** Code to remember last tab that was opened.
    //  jQueryUI 1.10 and HTML5 ready
    //      http://jqueryui.com/upgrade-guide/1.10/#removed-cookie-option 
    //  Documentation
    //      http://api.jqueryui.com/tabs/#option-active
    //      http://api.jqueryui.com/tabs/#event-activate
    //      http://balaarjunan.wordpress.com/2010/11/10/html5-session-storage-key-things-to-consider/
    //
    //  Define friendly index name
    var index = "key";
    //  Define friendly data store name
    var dataStore = window.sessionStorage;
    //  Start magic!
    try {
        // getter: Fetch previous value
        var oldIndex = dataStore.getItem(index);
    } catch(e) {
        // getter: Always default to first tab in error state
        var oldIndex = 0;
    }
    $("#tabs").tabs({
        // The zero-based index of the panel that is active (open)
        active : oldIndex,
        // Triggered after a tab has been activated
        activate : function( event, ui ){
            //  Get future value
            var newIndex = ui.newTab.parent().children().index(ui.newTab);
            //  Set future value
            dataStore.setItem( index, newIndex ) 
        }
    });
    

});
</script>
