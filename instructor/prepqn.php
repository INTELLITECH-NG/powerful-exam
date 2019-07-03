
<?php
/*
 * ***************************************************
 * ** Examination Management System                ***
 * **--------------------------------------------- ***
 * ** Developer: Dejene Techane                    ***
 * ** Title: Prepare Questions(Add,delete,Modify)  ***
 * ***************************************************
 */

/* Procedure
 * ********************************************

 * ----------- *
 * PHP Section *
 * ----------- *

  Step 1: Perform Session Validation.

  Step 2: Event to Process...
  Case 1 : Logout - perform session cleanup.
  Case 2 : Manage Exam - redirect to Exam Management Section.
  Case 3 : Delete - Delete the selected Question/s from Exam.
  Case 4 : Edit - Update the Question.
  Case 5 : Add - Add new Question to the Exam.

 * ------------ *
 * HTML Section *
 * ------------ *

  Step 3: Display the HTML Components for...
  Case 1: Add - Form to receive new Question.
  Case 2: Edit - Form to edit Existing Question.
  Case 3: Default Mode - Displays the Information of Existing Questions, If any.
 * ********************************************
 */

error_reporting(0);
session_start();
include_once '../include/emsdb.php';
/* * ************************ Step 1 ************************ */
if (!isset($_SESSION['username']) || !isset($_SESSION['testqn'])) {
    $_GLOBALS['message'] = "Session Timeout.Click here to <a href=\"../index.php\">Re-LogIn</a>";
} 
//This is to protect unauthorized users
 else if(!($_SESSION['role']=="Instructor")){
    unset($_SESSION['username']);
  $_GLOBALS['message']="Please, You are accessing unauthorized page.Click here to <a href=\"../index.php\">Re-LogIn</a>";
 }else if (isset($_REQUEST['logout'])) {
    /*     * ************************ Step 2 - Case 1 ************************ */
    //Log out and redirect login page
    unset($_SESSION['username']);
    header('Location: ../index.php');
} else if (isset($_REQUEST['mngexam'])) {
    /*     * ************************ Step 2 - Case 2 ************************ */
    //redirect to Manage Tests Section

    header('Location: exammng.php');
} else if (isset($_REQUEST['delete'])) {
    /*     * ************************ Step 2 - Case 3 ************************ */
    //deleting the selected Questions
    unset($_REQUEST['delete']);
    $hasvar = false;
    $count = 1;
    foreach ($_REQUEST as $variable) {
        if (is_numeric($variable)) { //it is because, some session values are also passed with request
            $hasvar = true;

            if (!@executeQuery("delete from question where testid=" . $_SESSION['testqn'] . " and qnid=$variable"))
                $_GLOBALS['message'] = mysql_error();
        }
    }
    //reordering questions

    $result = executeQuery("select qnid from question where testid=" . $_SESSION['testqn'] . " order by qnid;");
    while ($r = mysql_fetch_array($result))
        if (!@executeQuery("update question set qnid=" . ($count++) . " where testid=" . $_SESSION['testqn'] . " and qnid=" . $r['qnid'] . ";"))
            $_GLOBALS['message'] = mysql_error();

    
    if (!isset($_GLOBALS['message']) && $hasvar == true)
        $_GLOBALS['message'] = "Selected Questions are successfully Deleted";
    else if (!$hasvar) {
        $_GLOBALS['message'] = "First Select the Questions to be Deleted.";
    }
} else if (isset($_REQUEST['savem'])) {
    /*     * ************************ Step 2 - Case 4 ************************ */
    //updating the modified values
    //checking question type
    	$cans=$_SESSION['cans'];
    	if(!($cans=='TRUE'||$cans=='FALSE')){
    if (strcmp($_REQUEST['correctans'], "<Choose the Correct Answer>") == 0 || empty($_REQUEST['question']) || empty($_REQUEST['optiona']) || empty($_REQUEST['optionb']) || empty($_REQUEST['optionc']) || empty($_REQUEST['optiond']) || empty($_REQUEST['marks'])) {
        $_GLOBALS['message'] = "Some of the required Fields are Empty";
    } else if (strcasecmp($_REQUEST['optiona'], $_REQUEST['optionb']) == 0 || strcasecmp($_REQUEST['optiona'], $_REQUEST['optionc']) == 0 || strcasecmp($_REQUEST['optiona'], $_REQUEST['optiond']) == 0 || strcasecmp($_REQUEST['optionb'], $_REQUEST['optionc']) == 0 || strcasecmp($_REQUEST['optionb'], $_REQUEST['optiond']) == 0 || strcasecmp($_REQUEST['optionc'], $_REQUEST['optiond']) == 0) {
        $_GLOBALS['message'] = "Two or more options are representing same answers.Verify Once again";
    } else {
    	
        $query = "update question set question='" . htmlspecialchars($_REQUEST['question'], ENT_QUOTES) . "',optiona='" . htmlspecialchars($_REQUEST['optiona'], ENT_QUOTES) . "',optionb='" . htmlspecialchars($_REQUEST['optionb'], ENT_QUOTES) . "',optionc='" . htmlspecialchars($_REQUEST['optionc'], ENT_QUOTES) . "',optiond='" . htmlspecialchars($_REQUEST['optiond'], ENT_QUOTES) . "',correctanswer='" . htmlspecialchars($_REQUEST['correctans'], ENT_QUOTES) . "',marks=" . htmlspecialchars($_REQUEST['marks'], ENT_QUOTES) . " where testid=" . $_SESSION['testqn'] . " and qnid=" . $_REQUEST['qnid'] . " ;";
        if (!@executeQuery($query))
            $_GLOBALS['message'] = mysql_error();
        else
            $_GLOBALS['message'] = "Question is updated Successfully.";
        }
        }
   else{
   	   $optionc="TRUE";
   	   $optiond="FALSE";
    $query = "update question set question='" . htmlspecialchars($_REQUEST['question'], ENT_QUOTES) . "',optionc='" .$optionc."',optiond='" . $optiond . "',correctanswer='" . htmlspecialchars($_REQUEST['corrans'], ENT_QUOTES) . "',marks=" . htmlspecialchars($_REQUEST['marks'], ENT_QUOTES) . " where testid=" . $_SESSION['testqn'] . " and qnid=" . $_REQUEST['qnid'] . " ;";
        if (!@executeQuery($query))
            $_GLOBALS['message'] = mysql_error();
        else
            $_GLOBALS['message'] = "Question is updated Successfully.";
    }
    closedb();
}
 else if(isset($_REQUEST['savea'])){ 
 	 
 if ($_FILES['file']['size'] > 0) {  	
 	 $file = $_FILES['file']['tmp_name'];
 	 $handle = fopen($file, "r");	     
 	 //switch statement to check exam type
switch($_REQUEST['qtype']){	
	
case 'MCQ':
		$c=0;	
		while(($filesop = fgetcsv($handle, 10000, ",")) !== false)
		{
		    $question = $filesop[0];			
			$optiona=$filesop[1];
			$optionb=$filesop[2];
			$optionc=$filesop[3];
			$optiond=$filesop[4];
			$canswer = $filesop[5];
			$marks = $filesop[6];
			
			if($canswer=="A" || $canswer=="a")
				$cans="optiona";
			else if($canswer=="B" || $canswer=="b")
			    $cans="optionb";
			else if($canswer=="C" || $canswer=="c")
			    $cans="optionc";
			else if($canswer=="D" || $canswer=="d")
			    $cans="optiond";			
		
			$c++;
	$result = executeQuery("select count(*) as q from question where testid=" . $_SESSION['testqn'] . ";");
    $r2 = mysql_fetch_array($result);
    $totalq=$r2['q'];
    $result = executeQuery("select totalquestions from test where testid=" . $_SESSION['testqn'] . ";");
    $r1 = mysql_fetch_array($result);
    $tq=$r1['totalquestions'];
    if (!is_null($r2['q']) && (int) htmlspecialchars_decode($r1['totalquestions'],ENT_QUOTES) == (int) $r2['q']) {
        //$cancel = true;
   $_GLOBALS['message'] = "Already you have created all the Questions for this Exam.<br /><b>Help:</b> If you still want to add some more questions then edit the exam settings(option:Total Questions).";
    }   
	$result = executeQuery("select max(qnid) as qn from question where testid=" . $_SESSION['testqn'] . ";");
    $r = mysql_fetch_array($result);
    if (is_null($r['qn']))
        $newq = 1;
    else
        $newq=$r['qn'] + 1;
        $q=$newq-1;
   $result = executeQuery("select * from question where testid=" . $_SESSION['testqn'] . " and question='" .$question . "';");
   $r1 = mysql_fetch_array($result);
    //to protect different question type or other excel data to be inserted.
    if(!($canswer =="A"|| $canswer =="a"||$canswer =="B"||$canswer =="b" ||$canswer =="C"||$canswer =="c"||$canswer =="D"||$canswer =="d"))
    {
    	$_GLOBALS['message']="Sorry, you are adding different question type that you are selected.Verify Once again ";
    }
   //string comparing the same questions are not to be added on same exam.
   else if (strcasecmp($optiona, $optionb)== 0 || strcasecmp($optiona, $optionc)== 0||strcasecmp($optiona, $optiond)== 0||strcasecmp($optionb, $optionc)== 0||strcasecmp($optionb, $optiond)== 0||strcasecmp($optionc, $optiond)== 0) {
   	   $_GLOBALS['message']="Sorry,Two or more options are representing same answers .Verify Once again ";
   }
    else if (strcasecmp($optiona, $r1['optiona'])== 0 || strcasecmp($optionb, $r1['optionb'])== 0||strcasecmp($optionc, $r1['optionc'])== 0||strcasecmp($optiond, $r1['optiona'])== 0) {
   	   $_GLOBALS['message']="Sorry, you are adding same question for Same exam.Verify Once again  ";
   } 
     else {      	 
    if($c>1){		
    if($totalq<$tq)
		$sql1 = executeQuery("INSERT INTO question(testid,qnid,question,optiona,optionb,optionc,optiond,correctanswer,marks) VALUES ('".$_SESSION['testqn']."','$newq','$question','$optiona','$optionb','$optionc','$optiond','$cans','$marks')");			
       } 
		if($sql1){
			$_GLOBALS['message']="Your Questions has imported successfully.You have added ".$q ." Questions for this exam.";
		}else{
			 $_GLOBALS['message']= "Your questions has not inserted successfully".mysql_error();	
		}
		 }
     }
		
break;
case 'TF':	  
     	$c = 0;   	
		while(($filesop = fgetcsv($handle, 10000, ",")) !== false)
		{
			$question = $filesop[0];
			$canswer = $filesop[1];
			$marks = $filesop[2];
			$optiona="NULL";
			$optionb="NULL";
			$optionc="TRUE";
			$optiond="FALSE"; 
			if($canswer=="TRUE")
				$cans="optionc";
			else if($canswer=="FALSE")
			    $cans="optiond";		
			$c++;
	$result = executeQuery("select count(*) as q from question where testid=" . $_SESSION['testqn'] . ";");
    $r2 = mysql_fetch_array($result);
    $totalq=$r2['q'];
    $result = executeQuery("select totalquestions from test where testid=" . $_SESSION['testqn'] . ";");
    $r1 = mysql_fetch_array($result);
    $tq=$r1['totalquestions'];
    if (!is_null($r2['q']) && (int) htmlspecialchars_decode($r1['totalquestions'],ENT_QUOTES) == (int) $r2['q']) {
        //$cancel = true;
   $_GLOBALS['message'] = "Already you have created all the Questions for this Exam.<br /><b>Help:</b> If you still want to add some more questions then edit the exam settings(option:Total Questions).";
    }   
	$result = executeQuery("select max(qnid) as qn from question where testid=" . $_SESSION['testqn'] . ";");
    $r = mysql_fetch_array($result);
    if (is_null($r['qn']))
        $newq = 1;
    else
        $newq=$r['qn'] + 1;
        $q=$newq-1;
   $result = executeQuery("select * from question where testid=" . $_SESSION['testqn'] . " and question='" .$question . "';");
   $r1 = mysql_fetch_array($result);
   //string comparing the same questions are not to added on same exam.
   if (strcasecmp($question, $r1['question'])== 0) {
   	   $_GLOBALS['message']="Sorry, You are entering same question for Same exam.Verify Once again ";
   }  
   //to protect different question type or other excel data to inserted.
    else if(!($canswer =="FALSE"|| $canswer=="TRUE"))
    {
    	$_GLOBALS['message']="Sorry,You are added different question type that you are selected.Verify Once again ";
    }
     else {      
    	if($c>1){
    		
    if($totalq<$tq)
		$sql1 = executeQuery("INSERT INTO question(testid,qnid,question,optionc,optiond,correctanswer,marks) VALUES ('".$_SESSION['testqn']."','$newq','$question','$optionc','$optiond','$cans','$marks')");			
       }     
		if($sql1){
			$_GLOBALS['message']="Your Questions has imported successfully.You have added ".$q ." Questions for this exam.";
		}else{
			 $_GLOBALS['message']= "Your question is not successfully inserted";	
		}
	 }
	}
	break;           
	default:
	 $_GLOBALS['message']= "Please, Select Question Type.";
      }   	  
	  }			
	else{
	 $_GLOBALS['message']= "You are opening Wrong CSV file, try again.";	
	}
 
	closedb();
	}
	
