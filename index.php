



 <?php

 /*
***************************************************
*** Online Examination System                   ***
***---------------------------------------------***
*** Developer: Dejene Techane                   ***
*** Title:  Users Authentication                ***
***************************************************
*/

 /* Procedure
*********************************************

 * ----------- *
 * PHP Section *
 * ----------- *
Step 1: Event to Process...
           Case 1 : Authenticate
       

 * ------------ *
 * HTML Section *
 * ------------ *
Step 2: Display the Html page to receive Authentication Parameters(Name & Password).

*********************************************
*/
 
      error_reporting(0);
      session_start();
      include_once 'include/emsdb.php';

     if($_REQUEST['submit'])
      {
/***************************** Step 1 : Case 1****************************/
 //Perform Authentication
 //encrypting password use md5
 	     $pass=md5(htmlspecialchars($_REQUEST['password'],ENT_QUOTES));
          $result=executeQuery("select * from user where username='".htmlspecialchars($_REQUEST['uname'],ENT_QUOTES)."' and password='".$pass."';");
          if(mysql_num_rows($result)>0)
          {

              $r=mysql_fetch_array($result);
               $role=$r['role'];
               //echo $role;
              if(strcmp(htmlspecialchars($r['password'],ENT_QUOTES),$pass)==0)
              {
                  $_SESSION['username']=htmlspecialchars_decode($r['username'],ENT_QUOTES);
                  $_SESSION['uid']=$r['uid'];
                  //storing user role into session variable to protect unauthorized users.
                  $_SESSION['role']=$r['role'];
                  unset($_GLOBALS['message']);
                 
                 // echo $role;
                  if($role=='Administrator'){
                  	  
                  header('Location: admin/admwelcome.php');
                  }
                  else if($role=='Department'){
                 header('Location: department/deptwelcome.php');
                  }
                 else if($role=='Instructor'){
                 header('Location: instructor/inswelcome.php');
                  }
                  else if($role=='Student'){
                 header('Location: student/stdwelcome.php');
                  }
                  	else{
                  		 $_GLOBALS['message']="Check Your user name and Password.";
                  	}
                  	
                  	  
              }else
          {
              $_GLOBALS['message']="Check Your user name and Password.";
          }

          }
          else
          {
              $_GLOBALS['message']="Check Your user name and Password.";
          }
          closedb();
      }
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>EMS</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
     <link rel="icon" type="jpg/png" href="images/logo.png"/>
    <link rel="stylesheet" type="text/css" href="ems.css"/>
  </head>
  <body>
      <?php

        if($_GLOBALS['message'])
        {
         echo "<div class=\"message\">".$_GLOBALS['message']."</div>";
        }
      ?>
      
      <div id="container">
            
    <div class="header">
          <img style="margin:10px 2px 2px 10px;float:left;" height="53" width="80" src="images/logo.png" alt="ems"/>
         <h3 class="headtext"> &nbsp;Examination Management System </h3><h3 style="color:#ffffff;text-align:center;margin:0 0 5px 5px;"><i>...Ambo University...

             </i></h3>
            </div>
     <form id="stdloginform" action="index.php" method="post">
      <div class="menubar">
       
       <ul id="menu">
                   
                    </ul>

      </div>
      <div class="page">
              
              <table cellpadding="30" cellspacing="10" >
            
              <tr>
                  <td>User Name</td>
                  <td><input type="text" tabindex="1" name="uname" value="" size="16" /></td>

              </tr>
              <tr>
                  <td>Password</td>
                  <td><input type="password" tabindex="2" name="password" value="" size="16" /></td>
              </tr>

              <tr>
                  <td colspan="2">
                      <input type="submit" tabindex="3" value="Log In" name="submit" class="subbtn" />
                  </td><td></td>
              </tr>
            </table>


      </div>
       </form>

    <div id="footer">
    <?php include'include/footer.php';?>
             </div>
      </div>
  </body>
</html>
