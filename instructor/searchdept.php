                      
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
} 
else if($_REQUEST['search']){
	 //if($_REQUEST['dept'])
        //storing department into session variable
        if (strcmp($_REQUEST['dept'], "<Choose the Department>") == 0){
        	//$_GLOBALS['message']="Please select Department first";
        	header('Location:searchdept.php');
}
        else{
       $_SESSION['dept']=$_REQUEST['dept'];
        header('Location: exammng.php');
	}
}
?>
<html>
    <head>
        <title>EMS-Manage Exam</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="icon" type="jpg/png" href="../images/logo.png"/>
        <link rel="stylesheet" type="text/css" href="../ems.css"/> 
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
                <div class="menubar">
                <form name="testmng" action=" " method="post">

                    <ul id="menu">
<?php
if (isset($_SESSION['username'])) {
    // Navigations
?>
                        <li><input type="submit" value="LogOut" name="logout" class="subbtn" title="Log Out"/></li>
                        <li><input type="submit" value="DashBoard" name="dashboard" class="subbtn" title="Dash Board"/></li>
                       
                        
<?php 
} ?>
                    </ul>

                </div>
                <div class="page">
<?php
if (isset($_SESSION['username'])) {
    // To display the Help Message
      echo "<div class=\"pmsg\" style=\"text-align:center;\">Exam Management </div>";
    ?>
			
                                    
                                    
                    <table cellpadding="20" cellspacing="20" style="text-align:left;" >
                        <tr>
                            <td>Department Name</td>
                            <td>
                                <select name="dept" id="textbox">
                                    <option selected value="<Choose the Department>">&lt;Choose the Department&gt;</option>
<?php
        $result = executeQuery("select dept from course ;");
        
        while ($r = mysql_fetch_array($result)) {

            echo "<option value=\"" . $r['dept'] . "\">" . htmlspecialchars_decode($r['dept'], ENT_QUOTES) . "</option>";
        }
        
        
        closedb();
?>
                                </select></td>
                               <td> <input type="submit" class="subbtn" color="$ffffff" name="search" value="Search">
                            </td>

                        </tr>
                       
                    </table>

<?php 
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