?>
<html>
    <head>
        <title>EMS-Manage Questions</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
       <link rel="icon" type="jpg/png" href="images/logo.png"/>
        <link rel="stylesheet" type="text/css" href="../ems.css"/>  
        <script language="JavaScript" type="text/javascript" src="../jquery.js"></script>
        <script language="JavaScript" type="text/javascript" src="../jTPS.js"></script>
		<link rel="stylesheet" type="text/css" href="../jTPS.css">

        <script>

                $(document).ready(function () {
               
                        $('#demoTable').jTPS( {perPages:[5,12,15,50,'ALL'],scrollStep:1,scrollDelay:30,
                                clickCallback:function () {    
                                        // target table selector
                                        var table = '#demoTable';
                                        // store pagination + sort in cookie
                                        document.cookie = 'jTPS=sortasc:' + $(table + ' .sortableHeader').index($(table + ' .sortAsc')) + ',' +
                                                'sortdesc:' + $(table + ' .sortableHeader').index($(table + ' .sortDesc')) + ',' +
                                                'page:' + $(table + ' .pageSelector').index($(table + ' .hilightPageSelector')) + ';';
                                }
                        });

                        // reinstate sort and pagination if cookie exists
                        var cookies = document.cookie.split(';');
                        for (var ci = 0, cie = cookies.length; ci < cie; ci++) {
                                var cookie = cookies[ci].split('=');
                                if (cookie[0] == 'jTPS') {
                                        var commands = cookie[1].split(',');
                                        for (var cm = 0, cme = commands.length; cm < cme; cm++) {
                                                var command = commands[cm].split(':');
                                                if (command[0] == 'sortasc' && parseInt(command[1]) >= 0) {
                                                        $('#demoTable .sortableHeader:eq(' + parseInt(command[1]) + ')').click();
                                                } else if (command[0] == 'sortdesc' && parseInt(command[1]) >= 0) {
                                                        $('#demoTable .sortableHeader:eq(' + parseInt(command[1]) + ')').click().click();
                                                } else if (command[0] == 'page' && parseInt(command[1]) >= 0) {
                                                        $('#demoTable .pageSelector:eq(' + parseInt(command[1]) + ')').click();
                                                }
                                        }
                                }
                        }

                        // bind mouseover for each tbody row and change cell (td) hover style
                        $('#demoTable tbody tr:not(.stubCell)').bind('mouseover mouseout',
                                function (e) {
                                        // hilight the row
                                        e.type == 'mouseover' ? $(this).children('td').addClass('hilightRow') : $(this).children('td').removeClass('hilightRow');
                                }
                        );

                });


        </script>
        <style>
                body {
                        font-family: Tahoma;
                        font-size: 10pt;
                }
                #demoTable thead th {										
                        white-space: nowrap;
                        overflow-x:hidden;
                        padding: 3px;
                }
                #demoTable tbody td{
												                           
                        padding: 3px;
                }
        </style>
        <script type="text/javascript" src="../validate.js" ></script>
    </head>
    <body>
