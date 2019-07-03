
<?php


/*
****************************************************
*** Examination Management System                ***
***----------------------------------------------***
*** Developer: Dejene Techane                    ***
*** Title: Results Management                    ***
****************************************************
*/

/* Procedure
*********************************************

 * ----------- *
 * PHP Section *
 * ----------- *

Step 1: Perform Session Validation.

Step 2: Event to Process...
        Case 1 : Logout - perform session cleanup.
        Case 2 : Dashboard - redirect to Dashboard
        Case 3 : Back - Return back to Result Management.


 * ------------ *
 * HTML Section *
 * ------------ *

Step 3: Display the HTML Components for...
        Case 1: Display the exam results in detail.
        Case 2: Default Mode.. in which exam results are summarised.

*********************************************
*/

error_reporting(0);
session_start();
include_once '../include/emsdb.php';
/************************** Step 1 *************************/
if(!isset($_SESSION['username'])) {
    $_GLOBALS['message']="Session Timeout.Click here to <a href=\"../index.php\">Re-LogIn</a>";
}
//This is to protect unauthorized users
 else if(!($_SESSION['role']=="Instructor")){
    unset($_SESSION['username']);
  $_GLOBALS['message']="Please, You are accessing unauthorized page.Click here to <a href=\"../index.php\">Re-LogIn</a>";
 }
else if(isset($_REQUEST['logout'])) {
    /************************** Step 2 - Case 1 *************************/
    //Log out and redirect login page
        unset($_SESSION['username']);
        header('Location: ../index.php');

    }
    else if(isset($_REQUEST['dashboard'])) {
    /************************** Step 2 - Case 2 *************************/
        //redirect to dashboard
            header('Location: inswelcome.php');

        }
        else if(isset($_REQUEST['back'])) {
    /************************** Step 2 - Case 3 *************************/
            //redirect to Result Management Section
                header('Location: rsltmng.php');

            }
 else if(isset($_REQUEST['testid'])) {
    /************************** Step 2 - Case 3 *************************/
            //exporting student Results to excel sheet
  // $rslt=executeQuery("select * from test where testid='".$_REQUEST['testid']."';");
   //$r=mysql_fetch_array($rslt);
  // $tname=$r['testname'];
   //echo $tname;
   
 $filename ='result.csv'; 
 //$filename = "uploads/".strtotime("now").'.csv';
$sql = executeQuery("SELECT s.idno,s.fullname,s.sex,ass.Marks FROM student as s,assessment as ass where s.stdid=ass.stdid and testid='".$_REQUEST['testid']."';");
 //$result1=executeQuery("select s.stdid,s.idno,s.fullname,s.sex,u.username,IFNULL((select sum(q.marks) from studentquestion as sq, question as q where sq.testid=q.testid and sq.qnid=q.qnid and sq.stdanswer=q.correctanswer and sq.uid=st.uid and sq.testid=".$_REQUEST['testid']."),0) as om from student as s,user as u,studenttest as st where s.idno=u.username and u.uid=st.uid and st.testid=".$_REQUEST['testid'].";") ;
      
		$num_rows = mysql_num_rows($sql);
		if($num_rows >= 1)
		{
			$row = mysql_fetch_assoc($sql);
			$fp = fopen($filename, "w");
			$seperator = "";
			$comma = "";

			foreach ($row as $name => $value)
				{
					$seperator .= $comma . '' .str_replace('', '""', $name);
					$comma = ",";
				}

			$seperator .= "\n";
			fputs($fp, $seperator);
	
			mysql_data_seek($sql, 0);
			while($row = mysql_fetch_assoc($sql))
				{
					$seperator = "";
					$comma = "";

					foreach ($row as $name => $value) 
						{
							$seperator .= $comma . '' .str_replace('', '""', $value);
							$comma = ",";
						}

					$seperator .= "\n";
					fputs($fp, $seperator);
				}	
			fclose($fp);
			$_GLOBALS['message']= "You can Export Student Result To Excel Sheet. Click <a href='$filename'>Export!</a>";
			//automatically deleting records from assessment table to remove data redundency
			executeQuery("delete from assessment where testid='".$_REQUEST['testid']."';");
			
		}
		else
		{
			$_GLOBALS['message']= "There is no record in your Database";
			
		}

			closedb();
			
            }
   /*     //here automatically delete file after downloaded	
		ignore_user_abort(true);
		if (connection_aborted()) {
			unlink($filename);
		}*/
			   
?>
<html>
    <head>
        <title>EMS-Manage Results</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
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
   <script type="text/javascript">
