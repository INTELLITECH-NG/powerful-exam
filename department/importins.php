<?php

/*
***************************************************
*** Examination ment ManageSystem               ***
***---------------------------------------------***
*** Developer: Dejene Techane                   ***
*** Title: Import student profile               ***
***************************************************
*/

/* Procedure
*********************************************
 * ----------- *
 * PHP Section *
 * ----------- *
Step 1: Perform Session Validation.
  case 1:Submit to import file to the system.
 * ------------ *
 * HTML Section *
 * ------------ *
Step 2: Display the html form
	case 1: Import form to import excel file
	

*********************************************
*/

error_reporting(0);
/********************* Step 1 *****************************/
session_start();
include_once '../include/emsdb.php';
        if(!isset($_SESSION['username'])){
            $_GLOBALS['message']="Session Timeout.Click here to <a href=\"../index.php\">Re-LogIn</a>";
        }
        //This is to protect unauthorized users
 else if(!($_SESSION['role']=="Department")){
    unset($_SESSION['username']);
  $_GLOBALS['message']="Please, You are accessing unauthorized page.Click here to <a href=\"../index.php\">Re-LogIn</a>";
 }
        else if(isset($_REQUEST['logout'])){
           unset($_SESSION['username']);
            $_GLOBALS['message']="You are Loggged Out Successfully.";
            header('Location: ../index.php');
        }else if(isset($_REQUEST['ins'])){
        	header('Location:insmng.php');       	
        }
        else if(isset($_REQUEST['import'])){
	
		if (($_FILES['file']['size'] > 0)) {             	
          $file = $_FILES['file']['tmp_name'];
		  $handle = fopen($file, "r"); 		  
     	$c = 0;
		while(($filesop = fgetcsv($handle, 10000, ",")) !== false)
		{
			$idno = $filesop[0];
			$name = $filesop[1];
			$sex = $filesop[2];
			$nation = $filesop[3];
			$rank = $filesop[4];
			$phone = $filesop[5];
			$email = $filesop[6];
			//ecrypting password
			$pass = md5($filesop[7]);
			$role = "Instructor";			
			$c++;	
        	
			if($c>1)
			{
		     $sql1 = executeQuery("INSERT INTO instructor(idno,fullname, sex,nationality,academicrank,phoneno,email,uid) VALUES ('$idno','$name','$sex','$nation','$rank','$phone','$email','".$_SESSION['uid']."')");
			 $sql2=executeQuery("INSERT INTO user (username,password,role) VALUES('$idno','$pass','$role')");
		}
		}//end of while loop
		
			if($sql1 || $sql2){
			$_GLOBALS['message']="Your database has imported successfully. Click <a href=\"insmng.php\">Here</a>  to view.";
			
			}else{
			$_GLOBALS['message']="Sorry! There may be duplication of data.";
			}
	       }
	  	
  	  else
  	  $_GLOBALS['message']= "You are opening Empty file, try again.";
		    
	closedb();
	}
        
?>

<html>
    <head>
        <title>EMS-IMPORT INSTRUCTOR PROFILE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
         <link rel="icon" type="jpg/png" href="../images/logo.png"/>
        <link rel="stylesheet" type="text/css" href="../ems.css"/>
    </head>
    <body>
        <?php
       /********************* Step 2 *****************************/
        if(isset($_GLOBALS['message'])) {
            echo "<div class=\"message\">".$_GLOBALS['message']."</div>";
        }
        ?>
        <div id="container">
             <div class="header">                              
                <?php require'../include/header.php';?>                
                </div>
            <div class="menubar">

                <form name="ins" action="insmng.php" method="post">
                    <ul id="menu">
                        <?php if(isset($_SESSION['username'])){ ?>
                        <li><input type="submit" value="LogOut" name="logout" class="subbtn" title="Log Out"/></li>
                          <li><input type="submit" value="Manage Instructor" name="ins" class="subbtn" title="Manage Instructor"/></li>
                        <?php } ?>
                    </ul>
                </form>
            </div>
       <div class="page">
          <?php if(isset($_SESSION['username'])){ ?>

        <form name="import" method="post" enctype="multipart/form-data">
        <table cellpadding="20" cellspacing="20" style="text-align:left;margin-left:15em" >
         <tr>
         <td><div class="help">NOTE: To import Staff profile from excel follow the following steps:</br>
         1.Open new spreadsheet and format like the following</br></div></td></tr>
         <tr><td><img src="../images/stf.png"/></br></td>
         <tr><td><div class="help">2. Then save it as &lt;filename &gt;.csv(e.g instructorlist.csv)</br>
         3.Then go back to the system and click on the browse file button and open .csv file format</br>
         4. Finally, Click on Load File Into Database button to load excel file to database.</br>
         NB:The file extension should be .csv file format, Unless other file format should not be possible!</div></td></tr>
         <tr>
    	<td><input type="file" name="file" />
        <input type="submit" name="import" class="subbtn" value="Load File Into Database" /></td>
        </tr>
        </table>
    </form>
                <?php }?>

            </div>

          <div id="footer">
           <?php include'../include/footer.php';?>
           
           
           </div>
      </div>
  </body>
</html>