<?php
if ($_GLOBALS['message']) {
    echo "<div class=\"message\">" . $_GLOBALS['message'] . "</div>";
}
?>
        <div id="container">
             <div class="header">                              
                <?php require'../include/header.php';?>                
                </div>
            <form name="prepqn" action="prepqn.php" method="post" enctype="multipart/form-data">
                <div class="menubar">


                    <ul id="menu">
<?php
if (isset($_SESSION['username']) && isset($_SESSION['testqn'])) {
    // Navigations
?>
                        <li><input type="submit" value="LogOut" name="logout" class="subbtn" title="Log Out"/></li>
                        <li><input type="submit" value="Manage Exam" name="mngexam" class="subbtn" title="Manage Exam"/></li>

<?php
    //navigation for Add option
    if (isset($_REQUEST['add'])) {
?>
                        <li><input type="submit" value="Cancel" name="cancel" class="subbtn" title="Cancel"/></li>
                        <li><input type="submit" value="Save" name="savea" class="subbtn" onclick="validateqnform('prepqn')" title="Save the Changes"/></li>

<?php
    } else if (isset($_REQUEST['edit'])) { //navigation for Edit option
?>
                        <li><input type="submit" value="Cancel" name="cancel" class="subbtn" title="Cancel"/></li>
                        <li><input type="submit" value="Save" name="savem" class="subbtn" onclick="validateqnform('prepqn')" title="Save the changes"/></li>

<?php
    } else {  //navigation for Default
?>
                        <li><input type="submit" value="Delete" name="delete" class="subbtn" title="Delete"onclick="return confirm('Are you sure to delete this record?')"/></li>
                        <li><input type="submit" value="Add" name="add" class="subbtn" title="Add new Questions"/></li>
<?php }
} ?>
                    </ul>

                </div>

                <div class="page">
