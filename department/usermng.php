
<?php
 /*
 * ***************************************************
 * ** Examination Management System                ***
 * **--------------------------------------------- ***
 * ** Developer: Dejene Techane                    ***
 * ** Title: Users Management(Add,delete,Modify)   ***
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
  Case 3 : Delete - Delete the selected User/s from System.
  Case 4 : Edit - Update the new information.
  Case 5 : Add - Add new user to the system.

 * ------------ *
 * HTML Section *
 * ------------ *

  Step 3: Display the HTML Components for...
  Case 1: Add - Form to receive new user information.
  Case 2: Edit - Form to edit Existing User Information.
  Case 3: Default Mode - Displays the Information of Existing Users, If any.
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
 else if(!($_SESSION['role']=="Department")){
    unset($_SESSION['username']);
  $_GLOBALS['message']="Please, You are accessing unauthorized page.Click here to <a href=\"../index.php\">Re-LogIn</a>";
 }else if (isset($_REQUEST['logout'])) {
    /*     * ************************ Step 2 - Case 1 ************************ */
    //Log out and redirect login page
    unset($_SESSION['username']);
    header('Location: ../index.php');
} else if (isset($_REQUEST['dashboard'])) {
    /*     * ************************ Step 2 - Case 2 ************************ */
    //redirect to dashboard

    header('Location: deptwelcome.php');
} else if (isset($_REQUEST['ins'])) {
    /*     * ************************ Step 2 - Case 2 ************************ */
    //redirect to dashboard

    header('Location: insmng.php');
} else if (isset($_REQUEST['std'])) {
    /*     * ************************ Step 2 - Case 2 ************************ */
    //redirect to dashboard

    header('Location: importstd.php');
}else if (isset($_REQUEST['delete'])) {
    /*     * ************************ Step 2 - Case 3 ************************ */
    //deleting the selected users
    unset($_REQUEST['delete']);
    $hasvar = false;
    foreach ($_REQUEST as $variable) {
        if (is_numeric($variable)) { //it is because, some sessin values are also passed with request
            $hasvar = true;

            if (!@executeQuery("delete from student where stdid=$variable")) {
                if (mysql_errno () == 1451) //Children are dependent value
                    $_GLOBALS['message'] = "Too Prevent accidental deletions, system will not allow propagated deletions.<br/><b>Help:</b> If you still want to delete this user, then first manually delete all the records that are associated with this user.";
                else
                    $_GLOBALS['message'] = mysql_errno();
            }
        }
    }
    if (!isset($_GLOBALS['message']) && $hasvar == true)
        $_GLOBALS['message'] = "Selected Student/s are successfully Deleted";
    else if (!$hasvar) {
        $_GLOBALS['message'] = "First Select the students to be Deleted.";
    }
} else if (isset($_REQUEST['savem'])) {
    /*     * ************************ Step 2 - Case 4 ************************ */
    //updating the modified values
    //retrieving values from the form
   //encrypting password
    	$newpass=md5(htmlspecialchars($_REQUEST['newpass'], ENT_QUOTES));
    	$repass=md5(htmlspecialchars($_REQUEST['repass'], ENT_QUOTES));
    	$idno=htmlspecialchars($_REQUEST['idno'], ENT_QUOTES);
    	$fullname=htmlspecialchars($_REQUEST['fullname'], ENT_QUOTES);
    	$sex=htmlspecialchars($_REQUEST['sex'], ENT_QUOTES);
    	$dept=htmlspecialchars($_REQUEST['dept'], ENT_QUOTES);
    	//$role=htmlspecialchars($_REQUEST['role'], ENT_QUOTES);
    if (empty($_REQUEST['fullname']) || empty($_REQUEST['newpass']) || empty($_REQUEST['repass'])) {
        $_GLOBALS['message'] = "Some of the required Fields are Empty.Therefore Nothing is Updated";
    }  else if(!($repass==$newpass)){
        $_GLOBALS['message'] = "Re-password & new Password are not same, please try again";
        }
    
    else {
        $query = "update student as s,user as u set s.idno='".$idno."',s.fullname='".$fullname."',s.sex='".$sex."',s.dept='".$dept."',u.username='" .$idno. "', u.password='".$newpass."' where s.stdid='".htmlspecialchars($_REQUEST['student'],ENT_QUETES)."' and u.uid='" . htmlspecialchars($_REQUEST['user'], ENT_QUOTES) . "';";
        if (!@executeQuery($query))
            $_GLOBALS['message'] = mysql_error();
        else
            $_GLOBALS['message'] = "Student Information is Successfully Updated.";
    }
    closedb();
}

