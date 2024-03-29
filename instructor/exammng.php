
<?php
/*
 * ***************************************************
 * ** Examination Management System                ***
 * **--------------------------------------------- ***
 * ** Developer: Dejene Techane                    ***
 * ** Title: Exam Management(Add,delete,Modify)    ***
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
  Case 2 : Dashboard - redirect to Dashboard
  Case 3 : Delete - Delete the selected Exam/s from System.
  Case 4 : Edit - Update the new information.
  Case 5 : Add - Add new Exam to the system.
  Case 6 : Manage Questions - Store the Exam identity in session varibles and redirect to prepare question section.

 * ------------ *
 * HTML Section *
 * ------------ *

  Step 3: Display the HTML Components for...
  Case 1: Add - Form to receive new Exam information.
  Case 2: Edit - Form to edit Existing Exam Information.
  Case 3: Default Mode - Displays the Information of Existing Exam, If any.
 * ********************************************
 */

error_reporting(0);
session_start();
include_once '../include/emsdb.php';
/* * ************************ Step 1 ************************ */
if (!isset($_SESSION['username'])) {
    $_GLOBALS['message'] = "Session Timeout.Click here to <a href=\"../index.php\">Re-LogIn</a>";
} 
//This is to protect unauthorized users
 else if(!($_SESSION['role']=="Instructor")){
    unset($_SESSION['username']);
  $_GLOBALS['message']="Please, You are accessing unauthorized page.Click here to <a href=\"../index.php\">Re-LogIn</a>";
 }