<?php
$result = executeQuery("select count(*) as q from question where testid=" . $_SESSION['testqn'] . ";");
$r1 = mysql_fetch_array($result);

$result = executeQuery("select totalquestions from test where testid=" . $_SESSION['testqn'] . ";");
$r2 = mysql_fetch_array($result);
if ((int) $r1['q'] == (int) htmlspecialchars_decode($r2['totalquestions'], ENT_QUOTES))
    echo "<div class=\"pmsg\"> Exam Name: " . $_SESSION['testname'] . "<br/>Status: Already you have created all the Questions for this exam.<br /><b>Help:</b> If you still want to add some more questions then edit the exam settings(option:Total Questions).</div>";
else
    echo "<div class=\"pmsg\"> Exam Name: " . $_SESSION['testname'] . "<br/>Status: Still you need to create " . (htmlspecialchars_decode($r2['totalquestions'], ENT_QUOTES) - $r1['q']) . " Question/s. After that only, exam will be available for candidates.</div>";
?>
                    <?php
                    if (isset($_SESSION['username']) && isset($_SESSION['testqn'])) {

                 if (isset($_REQUEST['add'])) {
                            /*                             * ************************ Step 3 - Case 1 ************************ */
                            //form to select quesstion type  
                            
                    ?>
                    
        <table cellpadding="20" cellspacing="20" style="text-align:left;margin-left:15em" >
         <tr>
         <td><div class="help">NOTE: To import Questions from excel follow the following steps:</br>
         1.Open new spreadsheet and format like the following</br></div></td></tr>
         <tr><td><img src="../images/mc.png"/></br>
      <img src="../images/tf.png"/></br></td>
         <tr><td><div class="help">2. Then save it as &lt;filename &gt;.csv(e.g question.csv)</br>
         3.Then go back to the system and click on the browse file button and open .csv file format</br>
         4. Finally, Click on Save button to Save excel file to database.</br>
         NB:The file extension should be .csv file format, Unless other file format should not be possible!</div></td></tr>
         <tr>
    	<td>Load Question Paper here:<input type="file" name="file" />
    	<select name="qtype" id="textbox">
    	<option value="<Select Question Type>">&lt;Select Question Type &gt;</option>
    	<option value="MCQ">MCQ</option>
    	<option value="TF">True/False</option>
    	</select>
    	</td>
        <!--<input type="submit" name="import" class="subbtn" value="Load File Into Database" /></td>-->
        </tr>
        </table>                      
  

<?php
                        } else if (isset($_REQUEST['edit'])) {
                            /*                             * ************************ Step 3 - Case 2 ************************ */
                            // To allow Editing Existing Question.
                            $result = executeQuery("select * from question where testid=" . $_SESSION['testqn'] . " and qnid=" . $_REQUEST['edit'] . ";");
                            if (mysql_num_rows($result) == 0) {
                                header('Location: prepqn.php');
                            } else if ($r = mysql_fetch_array($result)) {
                            	//to identify question type here...
                            	$qtype= htmlspecialchars_decode($r[htmlspecialchars_decode($r['correctanswer'], ENT_QUOTES)], ENT_QUOTES);
                            	   //echo $qtype;
                            	   //storing correct answer in session variable to identify question type
                            	   $_SESSION['cans']=$qtype;
                            	if(!($qtype=='TRUE'||$qtype=='FALSE')){
                                     //editing components
                                    ?>
                                <table cellpadding="20" cellspacing="20" style="text-align:left;margin-left:15em;" >
                                    <tr>
                                        <td >Question<input type="hidden" name="qnid" value="<?php echo $r['qnid']; ?>" /></td>
                                        <td><textarea name="question" cols="90" rows="3"  ><?php echo htmlspecialchars_decode($r['question'], ENT_QUOTES); ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Option A</td>
                                        <td><input type="text" name="optiona" value="<?php echo htmlspecialchars_decode($r['optiona'], ENT_QUOTES); ?>" size="80"  /></td>
                                    </tr>
                                    <tr>
                                        <td>Option B</td>
                                        <td><input type="text" name="optionb" value="<?php echo htmlspecialchars_decode($r['optionb'], ENT_QUOTES); ?>" size="80"  /></td>
                                    </tr>

                                    <tr>
                                        <td>Option C</td>
                                        <td><input type="text" name="optionc" value="<?php echo htmlspecialchars_decode($r['optionc'], ENT_QUOTES); ?>" size="80"  /></td>
                                    </tr>
                                    <tr>
                                        <td>Option D</td>
                                        <td><input type="text" name="optiond" value="<?php echo htmlspecialchars_decode($r['optiond'], ENT_QUOTES); ?>" size="80"  /></td>
                                    </tr>
                                    <tr>
                                        <td>Correct Answer</td>
                                        <td>
                                            <select name="correctans" id="textbox">
                                                <option value="optiona" <?php if (strcmp(htmlspecialchars_decode($r['correctanswer'], ENT_QUOTES), "optiona") == 0)
                                    echo "selected"; ?>>Option A</option>
                                                <option value="optionb" <?php if (strcmp(htmlspecialchars_decode($r['correctanswer'], ENT_QUOTES), "optionb") == 0)
                                    echo "selected"; ?>>Option B</option>
                                    <option value="optionc" <?php if (strcmp(htmlspecialchars_decode($r['correctanswer'], ENT_QUOTES), "optionc") == 0)
                                    echo "selected"; ?>>Option C</option>
                                    <option value="optiond" <?php if (strcmp(htmlspecialchars_decode($r['correctanswer'], ENT_QUOTES), "optiond") == 0)
                                    echo "selected"; ?>>Option D</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Marks</td>
                            <td><input type="text" name="marks" value="<?php echo htmlspecialchars_decode($r['marks'], ENT_QUOTES); ?>" size="30" onkeyup="isnum(this)" /></td>

                        </tr>
                        </table>
                        		
<?php
                                closedb();
                            }
                            else{
                        			?>
                        			<table cellpadding="20" cellspacing="20" style="text-align:left;margin-left:15em;" >
                                    <tr>
                                        <td >Question<input type="hidden" name="qnid" value="<?php echo $r['qnid']; ?>" /></td>
                                        <td><textarea name="question" cols="90" rows="3"  ><?php echo htmlspecialchars_decode($r['question'], ENT_QUOTES); ?></textarea></td>
                                    </tr>
                                    <tr>
                        			<td>Answer</td>
                                        <td>
                                            <select name="corrans" id="textbox">
                                                
                                    <option value="optionc" <?php if (strcmp(htmlspecialchars_decode($r['correctanswer'], ENT_QUOTES), "optionc") == 0)
                                    echo "selected"; ?>>TRUE</option>
                                    <option value="optiond" <?php if (strcmp(htmlspecialchars_decode($r['correctanswer'], ENT_QUOTES), "optiond") == 0)
                                    echo "selected"; ?>>FALSE</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Marks</td>
                            <td><input type="text" name="marks" value="<?php echo htmlspecialchars_decode($r['marks'], ENT_QUOTES); ?>" size="30" onkeyup="isnum(this)" /></td>

                        </tr>
                        
                    </table>
                   
  <?php
  							closedb();	
  							}
  							}
  						}

                        else {

                            /*                             * ************************ Step 3 - Case 3 ************************ */
                            // Defualt Mode: Displays the Existing Question/s, If any.
                            $result = executeQuery("select * from question where testid=" . $_SESSION['testqn'] . " order by qnid;");
                            if (mysql_num_rows($result) == 0) {
                                echo "<h3 style=\"color:#0000cc;text-align:center;\">No Questions Yet..!</h3>";
                            } else {
                                $i = 0;
?>
                                <table id="demoTable" cellpadding="30" cellspacing="10" class="datatable">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>Qn.No</th>
                                        <th>Question</th>
                                        <th>Correct Answer</th>
                                        <th>Marks</th>
                                        <th>Edit</th>
                                    </tr>
                                    </thead>
                                    <tbody>
<?php
                                while ($r = mysql_fetch_array($result)) {
                                    $i = $i + 1;
                                    if ($i % 2 == 0)
                                        echo "<tr class=\"alt\">";
                                    else
                                        echo "<tr>";
                                    echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"d$i\" value=\"" . $r['qnid'] . "\" /></td><td> " . $i
                                    . "</td><td>" . htmlspecialchars_decode($r['question'], ENT_QUOTES) . "</td><td>" . htmlspecialchars_decode($r[htmlspecialchars_decode($r['correctanswer'], ENT_QUOTES)], ENT_QUOTES) . "</td><td>" . htmlspecialchars_decode($r['marks'], ENT_QUOTES) . "</td>"
                                    . "<td class=\"tddata\"><a title=\"Edit " . $r['qnid'] . "\"href=\"prepqn.php?edit=" . $r['qnid'] . "\"><img src=\"../images/edit.png\" height=\"30\" width=\"40\" alt=\"Edit\" /></a>"
                                    . "</td></tr>";
                                }
?>
                   </tbody>
				<tfoot class="nav">
               		 <tr>
                        <td colspan=6 ><font color="#000000">
                                <div class="pagination"></div>
                                <div class="paginationTitle">Page</div>
                                <div class="selectPerPage"></div>
                                <div class="status"></div>
								</font>
                        </td>
                </tr>
        </tfoot>
                    </table>
<?php
                }
                closedb();
            }
        }
?>

                </div>
            </form>
            <div id="footer">
                <?php include'../include/footer.php';?>  
                
                </div>
        </div>
    </body>
</html>
