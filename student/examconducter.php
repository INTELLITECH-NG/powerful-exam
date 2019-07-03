


<?php

/*
***************************************************
*** Examination Management System               ***
***---------------------------------------------***
*** Developer: Dejene Techane                   ***
*** Title: Exam Conductor                       ***
***************************************************
*/

error_reporting(0);
session_start();
include_once '../include/emsdb.php';
$final=false;
if(!isset($_SESSION['username'])) {
    $_GLOBALS['message']="Session Timeout.Click here to <a href=\"../index.php\">Re-LogIn</a>";
}
 //This is to protect unauthorized users
 else if(!($_SESSION['role']=="Student")){
    unset($_SESSION['username']);
  $_GLOBALS['message']="Please, You are accessing unauthorized page.Click here to <a href=\"../index.php\">Re-LogIn</a>";
 }
else if(isset($_REQUEST['logout']))
{
    //Log out and redirect login page
       unset($_SESSION['username']);
       header('Location: ../index.php');

}
else if(isset($_REQUEST['dashboard'])){
    //redirect to dashboard
    //
     header('Location: stdwelcome.php');

    }else if(isset($_REQUEST['next']) || isset($_REQUEST['summary']) || isset($_REQUEST['viewsummary']))
    {
        //next question
        $answer='unanswered';
        if(time()<strtotime($_SESSION['endtime']))
        {
            if(isset($_REQUEST['markreview']))
            {
                $answer='review';
            }
            else if(isset($_REQUEST['answer']))
            {
                $answer='answered';
            }
            else
            {
                $answer='unanswered';
            }
            if(strcmp($answer,"unanswered")!=0)
            {
                if(strcmp($answer,"answered")==0)
                {
                    $query="update studentquestion set answered='answered',stdanswer='".htmlspecialchars($_REQUEST['answer'],ENT_QUOTES)."' where uid=".$_SESSION['uid']." and testid=".$_SESSION['testid']." and qnid=".$_SESSION['qn'].";";
                }
                else
                {
                    $query="update studentquestion set answered='review',stdanswer='".htmlspecialchars($_REQUEST['answer'],ENT_QUOTES)."' where uid=".$_SESSION['uid']." and testid=".$_SESSION['testid']." and qnid=".$_SESSION['qn'].";";
                }
                if(!executeQuery($query))
                {
                // to do
                $_GLOBALS['message']="Your previous answer is not updated.Please answer once again";
                }
                closedb();
            }
            if(isset($_REQUEST['viewsummary']))
            {
                 header('Location: summary.php');
            }
            if(isset($_REQUEST['summary']))
             {
                     //summary page
                     header('Location: summary.php');
             }
        }
        if((int)$_SESSION['qn']<(int)$_SESSION['tqn'])
        {
        $_SESSION['qn']=$_SESSION['qn']+1;
       
        }
        if((int)$_SESSION['qn']==(int)$_SESSION['tqn'])
        {
           $final=true;
        }

    }
    else if(isset($_REQUEST['previous']))
    {
    // Perform the changes for current question
        $answer='unanswered';
        if(time()<strtotime($_SESSION['endtime']))
        {
            if(isset($_REQUEST['markreview']))
            {
                $answer='review';
            }
            else if(isset($_REQUEST['answer']))
            {
                $answer='answered';
            }
            else
            {
                $answer='unanswered';
            }
            if(strcmp($answer,"unanswered")!=0)
            {
                if(strcmp($answer,"answered")==0)
                {
                    $query="update studentquestion set answered='answered',stdanswer='".htmlspecialchars($_REQUEST['answer'],ENT_QUOTES)."' where uid=".$_SESSION['uid']." and testid=".$_SESSION['testid']." and qnid=".$_SESSION['qn'].";";
                }
                else
                {
                    $query="update studentquestion set answered='review',stdanswer='".htmlspecialchars($_REQUEST['answer'],ENT_QUOTES)."' where uid=".$_SESSION['uid']." and testid=".$_SESSION['testid']." and qnid=".$_SESSION['qn'].";";
                }
                if(!executeQuery($query))
                {
                // to do
                $_GLOBALS['message']="Your previous answer is not updated.Please answer once again";
                }
                closedb();
            }
        }
        //previous question
        if((int)$_SESSION['qn']>1)
        {
            $_SESSION['qn']=$_SESSION['qn']-1;
        }

    }
    else if(isset($_REQUEST['fs']))
    {
        //Final Submission
        header('Location: examtake.php');
    }