else if (isset($_REQUEST['logout'])) {
    /*     * ************************ Step 2 - Case 1 ************************ */
    //Log out and redirect login page
    unset($_SESSION['username']);
    header('Location: ../index.php');
} else if (isset($_REQUEST['dashboard'])) {
    /*     * ************************ Step 2 - Case 2 ************************ */
    //redirect to dashboard
    header('Location: inswelcome.php');
} else if (isset($_REQUEST['delete'])) { /* * ************************ Step 2 - Case 3 ************************ */
    //deleting the selected Tests
    unset($_REQUEST['delete']);
    $hasvar = false;
    foreach ($_REQUEST as $variable) {
        if (is_numeric($variable)) { //it is because, some session values are also passed with request
            $hasvar = true;

            if (!@executeQuery("delete from test where testid=$variable and uid=" . $_SESSION['uid'] . ";")) {
                if (mysql_errno () == 1451) //Children are dependent value
                    $_GLOBALS['message'] = "Too Prevent accidental deletions, system will not allow propagated deletions.<br/><b>Help:</b> If you still want to delete this Test, then first delete the questions that are assoicated with it.";
                else
                    $_GLOBALS['message'] = mysql_errno();
            }
        }
    }
    if (!isset($_GLOBALS['message']) && $hasvar == true)
        $_GLOBALS['message'] = "Selected Exam are successfully Deleted";
    else if (!$hasvar) {
        $_GLOBALS['message'] = "First Select the Exam to be Deleted.";
    }
} else if (isset($_REQUEST['savem'])) {
    /*     * ************************ Step 2 - Case 4 ************************ */
    //updating the modified values
   
    $fromtime = $_REQUEST['testfrom'] . " " . date("H:i:s");
    $totime = $_REQUEST['testto'] . " 23:59:59";
    $_GLOBALS['message'] = strtotime($totime) . "  " . strtotime($fromtime) . "  " . time();
    if (strtotime($fromtime) > strtotime($totime) || strtotime($totime) < time())
        $_GLOBALS['message'] = "Start date of test is less than end date or last date of exam is less than today's date.<br/>Therefore Nothing is Updated";
    else if (empty($_REQUEST['testname']) || empty($_REQUEST['testdesc']) || empty($_REQUEST['totalqn']) || empty($_REQUEST['duration']) || empty($_REQUEST['testfrom']) || empty($_REQUEST['testto']) || empty($_REQUEST['testcode'])) {
        $_GLOBALS['message'] = "Some of the required Fields are Empty.Therefore Nothing is Updated";
    } else {
    $rslt=executeQuery("select max(qnid) as qn from question where testid='".$_SESSION['testqn']."';");
    $r=mysql_fetch_array($rslt);
    $q=$r['qn'];
    $tqn=htmlspecialchars($_REQUEST['totalqn'], ENT_QUOTES) ;
    if($tqn >= $q)
    {
        $query = "update test set testname='" . htmlspecialchars($_REQUEST['testname'], ENT_QUOTES) . "',testdesc='" . htmlspecialchars($_REQUEST['testdesc'], ENT_QUOTES) . "',cid=" . $_REQUEST['course'] . ",testfrom='" . $fromtime . "',testto='" . $totime . "',duration=" . htmlspecialchars($_REQUEST['duration'], ENT_QUOTES) . ",totalquestions=" . htmlspecialchars($_REQUEST['totalqn'], ENT_QUOTES) . ",testcode=ENCODE('" . htmlspecialchars($_REQUEST['testcode'], ENT_QUOTES) . "','emspass') where testid=" . $_REQUEST['testid'] . " and uid=" . $_SESSION['uid'] . ";";
        if (!@executeQuery($query))
            $_GLOBALS['message'] = mysql_error();
        else
            $_GLOBALS['message'] = "Exam Information is Successfully Updated.";
    }
    else
    {
    	$_GLOBALS['message']="Please, Total Question must be greater or equal to Maximum Question you added in this exam."; 
    }
    }
    closedb();
}
else if (isset($_REQUEST['savea'])) {
    /*     * ************************ Step 2 - Case 5 ************************ */
    //Add the new Test information in the database
    $noerror = true;
    $fromtime = $_REQUEST['testfrom'] . " " . date("H:i:s");
    $totime = $_REQUEST['testto'] . " 23:59:59";
    if (strtotime($fromtime) > strtotime($totime) || strtotime($fromtime) < (time() - 3600)) {
        $noerror = false;
        $_GLOBALS['message'] = "Start date of exam is either less than today's date or greater than last date of exam.";
    } else if ((strtotime($totime) - strtotime($fromtime)) <= 3600 * 24) {
        $noerror = true;
        $_GLOBALS['message'] = "Note:<br/>The exam is valid upto " . date(DATE_RFC850, strtotime($totime));
    }
    //$_GLOBALS['message']="time".date_format($first, DATE_ATOM)."<br/>time ".date_format($second, DATE_ATOM);


    $result = executeQuery("select max(testid) as tst from test");
    $r = mysql_fetch_array($result);
    if (is_null($r['tst']))
        $newstd = 1;
    else
        $newstd=$r['tst'] + 1;

    // $_GLOBALS['message']=$newstd;
    if (strcmp($_REQUEST['course'], "<Choose the Course>") == 0 || empty($_REQUEST['testname']) || empty($_REQUEST['testdesc']) || empty($_REQUEST['totalqn']) || empty($_REQUEST['duration']) || empty($_REQUEST['testfrom']) || empty($_REQUEST['testto']) || empty($_REQUEST['testcode'])) {
        $_GLOBALS['message'] = "Some of the required Fields are Empty";
    } else if ($noerror) {
        $query = "insert into test values($newstd,'" . htmlspecialchars($_REQUEST['testname'], ENT_QUOTES) . "','" . htmlspecialchars($_REQUEST['testdesc'], ENT_QUOTES) . "',(select curDate()),(select curTime())," . $_REQUEST['course'] . ",'" . $fromtime . "','" . $totime . "'," . htmlspecialchars($_REQUEST['duration'], ENT_QUOTES) . "," . htmlspecialchars($_REQUEST['totalqn'], ENT_QUOTES) . ",0,ENCODE('" . htmlspecialchars($_REQUEST['testcode'], ENT_QUOTES) . "','emspass')," . $_SESSION['uid'] . ")";
        if (!@executeQuery($query)) {
            if (mysql_errno () == 1062) //duplicate value
                $_GLOBALS['message'] = "Given Test Name voilates some constraints, please try with some other name.";
            else
                $_GLOBALS['message'] = mysql_error();
        }
        else
            $_GLOBALS['message'] = $_GLOBALS['message'] . "<br/>Successfully New Exam is Created.";
    }
    closedb();
}
else if (isset($_REQUEST['manageqn'])) {
    /*     * ************************ Step 2 - Case 6 ************************ */
    //Store the Test identity in session varibles and redirect to prepare question section.
    //$tempa=explode(" ",$_REQUEST['testqn']);
    // $testname=substr($_REQUEST['manageqn'],0,-10);
    $testname = $_REQUEST['manageqn'];
    $result = executeQuery("select testid from test where testname='$testname' and uid=" . $_SESSION['uid'] . ";");

    if ($r = mysql_fetch_array($result)) {
        $_SESSION['testname'] = $testname;
        $_SESSION['testqn'] = $r['testid'];
        //  $_GLOBALS['message']=$_SESSION['testname'];
        header('Location: prepqn.php');
    }
}
?>
<html>
    <head>
        <title>EMS-Manage Exam</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="icon" type="jpg/png" href="../images/logo.png"/>
        <link rel="stylesheet" type="text/css" href="../ems.css"/>  
    
        <link rel="stylesheet" type="text/css" media="all" href="../calendar/jsDatePick.css" />
        <script type="text/javascript" src="../calendar/jsDatePick.full.1.1.js"></script>
        <script type="text/javascript">
            window.onload = function(){
                new JsDatePick({
                    useMode:2,
                    target:"testfrom"
                    //limitToToday:true <-- Add this should you want to limit the calendar until today.
                });

                new JsDatePick({
                    useMode:2,
                    target:"testto"
                    //limitToToday:true <-- Add this should you want to limit the calendar until today.
                });
            };
        </script>

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
            <form name="testmng" action="exammng.php" method="post">
                <div class="menubar">


                    <ul id="menu">
