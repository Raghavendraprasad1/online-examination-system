<?php 
require('components/header.php');
require('components/examiner-nav.php');

?>

<h1>Instructions for Examiner</h1>
<main>
    <h2>Creating Question Banks</h2><br />
    <ul>
        <li>Question Bank is a group of several questions which may be used again and again.</li>
        <li>Creating a question bank is the first step of examination system.<br />Proceed by selecting 
            <b>Create a Question Bank</b> tab.<br />Create the question bank.</li>
        <li>After creating a Question Bank click on its name in <b>Question Banks List</b> tab.</li>
        <li>A new page will open where you can save questions in particular Question Bank.</li>
    </ul>
    <br /><br />
    <h2>Setup an examination</h2><br />
    <ul>
        <li>Go to <b>Exams</b>.</li>
        <li>Select <b>Create an Exam</b> tab.</li>
        <li>Fill Exam details and click on <b>Create</b>.</li>
        <li>Select <b>Manage Exams</b> tab.</li>
        <li>Select an exam ID and click on <b>Start Now</b> button.</li>
        <li>Go to <b>Active Exams</b> tab.<br />This tab contains all ongoing exams. Click on exam name.<br />
            A new page will be opened.</li>
        <li>In the opened page, you can manage candidates appearing in the examination.</li>
    </ul>
    <br /><br />
    <h2>Inviting Candidates</h2><br />
    <ul>
        <li>Give the information to Candidates about the exam - <b>Exam ID, Examiner ID, Exam Password</b></li>
        <li>Candidates have to use their roll number which is not managed by the system(just for examiner's reference).</li>
    </ul>
    <br /><br />
    <h2>Managing Candidates</h2><br />
    <ul>
        <li>An examiner can manage candidates by clicking on Exam Name</li>
        <li>A candidate result can be suspended or sustained.</li>
        <li>Once a candidate result is suspended, candidate cannot appear in examination until sustained.</li>
        <li>Result of a candidate can be suspended at any time even after result is out.</li>
    </ul>
    <br /><br />
    <h2>Declaring Result</h2><br />
    <ul>
        <li>Once examination is over, result is only generated when examiner click on <b>Go To Results Page</b>.</li>
        <li>Until examiner opens <b>Results</b> page, result will not be generated.</li>
    </ul>
    <br /><br />
    <h2>Overall Results</h2><br />
    <ul>
        <li>This section shows all results associated with the examiner.</li>
        <li>List is sorted according to percentage in descending order.</li>
        <li>This section is different from particular examination's results.</li>
    </ul>
    <br /><br />
    <h2>Interference</h2>
    <ul>
        <li>Any unusual activity of candidate's examination window is counted as interference.</li>
        <li>It may involve minimization of examination window, refreshing window or third party pop ups.</li>
        <li>It may not mean unfair sometime.<br />
            As examination window can be interrupted by a third party software<br />
            such as Anti-virus software pop up or refreshing page when a timeout occurs.</li>
        <li>It is upto the examiner to check increased number of interference of a candidate examination window.</li>
        <li>System itself does not take any action against interference.</li>
    </ul>
    <br /><br />
</main>

<?php require('components/footer.php');