?>
<?php
header("Cache-Control: no-cache, must-revalidate");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>EMS-Exam Conducter</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="icon" type="jpg/png" href="../images/logo.png"/>
    <meta http-equiv="CACHE-CONTROL" content="NO-CACHE"/>
    <meta http-equiv="PRAGMA" content="NO-CACHE"/>
    <meta name="ROBOTS" content="NONE"/>
    <link rel="stylesheet" type="text/css" href="../ems.css"/>
    <script type="text/javascript" src="../validate.js" ></script>
    <script type="text/javascript" src="../cdtimer.js" ></script>
    <script type="text/javascript" >
    <!--
        <?php
                $elapsed=time()-strtotime($_SESSION['starttime']);
                if(((int)$elapsed/60)<(int)$_SESSION['duration'])
                {
                    $result=executeQuery("select TIME_FORMAT(TIMEDIFF(endtime,CURRENT_TIMESTAMP),'%H') as hour,TIME_FORMAT(TIMEDIFF(endtime,CURRENT_TIMESTAMP),'%i') as min,TIME_FORMAT(TIMEDIFF(endtime,CURRENT_TIMESTAMP),'%s') as sec from studenttest where uid=".$_SESSION['uid']." and testid=".$_SESSION['testid'].";");
                    if($rslt=mysql_fetch_array($result))
                    {
                     echo "var hour=".$rslt['hour'].";";
                     echo "var min=".$rslt['min'].";";
                     echo "var sec=".$rslt['sec'].";";
                    }
                    else
                    {
                        $_GLOBALS['message']="Try Again";
                    }
                    closedb();
                }
                else
                {
                    echo "var sec=01;var min=00;var hour=00;";
                }
        ?>
        
    -->
    </script>

    </head>
  <body >
      <noscript><h2>For the proper Functionality, You must use Javascript enabled Browser</h2></noscript>
       <?php

        if($_GLOBALS['message']) {
            echo "<div class=\"message\">".$_GLOBALS['message']."</div>";
           
        }
        ?>
      <div id="container">
       <div class="header">                              
                <?php require'../include/header.php';?>                
                </div>
           <form id="testconducter" action="examconducter.php" method="post">
          <div class="menubar" style="text-align:center;">
          
          <?php
         
           //retrieving instructer id to retrieve Instructor name
            $result=executeQuery("SELECT uid from test where testid=".$_SESSION['testid'].";");
           $r1=mysql_fetch_array($result);
            $ins=$r1['uid'];
            //$cid=$r1['cid'];
           
            $result2=executeQuery("SELECT username from user where uid='$ins'");
            $r=mysql_fetch_array($result2);
            $name=$r['username'];
            //$cname=$r['cname'];
            
            $result3=executeQuery("SELECT fullname from instructor where idno='$name'");
            $r3=mysql_fetch_array($result3);
            $fname=$r3['fullname'];?>
              <h2 style="font-family:helvetica,sans-serif;font-weight:bolder;font-size:120%;color:#f50000;padding-top:0.3em;letter-spacing:1px;">EMS:Instructor Name:<?php echo $fname; ?>
              </h2>
          </div>
      <div class="page">
       <h2 style="font-family:helvetica,sans-serif;font-weight:bolder;font-size:120%;color:#f50000;padding-top:0.3em;letter-spacing:1px;">Student IDNO:
       <?php echo $_SESSION['username'];?></h2>
         
          <?php
          if(isset($_SESSION['username']))
          {
                $result=executeQuery("select stdanswer,answered from studentquestion where uid=".$_SESSION['uid']." and testid=".$_SESSION['testid']." and qnid=".$_SESSION['qn'].";");
                $r1=mysql_fetch_array($result);
                $result=executeQuery("select * from question where testid=".$_SESSION['testid']." and qnid=".$_SESSION['qn'].";");
                $r=mysql_fetch_array($result);
                $qtype=htmlspecialchars_decode($r[htmlspecialchars_decode($r['correctanswer'], ENT_QUOTES)], ENT_QUOTES);
                //storing question type to session variables
                $_SESSION['qtype']=$qtype;
                	if(!($qtype=='TRUE'||$qtype=='FALSE')){
                		
          ?>
          <div class="tc">

              <table border="0" width="100%" class="ntab">
                  <tr>
                      <th style="width:10%;"><h3><span id="timer" class="timerclass"></span></h3></th>
                      <th style="width:40%;"><h4 style="color: #af0a36;">MULTIPLE CHOICE QUESTIONS</br>SELECT THE CORRECT ANSWER FROM THE FOLLOWING CHOICES. </h4></th>
                      <!--<th style="width:20%;"><h4 style="color: #af0a36;"><input type="checkbox" name="markreview" value="mark"> Mark for Review</input></h4></th>
                      -->
                  </tr>
                  <tr><td><h4 style="color: #af0a36;"><?php echo $_SESSION['qn']; ?>.</h4></td></tr>
              </table>
             <textarea cols="100" rows="8" name="question" readonly style="width:96.8%;text-align:left;margin-left:2%;margin-top:2px;font-size:120%;font-weight:bold;margin-bottom:0;color:#0000ff;padding:2px 2px 2px 2px;"><?php echo htmlspecialchars_decode($r['question'],ENT_QUOTES); ?></textarea>
              <table border="0" width="100%" class="ntab">
                  <tr><td >A. <input type="radio" name="answer" value="optiona" <?php if((strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"review")==0 ||strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"answered")==0)&& strcmp(htmlspecialchars_decode($r1['stdanswer'],ENT_QUOTES),"optiona")==0 ){echo "checked";} ?>> <?php echo htmlspecialchars_decode($r['optiona'],ENT_QUOTES); ?></input></td></tr>
                  <tr><td >B. <input type="radio" name="answer" value="optionb" <?php if((strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"review")==0 ||strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"answered")==0)&& strcmp(htmlspecialchars_decode($r1['stdanswer'],ENT_QUOTES),"optionb")==0 ){echo "checked";} ?>> <?php echo htmlspecialchars_decode($r['optionb'],ENT_QUOTES); ?></input></td></tr>
                  <tr><td >C. <input type="radio" name="answer" value="optionc" <?php if((strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"review")==0 ||strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"answered")==0)&& strcmp(htmlspecialchars_decode($r1['stdanswer'],ENT_QUOTES),"optionc")==0 ){echo "checked";} ?>> <?php echo htmlspecialchars_decode($r['optionc'],ENT_QUOTES); ?></input></td></tr>
                  <tr><td >D. <input type="radio" name="answer" value="optiond" <?php if((strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"review")==0 ||strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"answered")==0)&& strcmp(htmlspecialchars_decode($r1['stdanswer'],ENT_QUOTES),"optiond")==0 ){echo "checked";} ?>> <?php echo htmlspecialchars_decode($r['optiond'],ENT_QUOTES); ?></input></td></tr>
                  <tr><td>&nbsp;</td></tr>
                  <tr>
                      <th style="width:80%;"><h4><input type="submit" name="<?php if($final==true){ echo "viewsummary" ;}else{ echo "next";} ?>" value="<?php if($final==true){ echo "View Summary" ;}else{ echo "Next";} ?>" class="ssubbtn"/></h4></th>
                      <th style="width:12%;text-align:right;"><h4><input type="submit" name="previous" value="Previous" class="ssubbtn"/></h4></th>
                      <th style="width:8%;text-align:right;"><h4><input type="submit" name="summary" value="Summary" class="ssubbtn" /></h4></th>
                  </tr>
                  
              </table>
              

          </div>
          <?php
          			closedb();
          			}
          			else{
          				?>
          				 <div class="tc">

              <table border="0" width="100%" class="ntab">
                  <tr>
                      <th style="width:10%;"><h3><span id="timer" class="timerclass"></span></h3></th>
                      <th style="width:40%;"><h4 style="color: #af0a36;">TRUE/FALSE QUESTIONS</BR>Select TRUE "if the statement is correct" or FALSE "if the statement is not correct" </h4></th>
                      <!--<th style="width:20%;"><h4 style="color: #af0a36;"><input type="checkbox" name="markreview" value="mark"> Mark for Review</input></h4></th>
                      -->
                  </tr> 
                   <tr><td><h4 style="color: #af0a36;"><?php echo $_SESSION['qn']; ?>.</h4></td></tr>
              </table>
               <textarea cols="100" rows="8" name="question" readonly style="width:96.8%;text-align:left;margin-left:2%;margin-top:2px;font-size:120%;font-weight:bold;margin-bottom:0;color:#0000ff;padding:2px 2px 2px 2px;"><?php echo htmlspecialchars_decode($r['question'],ENT_QUOTES); ?></textarea>
              <table border="0" width="100%" class="ntab">
                  <tr><td>&nbsp;</td></tr>
                  <tr><td ><input type="radio" name="answer" value="optionc" <?php if((strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"review")==0 ||strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"answered")==0)&& strcmp(htmlspecialchars_decode($r1['stdanswer'],ENT_QUOTES),"optionc")==0 ){echo "checked";} ?>> <?php echo htmlspecialchars_decode($r['optionc'],ENT_QUOTES); ?></input></td></tr>
                  <tr><td ><input type="radio" name="answer" value="optiond" <?php if((strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"review")==0 ||strcmp(htmlspecialchars_decode($r1['answered'],ENT_QUOTES),"answered")==0)&& strcmp(htmlspecialchars_decode($r1['stdanswer'],ENT_QUOTES),"optiond")==0 ){echo "checked";} ?>> <?php echo htmlspecialchars_decode($r['optiond'],ENT_QUOTES); ?></input></td></tr>
                  <tr><td>&nbsp;</td></tr>
                  <tr>
                      <th style="width:80%;"><h4><input type="submit" name="<?php if($final==true){ echo "viewsummary" ;}else{ echo "next";} ?>" value="<?php if($final==true){ echo "View Summary" ;}else{ echo "Next";} ?>" class="ssubbtn"/></h4></th>
                      <th style="width:12%;text-align:right;"><h4><input type="submit" name="previous" value="Previous" class="ssubbtn"/></h4></th>
                      <th style="width:8%;text-align:right;"><h4><input type="submit" name="summary" value="Summary" class="ssubbtn" /></h4></th>
                  </tr>
                  
              </table>
              

          </div>
          <?php			
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