<?php
if (isset($_SESSION['username'])) {
    // Navigations
?>
                        <li><input type="submit" value="LogOut" name="logout" class="subbtn" title="Log Out"/></li>
                        <li><input type="submit" value="DashBoard" name="dashboard" class="subbtn" title="Dash Board"/></li>

<?php
    //navigation for Add option
    if (isset($_REQUEST['add'])) {
?>
                        <li><input type="submit" value="Cancel" name="cancel" class="subbtn" title="Cancel"/></li>
                        <li><input type="submit" value="Save" name="savea" class="subbtn" onclick="validatetestform('testmng')" title="Save the Changes"/></li>

<?php
    } else if (isset($_REQUEST['edit'])) { //navigation for Edit option
?>
                        <li><input type="submit" value="Cancel" name="cancel" class="subbtn" title="Cancel"/></li>
                        <li><input type="submit" value="Save" name="savem" class="subbtn" onclick="validatetestform('testmng')" title="Save the changes"/></li>

<?php
    } else {  //navigation for Default
?>
                        <li><input type="submit" value="Delete" name="delete" class="subbtn" title="Delete" onclick="return confirm('Are you sure to delete this record?')"/></li>
                        <li><input type="submit" value="Add" name="add" class="subbtn" title="Add"/></li>
                         <?php
               $result=executeQuery("select * from instructor where idno='".$_SESSION['username']."';");
                $r=mysql_fetch_array($result);
                closedb();?>
                <input type="text" class="subbtn" size=50 value="Instructor Name: <?php echo htmlspecialchars_decode($r['fullname'], ENT_QUOTES);?>" readonly>
<?php }
} ?>
                    </ul>

                </div>
                <div class="page">
