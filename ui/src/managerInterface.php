 
<html>
<head>
    <title>Manager Page</title>
    <link rel="stylesheet" type="text/css" href="style_m.css">
</head>

<body>
<p>view all recipes</p>

<form method="GET" action="managerInterface.php">
    <input type="hidden" id="dispalyRecipesRequest" name="dispalyRecipesRequest">
    <input type="submit" name="dispalyRecipes"></p >
</form>

<hr />
<!--Using Detetion-->
<p>Delete a recipe</p>

<form method ="POST" action="managerInterface.php">
    <input type = "hidden" id = "deleteRequest" name = "deleteRequest">
    Recipe ID you want to delete: <input type="number" name ="deleteID"> <br /><br />
    <p><input type = "submit" value = "Delete" name = "delete"></p>
</form>

<?php
//this tells the system that it's no longer just parsing html; it's now parsing PHP
session_start();

if (isset($_COOKIE['UserID'])) {

    $_SESSION['UserID'] = $_COOKIE['UserID'];
    echo "Hello Manager ".$_SESSION['UserID'].' , welcome to your manage page<br>';
}
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

//prints recipe name and ID and its photo
function printSearchResult($result) {
    echo "<br>Retrieved data from table:<br>";
    echo "<table>";
    echo "<tr><th>ID</th><th>RECIPE_NAME</th><th>PICTURE</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        $picture = '';
        $picResult = executePlainSQL("SELECT PHOTO_URL FROM PHOTOS_INCLUDES WHERE RECIPE_ID = '" . $row['ID'] . "'");
        if ($pictureRow = OCI_Fetch_Array($picResult, OCI_BOTH)) {
            $picture = trim($pictureRow['PHOTO_URL']);
        }
        echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["RECIPE_NAME"] . '</td> <td><img src="' . $picture . '" style="width: 50px; height: 50px;"/></td> </tr>';
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

//delete recipe by given recipe id
function handleDeletionRequest(){
    global $db_conn;
    $RECIPEID = $_POST['deleteID'];
    $numRecipe = executePlainSQL("SELECT COUNT(*) FROM RECIPE_HAS");
    if (is_numeric($RECIPEID) && $RECIPEID > 0) {
        echo 'Delete ', $RECIPEID, 'th recipe.';
        executePlainSQL("DELETE FROM RECIPE_HAS WHERE ID = '". $RECIPEID . "'");
        echo "Delete Successful! <br>";
        OCICommit($db_conn);
    } else echo 'invalid input';
}

//view the table of recipe_has
function handleViewRequest(){
    global $db_conn;
    $result = executePlainSQL("SELECT * FROM RECIPE_HAS");
    printSearchResult($result);}

// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {

        if (array_key_exists('delete', $_POST)) {
            handleDeletionRequest();
        }

        disconnectFromDB();
    }
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('dispalyRecipes', $_GET)) {
            handleViewRequest();
        }
        disconnectFromDB();
    }
}

if (isset($_POST['deleteRequest'])) {
    handlePOSTRequest();
} else if (isset($_GET['dispalyRecipesRequest'])) {
    handleGETRequest();
}
?>
</body>
</html>