//Place as last thing before the closing </body> tag
if(location.search.indexOf('reloaded=yes') < 0){
	var hash = window.location.hash;
	var loc = window.location.href.replace(hash, '');
	loc += (loc.indexOf('?') < 0? '?' : '&') + 'reloaded=yes';
	// SET THE ONE TIME AUTOMATIC PAGE RELOAD TIME TO 5000 MILISECONDS (5 SECONDS):
	setTimeout(function(){window.location.href = loc + hash;}, 5000);
}
</script>
</html>

    </head>
    <body>
        <?php

        if($_GLOBALS['message']) {
            echo "<div class=\"message\">".$_GLOBALS['message']."</div>";
        }
        ?>
        <div id="container">
           <div class="header">                              
                <?php require'../include/header.php';?>                
                </div>
            <form name="rsltmng" action="rsltmng.php" method="post"> 
                <div class="menubar">
                

                    <ul id="menu">
                        <?php if(isset($_SESSION['username'])) {
                        // Navigations
                            ?>
                        <li><input type="submit" value="LogOut" name="logout" class="subbtn" title="Log Out"/></li>
                            <?php  if(isset($_REQUEST['testid'])) { ?>
                        <li><input type="submit" value="Back" name="back" class="subbtn" title="Manage Results"/></li>
                    
                            <?php }else { ?>
                        <li><input type="submit" value="DashBoard" name="dashboard" class="subbtn" title="Dash Board"/></li>
                            <?php } ?>
                    </ul>
                </div>
                <div class="page">
                        <?php
                        if(isset($_REQUEST['testid'])) {
                    /************************** Step 3 - Case 2 *************************/
                        // Displays the Existing Test Results in detail, If any.
                            $result=executeQuery("select t.testid,t.testname,DATE_FORMAT(t.testfrom,'%d %M %Y') as fromdate,DATE_FORMAT(t.testto,'%d %M %Y %H:%i:%S') as todate,c.cid,c.cname,IFNULL((select sum(marks) from question where testid=".$_REQUEST['testid']."),0) as maxmarks from test as t, course as c where c.cid=t.cid and t.testid=".$_REQUEST['testid'].";") ;
                            if(mysql_num_rows($result)!=0) {

                                $r=mysql_fetch_array($result);
                                //temporarly storing row values into session variable
                                $_SESSION['cid']=$r['cid'];
                                $_SESSION['tm']=$r['maxmarks'];
                                $_SESSION['testid']=$r['testid'];
                                ?>
                    <table cellpadding="20" cellspacing="30" border="0" style="background:#ffffff url(../images/page.gif);text-align:left;line-height:20px;">
                        <tr>
                            <td colspan="2"><h3 style="color:#0000cc;text-align:center;">Exam Summary</h3></td>
                        </tr>
                        <tr>
                            <td colspan="2" ><hr style="color:#ff0000;border-width:4px;"/></td>
                        </tr>
                        <tr>
                            <td>Exam Name</td>
                            <td><?php echo htmlspecialchars_decode($r['testname'],ENT_QUOTES); ?></td>
                        </tr>
                        <tr>
                            <td>Course Name</td>
                            <td><?php echo htmlspecialchars_decode($r['cname'],ENT_QUOTES); ?></td>
                        </tr>
                        <tr>
                            <td>Validity</td>
                            <td><?php echo $r['fromdate']." To ".$r['todate']; ?></td>
                        </tr>
                        <tr>
                            <td>Max. Marks</td>
                            <td><?php echo $r['maxmarks']; ?></td>
                        </tr>
                        <tr><td colspan="2"><hr style="color:#ff0000;border-width:2px;"/></td></tr>
                        <tr>
                            <td colspan="2"><h3 style="color:#0000cc;text-align:center;">Attempted Students</h3></td>
                        </tr>
                        <tr>
                            <td colspan="2" ><hr style="color:#ff0000;border-width:4px;"/></td>
                        </tr>

                    </table>
                                <?php
  

      $result1=executeQuery("select s.*,u.username,
      	  IFNULL((select sum(q.marks) from studentquestion as sq, 
      	  question as q where sq.testid=q.testid and sq.qnid=q.qnid and sq.stdanswer=q.correctanswer and 
      	  sq.uid=st.uid and sq.testid=".$_REQUEST['testid']."),0) as om from student as s,user as u,
      	  studenttest as st where s.idno=u.username and u.uid=st.uid and st.testid=".$_REQUEST['testid'].";") ;
                                                   if(mysql_num_rows($result1)==0) {
                       echo"<h3 style=\"color:#0000cc;text-align:center;\">No Students Yet Attempted this Exam!</h3>";
                                }
                                else {
                                    ?>
                    <table id="demoTable" cellpadding="30" cellspacing="10" class="datatable">
                    <thead>
                        <tr>
                           <th>S.NO</th>
                            <th>Student IDNO</th>
                            <th>Name</th>
                            <th>Sex</th>
                            <th>Obtained Marks</th>
                            <th>Percentage(100%)</th>

                        </tr>
                        </thead>
                        <tbody>
                                        <?php
                                        $i=1;
                                        while($r1=mysql_fetch_array($result1)) {
                                 //temporarly storing row values into session variable
                                $_SESSION['stdid']=$r1['stdid'];
                                $_SESSION['om']=$r1['om'];
                                
                                            ?>
                        <tr>
                        <td><?php echo $i;?></td>
                            <td><?php echo htmlspecialchars_decode($r1['idno'],ENT_QUOTES); ?></td>
                            <td><?php echo htmlspecialchars_decode($r1['fullname'],ENT_QUOTES); ?></td>
                            <td><?php echo htmlspecialchars_decode($r1['sex'],ENT_QUOTES); ?></td>
                            <td><?php echo $r1['om']; ?></td>
                            <td><?php echo ($r1['om']/$r['maxmarks']*100)." %"; ?></td>
                        </tr>
                                        <?php 
                                        
                              $i++;  
                             
                           //automatically inserting student results into table assessment 
                           $id=$_SESSION['stdid'];
                           $uid=$_SESSION['uid'];
                           $cid=$_SESSION['cid'];
                           $om=$_SESSION['om'];
                           $tm=$_SESSION['tm'];
                           $tid=$_SESSION['testid'];
                          $rslt=executeQuery("INSERT INTO `ems`.`assessment` (`stdid`, `cid`, `uid`, `testid`, `Marks`, `maxmarks`) VALUES ('$id', '$cid', '$uid', '$tid', '$om', '$tm');");
                          //executeQuery("INSERT INTO std_result_temp VALUES('".$_SESSION['idno']."','".$_SESSION['name']."','".$_SESSION['sex']."','".$_SESSION['om']."','".$_SESSION['uid']."','".$_SESSION['cid']."','".$_SESSION['testid']."';");
                         if($rslt)
                          	  $_GLOBALS['message']="Automatically saved student results";
                          else
                          $_GLOBALS['message']="Something went wrong. Please logout and Try again.";
                          
                                        }

                                    }
                                }
                                else {
                                    echo"<h3 style=\"color:#0000cc;text-align:center;\">Something went wrong. Please logout and Try again.</h3>";
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

                        /************************** Step 3 - Case 2 *************************/
                        // Defualt Mode: Displays the Existing Test Results, If any.
                            $result=executeQuery("select t.testid,t.testname,DATE_FORMAT(t.testfrom,'%d %M %Y') as fromdate,DATE_FORMAT(t.testto,'%d %M %Y %H:%i:%S') as todate,c.cname,(select count(uid) from studenttest where testid=t.testid) as attemptedstudents from test as t, course as c where c.cid=t.cid and t.uid=".$_SESSION['uid'].";");
                            if(mysql_num_rows($result)==0) {
                                echo "<h3 style=\"color:#0000cc;text-align:center;\">No Exams Yet...!</h3>";
                            }
                            else {
                                $i=0;

                                ?>
                    <table cellpadding="30" cellspacing="10" class="datatable">
                        <tr>
                            <th>Exam Name</th>
                            <th>Validity</th>
                            <th>Course Name</th>
                            <th>Attempted Students</th>
                            <th>Details</th>
                        </tr>
            <?php
                                    while($r=mysql_fetch_array($result)) {
                                        $i=$i+1;
                                        if($i%2==0) {
                                            echo "<tr class=\"alt\">";
                                        }
                                        else { echo "<tr>";}
                                        echo "<td>".htmlspecialchars_decode($r['testname'],ENT_QUOTES)."</td><td>".$r['fromdate']." To ".$r['todate']." PM </td>"
                                            ."<td>".htmlspecialchars_decode($r['cname'],ENT_QUOTES)."</td><td>".$r['attemptedstudents']."</td>"
                                            ."<td class=\"tddata\"><a title=\"Details\" href=\"rsltmng.php?testid=".$r['testid']."\"><img src=\"../images/detail.png\" height=\"30\" width=\"40\" alt=\"Details\" /></a></td></tr>";
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