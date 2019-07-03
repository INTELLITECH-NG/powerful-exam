


<?php
/*
 * **************************************************
 * ** Examination Management System               ***
 * **---------------------------------------------***
 * ** Developer: Dejene Techane                   ***
 * ** Title:  Exam Environment Preparation        ***
 * **************************************************
 */

error_reporting(0);
session_start();
include_once '../include/emsdb.php';
if (!isset($_SESSION['username'])) {
    $_GLOBALS['message'] = "Session Timeout.Click here to <a href=\"../index.php\">Re-LogIn</a>";
} 
//This is to protect unauthorized users
 else if(!($_SESSION['role']=="Student")){
    unset($_SESSION['username']);
  $_GLOBALS['message']="Please, You are accessing unauthorized page.Click here to <a href=\"../index.php\">Re-LogIn</a>";
 }else if (isset($_SESSION['starttime'])) {
    header('Location: examconducter.php');
} else if (isset($_REQUEST['logout'])) {
    //Log out and redirect login page
    unset($_SESSION['username']);
    header('Location: ../index.php');
} else if (isset($_REQUEST['dashboard'])) {
    //redirect to dashboard
    //
    header('Location: stdwelcome.php');
} else if (isset($_REQUEST['starttest'])) {
    //Prepare the parameters needed for Test Conducter and redirect to test conducter
    if (!empty($_REQUEST['tc'])) {
        $result = executeQuery("select DECODE(testcode,'emspass') as tcode from test where testid=" . $_SESSION['testid'] . ";");

        if ($r = mysql_fetch_array($result)) {
            if (strcmp(htmlspecialchars_decode($r['tcode'], ENT_QUOTES), htmlspecialchars($_REQUEST['tc'], ENT_QUOTES)) != 0) {
                $display = true;
                $_GLOBALS['message'] = "You have entered an Invalid Exam Code.Try again.";
            } else {
                //now prepare parameters for Exam Conducter and redirect to it.
                //first step: Insert the questions into table

                $result = executeQuery("select * from question where testid=" . $_SESSION['testid'] . " order by qnid;");
                if (mysql_num_rows($result) == 0) {
                    $_GLOBALS['message'] = "Exam questions cannot be selected.Please Try after some time!";
                } else {
                  //  executeQuery("COMMIT");
                    $error = false;
                //    executeQuery("delimiter |");
                 /*   if (!executeQuery("create event " . $_SESSION['stdname'] . time() . "
ON SCHEDULE AT (select endtime from studenttest where stdid=" . $_SESSION['stdid'] . " and testid=" . $_SESSION['testid'] . ") + INTERVAL (select duration from test where testid=" . $_SESSION['testid'] . ") MINUTE
DO update studenttest set correctlyanswered=(select count(*) from studentquestion as sq,question as q where sq.qnid=q.qnid and sq.testid=q.testid and sq.answered='answered' and sq.stdanswer=q.correctanswer and sq.stdid=" . $_SESSION['stdid'] . " and sq.testid=" . $_SESSION['testid'] . "),status='over' where stdid=" . $_SESSION['stdid'] . " and testid=" . $_SESSION['testid'] . "|"))
                        $_GLOBALS['message'] = "error" . mysql_error();
                    executeQuery("delimiter ;");*/
                    if (!executeQuery("insert into studenttest values(" . $_SESSION['uid'] . "," . $_SESSION['testid'] . ",(select CURRENT_TIMESTAMP),date_add((select CURRENT_TIMESTAMP),INTERVAL (select duration from test where testid=" . $_SESSION['testid'] . ") MINUTE),0,'inprogress')"))
                        $_GLOBALS['message'] = "error" . mysql_error();
                    else {
                        while ($r = mysql_fetch_array($result)) {
                            if (!executeQuery("insert into studentquestion values(" . $_SESSION['uid'] . "," . $_SESSION['testid'] . "," . $r['qnid'] . ",'unanswered',NULL)")) {
                                $_GLOBALS['message'] = "Failure while preparing questions for you.Try again".mysql_error();
                                $error = true;
                            }
                        }
                        if ($error == true) {
                      //      executeQuery("rollback;");
                        } else {
                            $result = executeQuery("select totalquestions,duration from test where testid=" . $_SESSION['testid'] . ";");
                            $r = mysql_fetch_array($result);
                            $_SESSION['tqn'] = htmlspecialchars_decode($r['totalquestions'], ENT_QUOTES);
                            $_SESSION['duration'] = htmlspecialchars_decode($r['duration'], ENT_QUOTES);
                            $result = executeQuery("select DATE_FORMAT(starttime,'%Y-%m-%d %H:%i:%s') as startt,DATE_FORMAT(endtime,'%Y-%m-%d %H:%i:%s') as endt from studenttest where testid=" . $_SESSION['testid'] . " and uid=" . $_SESSION['uid'] . ";");
                            $r = mysql_fetch_array($result);
                            $_SESSION['starttime'] = $r['startt'];
                            $_SESSION['endtime'] = $r['endt'];
                            $_SESSION['qn'] = 1;
                            header('Location: examconducter.php');
                        }
                    }
                }
            }
        } else {
            $display = true;
            $_GLOBALS['message'] = "You have entered an Invalid Exam Code.Try again.";
        }
    } else {
        $display = true;
        $_GLOBALS['message'] = "Enter the Exam Code First!";
    }
} else if (isset($_REQUEST['testcode'])) {
    //test code preparation
    if ($r = mysql_fetch_array($result = executeQuery("select testid from test where testname='" . htmlspecialchars($_REQUEST['testcode'], ENT_QUOTES) . "';"))) {
        $_SESSION['testname'] = $_REQUEST['testcode'];
        $_SESSION['testid'] = $r['testid'];
    }
     closedb();
} 
   

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title>EMS-Offered Exam</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
         <link rel="icon" type="jpg/png" href="../images/logo.png"/>
        <meta http-equiv="CACHE-CONTROL" content="NO-CACHE"/>
        <meta http-equiv="PRAGMA" content="NO-CACHE"/>
        <meta name="ROBOTS" content="NONE"/>

        <link rel="stylesheet" type="text/css" href="../ems.css"/>
        <script type="text/javascript" src="../validate.js" ></script>
    </head>
    <body >
        <?php
        if ($_GLOBALS['message']) {
            echo "<div class=\"message\">" . $_GLOBALS['message'] . "</div>";
        }
        ?>
        <div id="container">
             <div class="header">                              
                <?php require'../include/header.php';?>                
                </div>
            <form id="stdtest" action="stdexam.php" method="post">
                <div class="menubar">
                    <ul id="menu">
                        <?php
                        if (isset($_SESSION['username'])) {
                            // Navigations
                        ?>
                            <li><input type="submit" value="LogOut" name="logout" class="subbtn" title="Log Out"/></li>
                            <li><input type="submit" value="DashBoard" name="dashboard" class="subbtn" title="Dash Board"/></li>
              <?php
               $result=executeQuery("select * from student where idno='".$_SESSION['username']."';");
                $r=mysql_fetch_array($result);
                closedb();
                ?>
                <input type="" class="subbtn" value="Name: <?php echo htmlspecialchars_decode($r['fullname'], ENT_QUOTES);?>">


                        </ul>
                    </div>
                    <div class="page">
                    <?php
                            if (isset($_REQUEST['testcode'])) {
                                echo "<div class=\"pmsg\" style=\"text-align:center;\">What is the Code of " . $_SESSION['testname'] . " ? </div>";
                            } else {
                                echo "<div class=\"pmsg\" style=\"text-align:center;\">Offered Exam</div>";
                            }
                    ?>
                    <?php
                            if (isset($_REQUEST['testcode']) || $display == true) {
                    ?>
                                <table cellpadding="30" cellspacing="10">
                                    <tr>
                                        <td>Enter Exam Code</td>
                                        <td><input type="text" tabindex="1" name="tc" value="" size="16" /></td>
                                        <td><div class="help"><b>Note:</b><br/>Once you press start Exam<br/>button timer will be started</div></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <input type="submit" tabindex="3" value="Start Exam" name="starttest" class="ssubbtn" />
                                        </td>
                                    </tr>
                                </table>


                    <?php
                            } else {
                                $result = executeQuery("select t.*,c.cname from test as t, course as c where c.cid=t.cid and CURRENT_TIMESTAMP<t.testto and t.totalquestions=(select count(*) from question where testid=t.testid) and NOT EXISTS(select uid,testid from studenttest where testid=t.testid and uid=" . $_SESSION['uid'] . ");");
                                if (mysql_num_rows($result) == 0) {
                                    echo"<h3 style=\"color:#0000cc;text-align:center;\">Sorry...! For this moment, You have not Offered to take any Exam.</h3>";
                                } else {
                                    //editing components
                    ?>
                                    <table cellpadding="30" cellspacing="10" class="datatable">
                                        <tr>
                                            <th>Exam Name</th>
                                            <th>Exam Description</th>
                                            <th>Course Name</th>
                                            <th>Duration</th>
                                            <th>Total Questions</th>
                                            <th>Take Exam</th>
                                        </tr>
                        <?php
                                    while ($r = mysql_fetch_array($result)) {
                                        $i = $i + 1;
                                        if ($i % 2 == 0) {
                                            echo "<tr class=\"alt\">";
                                        } else {
                                            echo "<tr>";
                                        }
                                        echo "<td>" . htmlspecialchars_decode($r['testname'], ENT_QUOTES) . "</td><td>" . htmlspecialchars_decode($r['testdesc'], ENT_QUOTES) . "</td><td>" . htmlspecialchars_decode($r['cname'], ENT_QUOTES)
                                        . "</td><td>" . htmlspecialchars_decode($r['duration'], ENT_QUOTES) . "</td><td>" . htmlspecialchars_decode($r['totalquestions'], ENT_QUOTES) . "</td>"
                                        . "<td class=\"tddata\"><a title=\"Start Exam\" href=\"stdexam.php?testcode=" . htmlspecialchars_decode($r['testname'], ENT_QUOTES) . "\"><img src=\"../images/starttest.png\" height=\"30\" width=\"40\" alt=\"Start Exam\" /></a></td></tr>";
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

