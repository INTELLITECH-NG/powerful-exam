


<?php

/*
***************************************************
*** Examination Management System               ***
***---------------------------------------------***
*** Developer: Dejene Techane                   ***
*** Title: View Results                         ***
***************************************************
*/

error_reporting(0);
session_start();
include_once '../include/emsdb.php';
if(!isset($_SESSION['username'])) {
    $_GLOBALS['message']="Session Timeout.Click here to <a href=\"../index.php\">Re-LogIn</a>";
}
 //This is to protect unauthorized users
 else if(!($_SESSION['role']=="Student")){
    unset($_SESSION['username']);
  $_GLOBALS['message']="Please, You are accessing unauthorized page.Click here to <a href=\"../index.php\">Re-LogIn</a>";
 }
else if(isset($_REQUEST['logout'])) {
    //Log out and redirect login page
        unset($_SESSION['username']);
        header('Location: ../index.php');

    }
    else if(isset($_REQUEST['back'])) {
        //redirect to View Result

            header('Location: viewresult.php');

        }
        else if(isset($_REQUEST['dashboard'])) {
        //redirect to dashboard

            header('Location: stdwelcome.php');

        }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title>EMS-View Result</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="icon" type="jpg/png" href="../images/logo.png"/>
        <meta http-equiv="CACHE-CONTROL" content="NO-CACHE"/>
        <meta http-equiv="PRAGMA" content="NO-CACHE"/>
        <meta name="ROBOTS" content="NONE"/>
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
        <link rel="stylesheet" type="text/css" href="../ems.css"/>
        <script type="text/javascript" src="../validate.js" ></script>
        
    </head>
    <body >
        <?php

        if($_GLOBALS['message']) {
            echo "<div class=\"message\">".$_GLOBALS['message']."</div>";
        }
        ?>
        <div id="container">
             <div class="header">                              
                <?php require'../include/header.php';?>                
                </div>
            <form id="summary" action="viewresult.php" method="post">
                <div class="menubar">
                    <ul id="menu">
                        <?php if(isset($_SESSION['username'])) {
                        // Navigations
                        if(isset($_REQUEST['details'])) {
              ?>
                        <li><input type="submit" value="LogOut" name="logout" class="subbtn" title="Log Out"/></li>
                        <li><input type="submit" value="Back" name="back" class="subbtn" title="View Results"/></li>
                       <li><input type="submit" value="DashBoard" name="dashboard" class="subbtn" title="Dash Board"/></li>
                                     <?php
               $result=executeQuery("select * from student where idno='".$_SESSION['username']."';");
                $r=mysql_fetch_array($result);
                closedb();
                ?>
                <li><input type="" class="subbtn" value="Name: <?php echo htmlspecialchars_decode($r['fullname'], ENT_QUOTES);?>"></li>


                        <?php
                        }
                        else
                        {
                            ?>
                        <li><input type="submit" value="LogOut" name="logout" class="subbtn" title="Log Out"/></li>
                        <li><input type="submit" value="DashBoard" name="dashboard" class="subbtn" title="Dash Board"/></li>
                                      <?php
               $result=executeQuery("select * from student where idno='".$_SESSION['username']."';");
                $r=mysql_fetch_array($result);
                closedb();
                ?>
                <input type="" class="subbtn" value="Name: <?php echo htmlspecialchars_decode($r['fullname'], ENT_QUOTES);?>">

                        <?php
                        }
                        ?>

                    </ul>


                </div>
                <div class="page">

                        <?php

                        if(isset($_REQUEST['details'])) {
                            $result=executeQuery("select u.username,t.testname,t.testdesc,c.cname,DATE_FORMAT(st.starttime,'%d %M %Y %H:%i:%s') as stime,TIMEDIFF(st.endtime,st.starttime) as dur,(select sum(marks) from question where testid=".$_REQUEST['details'].") as tm,IFNULL((select sum(q.marks) from studentquestion as sq, question as q where sq.testid=q.testid and sq.qnid=q.qnid and sq.answered='answered' and sq.stdanswer=q.correctanswer and sq.uid=".$_SESSION['uid']." and sq.testid=".$_REQUEST['details']."),0) as om from user as u,test as t, course as c,studenttest as st where u.uid=st.uid and st.testid=t.testid and t.cid=c.cid and st.uid=".$_SESSION['uid']." and st.testid=".$_REQUEST['details'].";") ;
                            if(mysql_num_rows($result)!=0) {

                                $r=mysql_fetch_array($result);
                                ?>
                    <table cellpadding="20" cellspacing="30" border="0" style="background:#ffffff url(../images/page.gif);text-align:left;line-height:20px;">
                        <tr>
                            <td colspan="2"><h3 style="color:#0000cc;text-align:center;">Exam Summary</h3></td>
                        </tr>
                        <tr>
                            <td colspan="2" ><hr style="color:#ff0000;border-width:4px;"/></td>
                        </tr>
                        <tr>
                            <td>IDNO:</td>
                            <td><?php echo htmlspecialchars_decode($r['username'],ENT_QUOTES); ?></td>
                        </tr>
                        <tr>
                            <td>Exam</td>
                            <td><?php echo htmlspecialchars_decode($r['testname'],ENT_QUOTES).":".htmlspecialchars_decode($r['testdesc'],ENT_QUOTES); ?></td>
                        </tr>
                        <tr>
                            <td>Course</td>
                            <td><?php echo htmlspecialchars_decode($r['cname'],ENT_QUOTES); ?></td>
                        </tr>
                        <tr>
                            <td>Date and Time</td>
                            <td><?php echo $r['stime']; ?></td>
                        </tr>
                        <tr>
                            <td>Exam Duration</td>
                            <td><?php echo $r['dur']; ?></td>
                        </tr>
                        <tr>
                            <td>Max. Marks</td>
                            <td><?php echo $r['tm']; ?></td>
                        </tr>
                        <tr>
                            <td>Obtained Marks</td>
                            <td><?php echo $r['om']; ?></td>
                        </tr>
                        <tr>
                            <td>Percentage</td>
                            <td><?php echo (($r['om']/$r['tm'])*100)." %"; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" ><hr style="color:#ff0000;border-width:2px;"/></td>
                        </tr>
                         <tr>
                            <td colspan="2"><h3 style="color:#0000cc;text-align:center;">Exam Information in Detail</h3></td>
                        </tr>
                        <tr>
                            <td colspan="2" ><hr style="color:#ff0000;border-width:4px;"/></td>
                        </tr>
                    </table>
                                <?php

                                $result1=executeQuery("select q.qnid as questionid,q.question as quest,q.correctanswer as ca,sq.answered as status,sq.stdanswer as sa from studentquestion as sq,question as q where q.qnid=sq.qnid and sq.testid=q.testid and sq.testid=".$_REQUEST['details']." and sq.uid=".$_SESSION['uid']." order by q.qnid;" );

                                if(mysql_num_rows($result1)==0) {
                                    echo"<h3 style=\"color:#0000cc;text-align:center;\">1.Sorry because of some problems Individual questions Cannot be displayed.</h3>";
                                }
                                else {
                                    ?>
                    <table id="demoTable" cellpadding="30" cellspacing="10" class="datatable">
                    <thead>
                        <tr>
                            <th>Q. No</th>
                            <th>Question</th>
                            <th>Correct Answer</th>
                            <th>Your Answer</th>
                            <th>Score</th>
                            <th>&nbsp;</th>
                        </tr>
                          </thead>
                        <tbody>
                                        <?php
                                        while($r1=mysql_fetch_array($result1)) {

                                        if(is_null($r1['sa']))
                                        $r1['sa']="question"; //any valid field of question
                                           $result2=executeQuery("select ".$r1['ca']." as corans,IF('".$r1['status']."'='answered',(select ".$r1['sa']." from question where qnid=".$r1['questionid']." and testid=".$_REQUEST['details']."),'unanswered') as stdans, IF('".$r1['status']."'='answered',IFNULL((select q.marks from question as q, studentquestion as sq where q.qnid=sq.qnid and q.testid=sq.testid and q.correctanswer=sq.stdanswer and sq.uid=".$_SESSION['uid']." and q.qnid=".$r1['questionid']." and q.testid=".$_REQUEST['details']."),0),0) as stdmarks from question where qnid=".$r1['questionid']." and testid=".$_REQUEST['details'].";");

                                            if($r2=mysql_fetch_array($result2)) {
                                                ?>
                        <tr>
                            <td><?php echo $r1['questionid']; ?></td>
                            <td><?php echo htmlspecialchars_decode($r1['quest'],ENT_QUOTES); ?></td>
                            <td><?php echo htmlspecialchars_decode($r2['corans'],ENT_QUOTES); ?></td>
                            <td><?php echo htmlspecialchars_decode($r2['stdans'],ENT_QUOTES); ?></td>
                            <td><?php echo $r2['stdmarks']; ?></td>
                                                    <?php
                                                    if($r2['stdmarks']==0) {
                                                        echo"<td class=\"tddata\"><img src=\"../images/wrong.png\" title=\"Wrong Answer\" height=\"30\" width=\"40\" alt=\"Wrong Answer\" /></td>";
                                                    }
                                                    else {
                                                        echo"<td class=\"tddata\"><img src=\"../images/correct.png\" title=\"Correct Answer\" height=\"30\" width=\"40\" alt=\"Correct Answer\" /></td>";
                                                    }
                                                    ?>
                        </tr>
                            <?php
                                                }
                                                else {
                                                    echo"<h3 style=\"color:#0000cc;text-align:center;\">Sorry because of some problems Individual questions Cannot be displayed.</h3>".mysql_error();
                                                }
                                            }

                                        }
                                    }
                                    else {
                                        echo"<h3 style=\"color:#0000cc;text-align:center;\">Something went wrong. Please logout and Try again.</h3>".mysql_error();
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
                        else {


                            $result=executeQuery("select st.*,t.testname,t.testdesc,DATE_FORMAT(st.starttime,'%d %M %Y %H:%i:%s') as startt from studenttest as st,test as t where t.testid=st.testid and st.uid=".$_SESSION['uid']." and st.status='over' order by st.testid;");
                            if(mysql_num_rows($result)==0) {
                                echo"<h3 style=\"color:#0000cc;text-align:center;\">I Think You Haven't Attempted Any Exams Yet..! Please Try Again After Your Attempt.</h3>";
                            }
                            else {
                            //editing components
                                ?>
                    <table cellpadding="30" cellspacing="10" class="datatable">
                        <tr>
                            <th>Date and Time</th>
                            <th>Exam Name</th>
                            <th>Max. Marks</th>
                            <th>Obtained Marks</th>
                            <th>Percentage</th>
                            <th>Details</th>
                        </tr>
                      
            <?php
            while($r=mysql_fetch_array($result)) {
                                        $i=$i+1;
                                        $om=0;
                                        $tm=0;
                                        $result1=executeQuery("select sum(q.marks) as om from studentquestion as sq, question as q where sq.testid=q.testid and sq.qnid=q.qnid and sq.answered='answered' and sq.stdanswer=q.correctanswer and sq.uid=".$_SESSION['uid']." and sq.testid=".$r['testid']." order by sq.testid;");
                                        $r1=mysql_fetch_array($result1);
                                        $result2=executeQuery("select sum(marks) as tm from question where testid=".$r['testid'].";");
                                        $r2=mysql_fetch_array($result2);
                                        if($i%2==0) {
                                            echo "<tr class=\"alt\">";
                                        }
                                        else { echo "<tr>";}
                                        echo "<td>".$r['startt']."</td><td>".htmlspecialchars_decode($r['testname'],ENT_QUOTES)." : ".htmlspecialchars_decode($r['testdesc'],ENT_QUOTES)."</td>";
                                        if(is_null($r2['tm'])) {
                                            $tm=0;
                                            echo "<td>$tm</td>";
                                        }
                                        else {
                                            $tm=$r2['tm'];
                                            echo "<td>$tm</td>";
                                        }
                                        if(is_null($r1['om'])) {
                                            $om=0;
                                            echo "<td>$om</td>";
                                        }
                                        else {
                                            $om=$r1['om'];
                                            echo "<td>$om</td>";
                                        }
                                        if($tm==0) {
                                            echo "<td>0</td>";
                                        }
                                        else {
                                            echo "<td>".(($om/$tm)*100)." %</td>";
                                        }
                                        echo"<td class=\"tddata\"><a title=\"Details\" href=\"viewresult.php?details=".$r['testid']."\"><img src=\"../images/detail.png\" height=\"30\" width=\"40\" alt=\"Details\" /></a></td></tr>";
                                    }

                                    ?>
                    
                    </table>
        <?php
        }
                        }
                        closedb();
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