?>
<html>
    <head>
        <title>EMS-Manage Users</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="icon" type="jpg/png" href="../images/logo.png"/>
        <link rel="stylesheet" type="text/css" href="../ems.css"/>
        <script type="text/javascript" src="../validate.js" ></script>    
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
    </head>
    <body>
<?php
if (isset($_GLOBALS['message'])) {
    echo "<div class=\"message\">" . $_GLOBALS['message'] . "</div>";
}
?>
        <div id="container">
             <div class="header">                              
                <?php require'../include/header.php';?>                
                </div>
            <form name="usermng" action="usermng.php" method="post">
                <div class="menubar">


                    <ul id="menu">
<?php
if (isset($_SESSION['username'])) {
// Navigations
?>
                        <li><input type="submit" value="LogOut" name="logout" class="subbtn" title="Log Out"/></li>
                        <li><input type="submit" value="DashBoard" name="dashboard" class="subbtn" title="Dash Board"/></li>
                         <li><input type="submit" value="Instructor" name="ins" class="subbtn" title="Instructor Manage"/></li>
                        

<?php
    //navigation for Add option
    if (isset($_REQUEST['add'])) {
?>
<?php
    } else if (isset($_REQUEST['edit'])) { //navigation for Edit option
?>
                        <li><input type="submit" value="Cancel" name="cancel" class="subbtn" title="Cancel"/></li>
                        <li><input type="submit" value="Save" name="savem" class="subbtn" onclick="validateform('usermng')" title="Save the changes"/></li>

<?php
    } else {  //navigation for Default
?>
                        <li><input type="submit" value="Delete" name="delete" class="subbtn" title="Delete" onclick="return confirm('Are you sure you want to delete this program?')"/></li>
                        <li><input type="submit" value="Add" name="std" class="subbtn" title="Add" title="Add new Student Profile"/></li>
<?php }
} ?>
                    </ul>

                </div>
                <div class="page">
