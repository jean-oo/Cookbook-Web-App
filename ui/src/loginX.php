<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.  
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values
 
  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the 
  OCILogon below to be your ORACLE username and password -->

<html>
<head>
    <title>CPSC 304 Recipe Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>


<body>
<h2>Login</h2>


<form method="POST" action="loginX.php">
    <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
    <input type="hidden" id="searchUsernameRequest" name="searchUsernameRequest"> 
    UserID: <input type="number" name="UserID"> <br /><br />
    Password: <input type="text" name="PSWD"> <br /><br />
    <p><input type="submit" value="Login"  name="loginSubmit"></p>
   

</form>

<hr />

<h2>Reset Password</h2>

<form method="POST" action="loginX.php">
    <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
    <input type="hidden" id="restPassRequest" name="restPassRequest"> 
    UserID: <input type="number" name="RUserID"> <br /><br />
    New Password: <input type="text" name="newPSWD"> <br /><br />
    <input type="submit" value="Reset Password"  name="restPassword"></p>

</form>

<hr />

<h2>Sign Up Today</h2>

<form method="POST" action="loginX.php">
    <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
    <input type="hidden" id="signupRequest" name="signupRequest"> 
    UserName: <input type="text" name="userName"> <br /><br />
    Email: <input type="text" name="email"> <br /><br />
    Password: <input type="text" name="PSWD"> <br /><br />
    Cooking Level: <input type="text" name="CL"> <br /><br />
    Postal Code: <input type="text" name="PC"> <br /><br />
    City: <input type="text" name="city"> <br /><br />
    Province: <input type="text" name="province"> <br /><br />
    House Number: <input type="text" name="HN"> <br /><br />
    Street: <input type="text" name="street"> <br /><br />
    <input type="submit" value="Sign Up"  name="signupsubmit"></p>

</form>




<?php
//this tells the system that it's no longer just parsing html; it's now parsing PHP

session_start();
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = NULL; // edit the login credentials in connectToDB()
$show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

function debugAlertMessage($message) {
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;

    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }

    return $statement;
}

function executeBoundSQL($cmdstr, $list) {
    /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
In this case you don't need to create the statement several times. Bound variables cause a statement to only be
parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
See the sample code below for how this function is used */

    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            OCIBindByName($statement, $bind, $val);
            unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
}

function printResult($result) { //prints results from a select statement
    echo "<br>Retrieved data from table RECIPE:<br>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        //echo "<tr><td>" . $row["ACCOUNT_ID"] . "</td><td>" . $row["USERNAME"] . "</td></tr>"; 
        echo $row[0].$row[1];
    }

    echo "</table>";
}

