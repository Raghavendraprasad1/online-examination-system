<?php 
require('components/header.php');
require('components/candidate-nav.php');

require('components/formhandler.php');
require('components/dbhandler.php');


echo '
    <h1>Results</h1>
    <div id="content2">';

echo '<table class="tbl1">'
        . '<tr>'
        . '<th>S.<br />No.</th>'
        . '<th>Exam<br />ID</th>'
        . '<th>Exam<br />Roll<br />Number</th>'
        . '<th>Examiner<br />ID</th>'
        . '<th>Questions<br />Answered</th>'
        . '<th>Exam<br />Name</th>'
        . '<th>Duration<br />(minutes)</th>'
        . '<th>Start<br />Time</th>'
        . '<th>End<br />Time</th>'
        . '<th>Max<br />Questions</th>'
        . '<th>Correct<br />Answers</th>'
        . '<th>Percentage</th>'
        . '</tr>';
// Display result if decalared and set declared to true, otherwise not.

$obj = a_query('SELECT a.Exam_ID, a.Exam_Roll_Number, a.Examiner_ID, a.Questions_Answered,'
        . ' b.Exam_Name, b.Duration, b.Start_Time, b.End_Time,'
        . ' c.Max_Questions, c.Correct_Answers, c.Percentage'
            . ' FROM current_exams AS a'
        . ' INNER JOIN exams AS b ON a.Exam_ID=b.ID'
        . ' INNER JOIN candidates_result AS c ON a.Exam_ID=c.Exam_ID'
            . ' WHERE a.Candidate_ID=? AND a.Suspended_Result=False'
                            . ' ORDER BY c.ID DESC', [$id]);
if ($obj->rowCount()>0){
    $declared = true;
    $i = 0;
    while ($row = $obj->fetch()){
        $i++;
        echo '<tr>'
                .'<td>'. $i . '</td>'
                .'<td>'. $row['Exam_ID'] . '</td>'
                .'<td>'. $row['Exam_Roll_Number'] . '</td>'
                .'<td>'. $row['Examiner_ID'] .'</td>'
                .'<td>'. $row['Questions_Answered'] . '</td>'
                .'<td>'. $row['Exam_Name'] . '</td>'
                .'<td>'. $row['Duration'] . '</td>'
                .'<td>'. date('Y-M-d<\b\\r />h:i:s a', strtotime($row['Start_Time'])) . '</td>'
                .'<td>'. date('Y-M-d<\b\\r />h:i:s a', strtotime($row['End_Time'])) . '</td>'
                .'<td>'. $row['Max_Questions'] . '</td>'
                .'<td>'. $row['Correct_Answers'] . '</td>'
                .'<td>'. $row['Percentage'] . '</td>'
                .'</tr>';
    }
}

echo '</table>
        </div>
        ';

require('components/footer.php');

