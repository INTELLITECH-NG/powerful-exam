
<?php
/*
 * ***************************************************
 * ** Examination Management System                ***
 * **--------------------------------------------- ***
 * ** Developer: Dejene Techane                    ***
 * ** Title: Courses Management(Add,delete,Modify) ***
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
  Case 3 : Delete - Delete the selected Course/s from System.
  Case 4 : Edit - Update the new information.
  Case 5 : Add - Add new Course to the system.

 * ------------ *
 * HTML Section *
 * ------------ *

  Step 3: Display the HTML Components for...
  Case 1: Add - Form to receive new Course information.
  Case 2: Edit - Form to edit Existing Course Information.
  Case 3: Default Mode - Displays the Information of Existing Courses, If any.
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
}else if(isset($_REQUEST['crs'])){
	//redirect to import course
	header('Location:importcourse.php');
} else if (isset($_REQUEST['delete'])) {
    /*     * ************************ Step 2 - Case 3 ************************ */
    //deleting the selected course
    unset($_REQUEST['delete']);
    $hasvar = false;
    foreach ($_REQUEST as $variable) {
        if (is_numeric($variable)) { //it is because, some session values are also passed with request
            $hasvar = true;

            if (!@executeQuery("delete from course where cid=$variable")) {
                if (mysql_errno () == 1451) //Children are dependent value
                    $_GLOBALS['message'] = "Too Prevent accidental deletions, system will not allow propagated deletions.<br/><b>Help:</b> If you still want to delete this subject, then first delete the tests that are conducted/dependent on this subject.";
                else
                    $_GLOBALS['message'] = mysql_errno();
            }
        }
    }
    if (!isset($_GLOBALS['message']) && $hasvar == true)
        $_GLOBALS['message'] = "Selected Course/s are successfully Deleted";
    else if (!$hasvar) {
        $_GLOBALS['message'] = "First Select the course/s to be Deleted.";
    }
} else if (isset($_REQUEST['savem'])) {
    /*     * ************************ Step 2 - Case 4 ************************ */
    //updating the modified values
    if (empty($_REQUEST['mcode']) || empty($_REQUEST['mname'])||empty($_REQUEST['ccode'])||empty($_REQUEST['cname'])||empty($_REQUEST['ects'])) {
        $_GLOBALS['message'] = "Some of the required Fields are Empty.Therefore Nothing is Updated";
    } else {
    	
      //retriving value from the form
    	$mcode=htmlspecialchars($_REQUEST['mcode'], ENT_QUOTES);
    	$mname=htmlspecialchars($_REQUEST['mname'], ENT_QUOTES);
    	$ccode=htmlspecialchars($_REQUEST['ccode'], ENT_QUOTES);
    	$cname=htmlspecialchars($_REQUEST['cname'], ENT_QUOTES);
    	$ects=htmlspecialchars($_REQUEST['ects'], ENT_QUOTES);
    	$year=htmlspecialchars($_REQUEST['year'], ENT_QUOTES);
    	$sem=htmlspecialchars($_REQUEST['sem'], ENT_QUOTES);
    	$dept=htmlspecialchars($_REQUEST['dept'], ENT_QUOTES);
    	//$email=htmlspecialchars($_REQUEST['email'], ENT_QUOTES);
    	
        $query = "update course set mcode='" . $mcode."', mname='".$mname. "',ccode='" .$ccode . "',cname='" .$cname . "',ects='" .$ects . "',year='" . $year. "',sem='" .$sem. "',dept='" . $dept . "'where cid='" . $_REQUEST['course'] . "' ;";
        if (!@executeQuery($query))
            $_GLOBALS['message'] = mysql_error();
        else
            $_GLOBALS['message'] = "Course Information is Successfully Updated.";
    }
    closedb();
}

?>
<html>
    <head>
        <title>EMS-Manage Course</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="icon" type="jpg/png" href="images/logo.png"/>
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
if ($_GLOBALS['message']) {
    echo "<div class=\"message\">" . $_GLOBALS['message'] . "</div>";
}
?>
        <div id="container">
             <div class="header">                              
                <?php require'../include/header.php';?>                
                </div>
            <form name="coursemng" action="coursemng.php" method="post">
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
                        <li><input type="submit" value="Save" name="savea" class="subbtn" onclick="validatesubform('coursemng')" title="Save the Changes"/></li>

<?php
    } else if (isset($_REQUEST['edit'])) { //navigation for Edit option
?>
                        <li><input type="submit" value="Cancel" name="cancel" class="subbtn" title="Cancel"/></li>
                        <li><input type="submit" value="Save" name="savem" class="subbtn" onclick="validatesubform('subcoursemngmng')" title="Save the changes"/></li>

<?php
    } else {  //navigation for Default
?>
                        <li><input type="submit" value="Delete" name="delete" class="subbtn" title="Delete" onclick="return confirm('Are you sure to delete this record?')"/></li>
                        <li><input type="submit" value="Add" name="crs" class="subbtn" title="Import Course Details"/></li>
<?php }
} ?>
                    </ul>

                </div>
                <div class="page">