<?php
if (isset($_SESSION['username'])) {
    // To display the Help Message;
                
      echo "<div class=\"pmsg\" style=\"text-align:center;\">Exam Management </div>";
        echo "<div class=\"pmsg\" style=\"text-align:center\"> Which exam questions Do you want to Manage? <br/><b>Help:</b>Click on Questions button to manage the questions of respective exam</div>";
    if (isset($_REQUEST['add'])) {
        /*         * ************************ Step 3 - Case 1 ************************ */
       $dept=$_SESSION['dept'];
        //Form for the new Test
?>
			
                                    
                    <table cellpadding="20" cellspacing="20" style="text-align:left;" >
                        <tr>
                            <td>Course Name</td>
                            <td>
                                <select name="course" id="textbox">
                                    <option selected value="<Choose the Course>">&lt;Choose the Course&gt;</option>
<?php
        $result = executeQuery("select * from course where dept='$dept' order by cid;");
        
        while ($r = mysql_fetch_array($result)) {

            echo "<option value=\"" . $r['cid'] . "\">" . htmlspecialchars_decode($r['cname'], ENT_QUOTES) . "[".$r['ccode']."]</option>";
        }
        closedb();
?>
                                </select>
                            </td>

                        </tr>
                        <tr>
                            <td>Exam Name</td>
                            <td><input type="text" name="testname" value="" size="16" onkeyup="isalphanum(this)" /></td>
                            <td colspan=20><div class="help"><b>Note:</b><br/>Exam Name must be Unique<br/> in order to identify different<br/> exam on same course.</div></td>
                        </tr>
                        <tr>
                            <td>Exam Description</td>
                            <td><textarea name="testdesc" cols="20" rows="3" ></textarea></td>
                            <td><div class="help"><b>Describe here:</b><br/>What the exam is all about?</div></td>
                        </tr>
                        <tr>
                            <td>Total Questions</td>
                            <td><input type="text" name="totalqn" value="" size="16" onkeyup="isnum(this)" /></td>

                        </tr>
                        <tr>
                            <td>Duration(Mins)</td>
                            <td><input type="text" name="duration" value="" size="16" onkeyup="isnum(this)" /></td>

                        </tr>
                        <tr>
                            <td>Exam From </td>
                            <td><input id="testfrom" type="text" name="testfrom" value="" size="16" readonly /></td>
                        </tr>
                        <tr>
                            <td>Exam To </td>
                            <td><input id="testto" type="text" name="testto" value="" size="16" readonly /></td>
                        </tr>

                        <tr>
                            <td>Exam Secret Code</td>
                            <td><input type="text" name="testcode" value="" size="16" onkeyup="isalphanum(this)" /></td>
                            <td><div class="help"><b>Note:</b><br/>Candidates must enter<br/>this code in order to <br/> take the exam</div></td>
                        </tr>

                    </table>

<?php
    } else if (isset($_REQUEST['edit'])) {
        /*         * ************************ Step 3 - Case 2 ************************ */
        // To allow Editing Existing Test.
        $result = executeQuery("select t.totalquestions,t.duration,t.testid,t.testname,t.testdesc,t.cid,c.ccode,c.cname,DECODE(t.testcode,'emspass') as tcode,DATE_FORMAT(t.testfrom,'%Y-%m-%d') as testfrom,DATE_FORMAT(t.testto,'%Y-%m-%d') as testto from test as t,course as c where t.cid=c.cid and t.testname='" . htmlspecialchars($_REQUEST['edit'], ENT_QUOTES) . "' and t.uid=" . $_SESSION['uid'] . ";");
        if (mysql_num_rows($result) == 0) {
            header('Location: exammng.php');
        } else if ($r = mysql_fetch_array($result)) {


            //editing components
?>
                    <table cellpadding="20" cellspacing="20" style="text-align:left;margin-left:15em" >
                        <tr>
                            <td>Course Name</td>
                            <td>
                                <select name="course" id="textbox">
<?php
            $result = executeQuery("select cid,ccode,cname from course where dept='".$_SESSION['dept']."';");
            while ($r1 = mysql_fetch_array($result)) {
                if (strcmp(htmlspecialchars_decode($r['cname'], ENT_QUOTES), htmlspecialchars_decode($r1['cname'], ENT_QUOTES)) == 0)
                    echo "<option value=\"" . $r1['cid'] . "\" selected>" . htmlspecialchars_decode($r1['cname'], ENT_QUOTES) . "</option>";
                else
                    echo "<option value=\"" . $r1['cid'] . "\">" . htmlspecialchars_decode($r1['cname'], ENT_QUOTES) . "</option>";
            }
            closedb();
?>
                                </select>
                            </td>

                        </tr>
                        <tr>
                            <td>Exam Name</td>
                            <td><input type="hidden" name="testid" value="<?php echo $r['testid']; ?>"/><input type="text" name="testname" value="<?php echo htmlspecialchars_decode($r['testname'], ENT_QUOTES); ?>" size="16" onkeyup="isalphanum(this)" /></td>
                            <td><div class="help"><b>Note:</b><br/>Exam Name must be Unique<br/> in order to identify different<br/> exam on same course.</div></td>
                        </tr>
                        <tr>
                            <td>Exam Description</td>
                            <td><textarea name="testdesc" cols="20" rows="3" ><?php echo htmlspecialchars_decode($r['testdesc'], ENT_QUOTES); ?></textarea></td>
                            <td><div class="help"><b>Describe here:</b><br/>What the exam is all about?</div></td>
                        </tr>
                        <tr>
                            <td>Total Questions</td>
                            <td><input type="text" name="totalqn" value="<?php echo htmlspecialchars_decode($r['totalquestions'], ENT_QUOTES); ?>" size="16" onkeyup="isnum(this)" /></td>

                        </tr>
                        <tr>
                            <td>Duration(Mins)</td>
                            <td><input type="text" name="duration" value="<?php echo htmlspecialchars_decode($r['duration'], ENT_QUOTES); ?>" size="16" onkeyup="isnum(this)" /></td>

                        </tr>
                        <tr>
                            <td>Exam From </td>
                            <td><input id="testfrom" type="text" name="testfrom" value="<?php echo $r['testfrom']; ?>" size="16" readonly /></td>
                        </tr>
                        <tr>
                            <td>Exam To </td>
                            <td><input id="testto" type="text" name="testto" value="<?php echo $r['testto']; ?>" size="16" readonly /></td>
                        </tr>

                        <tr>
                            <td>Exam Secret Code</td>
                            <td><input type="text" name="testcode" value="<?php echo htmlspecialchars_decode($r['tcode'], ENT_QUOTES); ?>" size="16" onkeyup="isalphanum(this)" /></td>
                            <td><div class="help"><b>Note:</b><br/>Candidates must enter<br/>this code in order to <br/> take the exam</div></td>
                        </tr>

                    </table>
<?php
                                    closedb();
                                }
                            }

                            else {

                                /*                                 * ************************ Step 3 - Case 3 ************************ */
                                // Defualt Mode: Displays the Existing Test/s, If any.
                                $result = executeQuery("select t.testid,t.testname,t.testdesc,c.cname,DECODE(t.testcode,'emspass') as tcode,DATE_FORMAT(t.testfrom,'%d-%M-%Y') as testfrom,DATE_FORMAT(t.testto,'%d-%M-%Y %H:%i:%s %p') as testto from test as t,course as c where t.cid=c.cid and dept='".$_SESSION['dept']."' and t.uid=" . $_SESSION['uid'] . " order by t.testdate desc,t.testtime desc;");
                               
                                if (mysql_num_rows($result) == 0) {
                                    echo "<h3 style=\"color:#0000cc;text-align:center;\">No Exam Yet..!</h3>";
                                } else {
                                    $i = 0;
?>
                                    <table cellpadding="30" cellspacing="10" class="datatable">
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>No.</th>
                                            <th>Exam Description</th>
                                            <th>Course Name</th>
                                            <th>Exam Secret Code</th>
                                            <th>Validity</th>
                                            <th>Edit</th>
                                            <th style="text-align:center;">Manage<br/>Questions</th>
                                        </tr>
<?php
                                    while ($r = mysql_fetch_array($result)) {
                                        $i = $i + 1;
                                        if ($i % 2 == 0)
                                            echo "<tr class=\"alt\">";
                                        else
                                            echo "<tr>";
                                        echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"d$i\" value=\"" . $r['testid'] . "\" /></td><td>$i</td><td> " . htmlspecialchars_decode($r['testname'], ENT_QUOTES) . " : " . htmlspecialchars_decode($r['testdesc'], ENT_QUOTES)
                                        . "</td><td>" . htmlspecialchars_decode($r['cname'], ENT_QUOTES) . "</td><td>" . htmlspecialchars_decode($r['tcode'], ENT_QUOTES) . "</td><td>" . $r['testfrom'] . " To " . $r['testto'] . "</td>"
                                        . "<td class=\"tddata\"><a title=\"Edit " . htmlspecialchars_decode($r['testname'], ENT_QUOTES) . "\"href=\"exammng.php?edit=" . htmlspecialchars_decode($r['testname'], ENT_QUOTES) . "\"><img src=\"../images/edit.png\" height=\"30\" width=\"40\" alt=\"Edit\" /></a></td>"
                                        . "<td class=\"tddata\"><a title=\"Manage Questions of " . htmlspecialchars_decode($r['testname'], ENT_QUOTES) . "\"href=\"exammng.php?manageqn=" . htmlspecialchars_decode($r['testname'], ENT_QUOTES) . "\"><img src=\"../images/mngqn.png\" height=\"30\" width=\"40\" alt=\"Manage Questions\" /></a></td></tr>";
                                    }
?>
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