<?php 
require('components/header.php');
require('components/examiner-nav.php');

require('components/formhandler.php');
require('components/dbhandler.php');


echo '
    <h1>Results (<u>Overall</u>)</h1>
    <a href="'. a_server('PHP_SELF') .'" id="reloadBtn">Reload</a>
    <div id="content2">';

echo '<table class="tbl1">'
        . '<tr>'
        . '<th>S.<br />No.</th>'
        . '<th>Candidate<br />ID</th>'
        . '<th>Exam<br />Roll<br />Number</th>'
        . '<th>Exam<br />ID</th>'
        . '<th>Name</th>'
        . '<th>Sex</th>'
        . '<th>Contact<br />Number</th>'
        . '<th>Email</th>'
        . '<th>Correct<br />Answers</th>'
        . '<th>Max<br />Questions</th>'
        . '<th>Percentage</th>'
        . '</tr>';
// Display result if decalared and set declared to true, otherwise not.
    $obj = a_query('SELECT a.Exam_ID, a.Candidate_ID, a.Exam_Roll_Number,'
            . ' b.First_Name, b.Last_Name, b.Sex, b.Contact_Number, b.Email,'
            . ' c.Correct_Answers, c.Max_Questions, c.Percentage'
                . ' FROM current_exams AS a'
            . ' INNER JOIN candidate_info AS b ON a.Candidate_ID=b.ID'
            . ' INNER JOIN candidates_result AS c ON a.Candidate_ID=c.Candidate_ID'
                . ' WHERE a.Examiner_ID=? AND a.Suspended_Result=False AND c.Exam_ID=a.Exam_ID'
            . ' ORDER BY c.Percentage DESC', [$id]);
    if ($obj->rowCount()>0){
        $declared = true;
        $i = 0;
        while ($row = $obj->fetch()){
            $i++;
            echo '<tr>'
                    .'<td>'. $i . '</td>'
                    .'<td>'. $row['Candidate_ID'] . '</td>'
                    .'<td>'. $row['Exam_Roll_Number'] . '</td>'
                    .'<td>'. $row['Exam_ID'] . '</td>'
                    .'<td>'. $row['First_Name'] . ' '. $row['Last_Name'] . '</td>'
                    .'<td>'. $row['Sex'] . '</td>'
                    .'<td>'. $row['Contact_Number'] . '</td>'
                    .'<td>'. $row['Email'] . '</td>'
                    .'<td>'. $row['Correct_Answers'] . '</td>'
                    .'<td>'. $row['Max_Questions'] . '</td>'
                    .'<td>'. $row['Percentage'] . '</td>'
                    .'</tr>';
        }
    }
echo '
    </table>
    </div>
';

require('components/footer.php');