<?php
if (isset($_SESSION['username'])) {
    echo "<div class=\"pmsg\" style=\"text-align:center;\">Student Management </div>";
    if (isset($_REQUEST['add'])) {
        /*         * ************************ Step 3 - Case 1 ************************ */
        //Form for the new user
?>
                   

<?php
    } else if (isset($_REQUEST['edit'])) {
        /*         * ************************ Step 3 - Case 2 ************************ */
        // To allow Editing Existing User Information
        //$result = executeQuery("select * from user where username='" . htmlspecialchars($_REQUEST['edit'], ENT_QUOTES) . "';");
         $result = executeQuery("select s.stdid,s.idno, s.fullname,s.sex,s.dept,u.uid,u.password from student as s, user as u where s.idno=u.username and s.idno='" . htmlspecialchars($_REQUEST['edit'], ENT_QUOTES) . "';");
             
        if (mysql_num_rows($result) == 0) {
            header('Location: usermng.php');
        } else if ($r = mysql_fetch_array($result)) {

            //editing components
?>
                    <table cellpadding="20" cellspacing="20" style="text-align:left;margin-left:15em" >
                    <tr>
                            <td>IDNO</td> 
                            <td><input type="text" name="idno" value="<?php echo htmlspecialchars_decode($r['idno'], ENT_QUOTES); ?>" size="25"  onblur="if(this.value==''){alert('IDNO field is Empty');this.focus();this.value='';}"/></td>

                        </tr>
                        <tr>
                            <td>Full Name</td> 
                            <td><input type="text" name="fullname" value="<?php echo htmlspecialchars_decode($r['fullname'], ENT_QUOTES); ?>" size="25"  onblur="if(this.value==''){alert('Name field is Empty');this.focus();this.value='';}"/></td>

                        </tr>
                        <tr>
                        <td> Sex</td>
                            <td><select name="sex">
                            <option value="<?php echo htmlspecialchars_decode($r['sex'], ENT_QUOTES); ?>"><?php echo htmlspecialchars_decode($r['sex'], ENT_QUOTES); ?>
                            <option value="F">Female</option>
                            <option value="M">Male</option> 
                            </select></td>
                            </tr>
                            <tr>
                            <td>Department</td> 
                            <td><input type="text" name="dept" value="<?php echo htmlspecialchars_decode($r['dept'], ENT_QUOTES); ?>" size="25"  onblur="if(this.value==''){alert('Name field is Empty');this.focus();this.value='';}"/></td>

                        </tr>
                          <tr>
                            <td>New Password</td>
                            <td><input type="password" name="newpass" value="" size="25" onkeyup="isalphanum(this)" onblur="if(this.value==''){alert('New Password field is Empty');this.focus();this.value='';}"/></td>

                        </tr>
                        <tr>
                            <td>Re-type Password</td>
                            <td><input type="password" name="repass" value="" size="25" onkeyup="isalphanum(this)" onblur="if(this.value==''){alert('Re-type Password field is Empty');this.focus();this.value='';}"/></td>

                        </tr>
                        <tr>
                           
                            <input type="hidden" name="user" value="<?php echo htmlspecialchars_decode($r['uid'], ENT_QUOTES); ?>"/>
                            <input type="hidden" name="student" value="<?php echo htmlspecialchars_decode($r['stdid'], ENT_QUOTES); ?>"/></td>
                            
                        </tr>
                        
                    </table>
<?php
                    closedb();
                }
            } else {
                /*                 * ************************ Step 3 - Case 3 ************************ */
                // Defualt Mode: Displays the Existing Users, If any.
                $result = executeQuery("select s.stdid,s.idno, s.fullname,s.sex,s.dept,u.uid,u.password from student as s, user as u where s.idno=u.username and s.uid=".$_SESSION['uid']." order by s.stdid;");
                if (mysql_num_rows($result) == 0) {
                    echo "<h3 style=\"color:#0000cc;text-align:center;\">No Students Yet..!</h3>";
                } else {
                    $i = 0;
?>
                    <table id="demoTable" cellpadding="30" cellspacing="10" class="datatable">   
                    <thead>
                        <tr>                        	
                            <th>&nbsp;</th>
                            <th>S.No.</th>
                            <th>IDNO</th>
                             <th>Full Name</th>
                              <th>Sex</th>
                            <th>Department</th>
                            <th>Passowrd</th>
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
                        echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"d$i\" value=\"" . $r['stdid'] . "\" /></td><td>$i</td><td>" . htmlspecialchars_decode($r['idno'], ENT_QUOTES)
                        . "</td><td>" . htmlspecialchars_decode($r['fullname'], ENT_QUOTES) . "</td><td>" . htmlspecialchars_decode($r['sex'], ENT_QUOTES) . "</td>"
                        ."<td>" . htmlspecialchars_decode($r['dept'], ENT_QUOTES) . "</td><td>" . htmlspecialchars_decode($r['password'], ENT_QUOTES) . "</td>"
                        . "<td class=\"tddata\"><a title=\"Edit " . htmlspecialchars_decode($r['stdid'], ENT_QUOTES) . "\"href=\"usermng.php?edit=" . htmlspecialchars_decode($r['idno'], ENT_QUOTES) . "\"><img src=\"../images/edit.png\" height=\"30\" width=\"40\" alt=\"Edit\" /></a></td></tr>";
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

