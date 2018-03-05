<?php
if(isset($_POST['btn-submit']))
{
if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}
 
//echo "Socket created";
//Connect socket to remote server
if(!socket_connect($sock , '127.0.0.5' , 8085))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not connect: [$errorcode] $errormsg \n");
}
 

$message1 = "connection established between the socket";

if( ! socket_send ( $sock , $message1 , strlen($message1) , 0))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not send data: [$errorcode] $errormsg \n");
}
socket_close($sock);
}
?>

<html>
  <title>Personalised Recommendation Model of Facebook Advertisements</title>   	

  <body background="./data/feature_list/bg.jpg">
  <h2 style="color:red;">Personalised Recommendation Model of Facebook Advertisements</h2>

  <h3 style="color:blue;"> Facebook Comments Extraction: </h3>
        <form action="database.php" method="post" target="_blank">
          Product/Public Page UserName: <input type="text" name="pname" /><br>
          <br>
          <input type=submit value="Submit" />
          <input type=reset value="Clear All" />
        </form>

  <h3 style="color:blue;">Analysis over comments:</h3>
        <form method="POST">
          <input type=submit name="btn-submit" value="Execute" onclick="alertFunction()"/>          
        </form>
        <script >
        function alertFunction(){
           alert("Execution completed!! ,Go to Downloads folder and open Finaloutput.csv file.."); 
        }
        </script>
  </body>
</html>