<?php
if (isset($_SESSION['username'])) {
    echo "<div class=\"pmsg\" style=\"text-align:center;\">Course Management </div>";
    if (isset($_REQUEST['add'])) {
        /*         * ************************ Step 3 - Case 1 ************************ */
        //Form for the new user
?>
                               

<?php
    } else if (isset($_REQUEST['edit'])) {

        /*         * ************************ Step 3 - Case 2 ************************ */
        // To allow Editing Existing Subject.
        $result = executeQuery("select * from course where ccode='" . htmlspecialchars($_REQUEST['edit'], ENT_QUOTES) . "';");
        if (mysql_num_rows($result) == 0) {
            header('coursemng.php');
        } else if ($r = mysql_fetch_array($result)) {


            //editing components
?>
                    <table cellpadding="20" cellspacing="20" style="text-align:left;margin-left:15em" >
                        <tr>
                            <td>Module Code</td>
                            <td><input type="text" name="mcode" value="<?php echo htmlspecialchars_decode($r['mcode'], ENT_QUOTES); ?>" size="25" onkeyup="isalphanum(this)" onblur="if(this.value==''){alert('Module Code field is Empty');this.focus();this.value='';}"/></td>

                        </tr>
                         <table cellpadding="20" cellspacing="20" style="text-align:left;margin-left:15em" >
                    <tr>
                            <td>Module Name</td> 
                            <td><input type="text" name="mname" value="<?php echo htmlspecialchars_decode($r['mname'], ENT_QUOTES); ?>" size="25"  onblur="if(this.value==''){alert('Module Name field is Empty');this.focus();this.value='';}"/></td>

                        </tr>
                        <tr>
                            <td>Course No</td> 
                            <td><input type="text" name="ccode" value="<?php echo htmlspecialchars_decode($r['ccode'], ENT_QUOTES); ?>" size="25"  onblur="if(this.value==''){alert('Course Code field is Empty');this.focus();this.value='';}"/></td>

                        </tr>
                         <tr>
                            <td>Course Name</td> 
                            <td><input type="text" name="cname" value="<?php echo htmlspecialchars_decode($r['cname'], ENT_QUOTES); ?>" size="25"  onblur="if(this.value==''){alert('Course Name field is Empty');this.focus();this.value='';}"/></td>

                        </tr>
                         <tr>
                            <td>ECTS</td> 
                            <td><input type="text" name="ects" value="<?php echo htmlspecialchars_decode($r['ects'], ENT_QUOTES); ?>" size="25"  onblur="if(this.value==''){alert('Course ECTS field is Empty');this.focus();this.value='';}"/></td>

                        </tr>
                        <tr>
                        <td> Year</td>
                            <td><select name="year">
                            <option value="<?php echo htmlspecialchars_decode($r['year'], ENT_QUOTES); ?>"><?php echo htmlspecialchars_decode($r['year'], ENT_QUOTES); ?>
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                            <option value="VI">VI</option>
                            </select></td>
                            </tr>
                            <tr>
                        <td> Sem</td>
                            <td><select name="sem">
                            <option value="<?php echo htmlspecialchars_decode($r['sem'], ENT_QUOTES); ?>" size=25><?php echo htmlspecialchars_decode($r['sem'], ENT_QUOTES); ?>
                            <option value="I">I</option>
                            <option value="II">II</option>
                            </td></select>
                            </tr>
                            <tr>
                            <td>Department</td> 
                            <td><input type="text" name="dept" value="<?php echo htmlspecialchars_decode($r['dept'], ENT_QUOTES); ?>" size="16"  onblur="if(this.value==''){alert('Department field is Empty');this.focus();this.value='';}"/>

                           <input type="hidden" name="course" value="<?php echo $r['cid']; ?>"/></td>
                        </tr>
                    </table>
<?php
                    closedb();
                }
            } else {

                /*                 * ************************ Step 3 - Case 3 ************************ */
                // Defualt Mode: Displays the Existing Subject/s, If any.
                $result = executeQuery("select * from course order by year,sem;");
                if (mysql_num_rows($result) == 0) {
                    echo "<h3 style=\"color:#0000cc;text-align:center;\">No Courses Yet..!</h3>";
                } else {
                    $i = 0;
?>
                    <table id="demoTable" cellpadding="30" cellspacing="10" class="datatable">
                    	<thead>
                       <tr>                        
                            <th>&nbsp;</th>
                            <th>S.No.</th>
                            <th>Module Code</th>
                             <th>Module Name</th>
                             <th>Course No</th>
                            <th>Course Name</th>
                            <th>ECTS</th>
                            <th>Year</th>
                             <th>Sem</th>
                            <th>Department</th>
                            <th>Edit</th>                         
                        </tr>
                         </thead>
                        <tbody>
<?php
                    while ($r = mysql_fetch_array($result)) {
                        $i = $i + 1;
                        if ($i % 2 == 0) {
                            echo "<tr class=\"alt\">";
                        } else {
                            echo "<tr>";
                        }
                        echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"d$i\" value=\"" . $r['cid'] . "\" /></td><td>$i</td><td>" . htmlspecialchars_decode($r['mcode'], ENT_QUOTES)
                        . "</td><td>" . htmlspecialchars_decode($r['mname'], ENT_QUOTES) . "</td>"
                         . "</td><td>" . htmlspecialchars_decode($r['ccode'], ENT_QUOTES) . "</td>"
                          . "</td><td>" . htmlspecialchars_decode($r['cname'], ENT_QUOTES) . "</td>"
                           . "</td><td>" . htmlspecialchars_decode($r['ects'], ENT_QUOTES) . "</td>"
                            . "</td><td>" . htmlspecialchars_decode($r['year'], ENT_QUOTES) . "</td>"
                             . "</td><td>" . htmlspecialchars_decode($r['sem'], ENT_QUOTES) . "</td>"
                              . "</td><td>" . htmlspecialchars_decode($r['dept'], ENT_QUOTES) . "</td>"
                        . "<td class=\"tddata\"><a title=\"Edit " . htmlspecialchars_decode($r['ccode'], ENT_QUOTES) . "\"href=\"coursemng.php?edit=" . htmlspecialchars_decode($r['ccode'], ENT_QUOTES) . "\"><img src=\"../images/edit.png\" height=\"30\" width=\"40\" alt=\"Edit\" /></a></td></tr>";
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