function connectToDB() {
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = OCILogon("ora_jeanoo", "a72070014", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB() {
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}

function handleUpdateRequest() {
    global $db_conn;

    $old_name = $_POST['oldName'];
    $new_name = $_POST['newName'];

    // you need the wrap the old name and new name values with single quotations
    executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");

    
    OCICommit($db_conn);
}
function handleRestPasswordRequest() {
    global $db_conn;

    $RUserID = $_POST['RUserID'];
    $newPSWD = $_POST['newPSWD'];

    // you need the wrap the old name and new name values with single quotations
    executePlainSQL("UPDATE ACCOUNT SET ACCOUNT_PASSWORD='". $newPSWD . "' WHERE ACCOUNT_ID='" . $RUserID . "'");
    echo "<br> Reset Successfully! <br>";
    //$RIGHT_PASSWORD = executePlainSQL("SELECT ACCOUNT_PASSWORD FROM ACCOUNT WHERE ACCOUNT_ID='". $RUserID . "'");
    //$row = OCI_Fetch_Array($RIGHT_PASSWORD, OCI_BOTH);
   // echo "<tr><td>" . "newpass".$row["ACCOUNT_PASSWORD"] . "</td></tr>";

    OCICommit($db_conn);
}
function handleLoginRequest() {
    global $db_conn;

    $UserID = $_POST['UserID'];
    $PSWD = $_POST['PSWD'];

    // you need the wrap the old name and new name values with single quotations
    $RIGHT_PASSWORD = executePlainSQL("SELECT ACCOUNT_PASSWORD FROM ACCOUNT WHERE ACCOUNT_ID='". $UserID . "'");

  $row = OCI_Fetch_Array($RIGHT_PASSWORD, OCI_BOTH);
 // echo "<tr><td>" . gettype($row["ACCOUNT_PASSWORD"]) . "</td></tr>";
  //printResult($RIGHT_PASSWORD);
   // printf("r is pswd \n:%s",$row["ACCOUNT_PASSWORD"]);
   // printf("pswd:%d",$PSWD);
    //printf("id:%d",$UserID);
    if($PSWD==(int)$row["ACCOUNT_PASSWORD"]){
        echo "<br> login successfully <br>";
        $_SESSION['UserID'] = $UserID;
        setcookie('UserID', $UserID, time()+1);
        if($UserID==1){
            header('location:https://www.students.cs.ubc.ca/~jeanoo/managerInterface.php');
        exit;
        } else{
        header('location:https://www.students.cs.ubc.ca/~jeanoo/userInterface.php?attr='.$UserID.' ');
        exit;}
    }
    else if($PSWD!=$row["ACCOUNT_PASSWORD"]){echo "<br> wrong password <br>";}
    OCICommit($db_conn);
}


function handleInsertRequest() {
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['insNo'],
        ":bind2" => $_POST['insName']
    );

    $alltuples = array (
        $tuple
    );

    executeBoundSQL("insert into demoTable values (:bind1, :bind2)", $alltuples);
    OCICommit($db_conn);
}

function handleSearchByKeyword() {
    global $db_conn;
    $keyword = $_GET['keyword'];

    $result = executePlainSQL("SELECT * FROM RECIPE_HAS WHERE RECEIPE_NAME LIKE'". $keyword . "'");

    printResult($result);

}

function handleSignupRequest(){
    global $db_conn;
    $MAX1=executePlainSQL("SELECT ACCOUNT_ID FROM account WHERE account_id >= all(SELECT account_id FROM account)");
    $row = OCI_Fetch_Array($MAX1, OCI_BOTH);
        //echo "<tr><td>" . $row["ACCOUNT_ID"] . "</td><td>" . $row["USERNAME"] . "</td></tr>";
    //echo $row[0];
    $ID=(int)$row[0]+1;

    echo "<tr><td>Welcome, your new ID is " . $ID . "</td></tr>";

    //insert into ACCOUNT
    $userName=$_POST['userName'];
    $email=$_POST['email'];
    $PSWD=$_POST['PSWD'];

    $tuple = array (
         ":bind1" => $ID,
         ":bind2" => $_POST['userName'],
         ":bind3" => $_POST['email'],
         ":bind4" => $_POST['PSWD'],
     );


     $alltuples = array (
         $tuple
     );
     executeBoundSQL("insert into ACCOUNT values (:bind1, :bind2, :bind3, :bind4)", $alltuples);

//insert into location2
$tuple_l2 = array (
    ":bind1" => $_POST['PC'],
    ":bind2" => $_POST['city'],
    ":bind3" => $_POST['province'],
);
$pc=$_POST['PC'];
$r=executePlainSQL("SELECT postal_code from location2 where EXISTS(
    SELECT * FROM location2 WHERE postal_code='". $pc . "')");
$rr= OCI_Fetch_Array($r, OCI_BOTH);

$alltuples_l2 = array (
    $tuple_l2
);
//echo "<tr><td>" .'postal code'. $rr[0]. "</td></tr>";
if($rr[0] == NULL){
executeBoundSQL("insert into LOCATION2 values (:bind1, :bind2, :bind3)", $alltuples_l2);
}

//insert into location1
$tuple_l1 = array (
    ":bind1" => $_POST['PC'],
    ":bind2" => $_POST['HN'],
    ":bind3" => $_POST['street'],
);

$alltuples_l1 = array (
    $tuple_l1
);
executeBoundSQL("insert into LOCATION1 values (:bind1, :bind2, :bind3)", $alltuples_l1);


    //insert into useraccount
    $tuple_u = array (
        ":bind1" => $ID,
        ":bind6" => $_POST['CL'],
        ":bind7" => $_POST['PC'],
        ":bind8" => $_POST['HN'],
        ":bind5" => $_POST['street'],
    );

    $alltuples_u = array (
        $tuple_u
    );
    executeBoundSQL("insert into USER_ACCOUNT values (:bind1, :bind6, :bind7, :bind8, :bind5)", $alltuples_u);

   OCICommit($db_conn);

}


// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        } else if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        } else if (array_key_exists('restPassRequest', $_POST)) {
            handleRestPasswordRequest();
        } else if (array_key_exists('searchUsernameRequest', $_POST)) {
            handleLoginRequest();
        }else if (array_key_exists('signupRequest', $_POST)) {
            handleSignupRequest();
        }

        disconnectFromDB();
    }
}





// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('searchRecipes', $_GET)) {
            handleSearchByKeyword();
        } else if (array_key_exists('displayTuples', $_GET)) {
            handleDisplayRequest();
        } else if(array_key_exists('loginSubmit', $_GET)){
            handleLoginRequest();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])|| isset($_POST['restPassword'])|| isset($_POST['loginSubmit'])|| isset($_POST['signupsubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])) {
    handleGETRequest();
} else if (isset($_GET['displayTupleRequest'])) {
    handleGETRequest();
}
?>
</body>
</html>
