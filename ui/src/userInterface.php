<html>
<head>
    <title>CPSC 304 Recipe Project</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

<h2>Post Recipe</h2>
<hr />
<form method="POST" action="userInterface.php">
    <input type="hidden" id="postRecipe" name="postRecipe">
    RecipeName: <input type="text" name="RecipeName"> <br /><br />
    RecipeDetails: <input type="text" name="RecipeDetails"> <br /><br />
    RecipeDifficulty: <input type="number" name="RecipeDifficulty"> <br /><br />

    <input type="submit" value="postRecipe"  name="postRecipeSubmit"></p>

</form>

<h2>Search</h2>
<!--Using Selection-->
<p>Search recipes by keywords</p>

<form method="GET" action="userInterface.php">
    <input type="hidden" id="searchRecipesByKeywords" name="searchRecipesByKeywords">
    Keyword of Recipe Name(capital the inital letter): <input type="text" name="keyword"> <br /><br />
    <p><input type="submit" value="Search" name="search"></p>
</form>

<hr />
<!--Using Join-->
<p>Search recipes by ingredients</p>

<form method="GET" action="userInterface.php">
    <input type="hidden" id="searchIngredient" name="searchIngredient">
    Ingredient Name(please in lowercase): <input type="text" name="keyword"> <br /><br />
    <p><input type="submit" value="Search" name="search"></p>
</form>

<hr />
<!--Using Projection-->
<p>Search recipes by methods</p>

<form method="GET" action="userInterface.php">
    <input type="hidden" id="searchMethod" name="searchMethod">
    Method Name(please in lowercase): <input type="text" name="keyword"> <br /><br />
    <p><input type="submit" value="Search" name="search"></p>
</form>

<hr />


<p>Search avgRate for recipe</p>

<form method="GET" action="userInterface.php">
    <input type="hidden" id="avgRate" name="avgRate">
    Recipe Name(capital the inital letter): <input type="text" name="keyword"> <br /><br />
    <p><input type="submit" value="Search" name="search"></p>
</form>

<hr />
<h2>Recommendation</h2>
<!--Using nested aggregation with group by-->
<p>Recommendation by the highest avg score</p>
<form method="GET" action="userInterface.php">
    <input type="hidden" id="displaytheHighestAvgScoreRequest" name="displaytheHighestAvgScoreRequest">
    <input type="submit" value="get a high score recommendation!" name="displaytheHighestAvgScore"></p>
</form>

<hr />
<!--Using aggregation with group by-->
<p>Recommendation by the number of comments for each recipe name</p>
<form method="GET" action="userInterface.php">
    <input type="hidden" id="displaythePopularRecipeRequest" name="displaythePopularRecipeRequest">
    <input type="submit" value="views popular recipes!" name="displaythePopularRecipe"></p>
</form>
<hr />
<!--Using aggregation with having-->
<p>Recommendation by the number of comments(>2) for each recipe name</p>
<form method="GET" action="userInterface.php">
    <input type="hidden" id="displaytheMostPopularRecipeRequest" name="displaytheMostPopularRecipeRequest">
    <input type="submit" value="get the most popular recipes!" name="displaytheMostPopularRecipe"></p>
</form>
<hr />

<!--Using division-->
<h2>aviod certain food?</h2>
<p>Get your wanted diet!</p>
<form method="POST" action="userInterface.php">
    <input type="radio" id="displayDivisionRecipeRequest1" name="ans" value="milk">
    <label for="ingredient1">I don't want food containing milk</label><br>
    <input type="radio" id="displayDivisionRecipeRequest2" name="ans" value="fat">
    <label for="ingredient2">I don't want food containing fat</label><br>
    <input type="radio" id="displayDivisionRecipeRequest3" name="ans" value="Bacon">
    <label for="ingredient3">I don't want food containing bacon</label><br><br>
    <input type="submit" value="Submit" name="displayDivisionRecipeRequest"></p>
</form>

<?php

session_start();

if (isset($_COOKIE['UserID'])) {

    $_SESSION['UserID'] = $_COOKIE['UserID'];
    echo "Hiiii! ".$_SESSION['UserID'].' , welcome to food heaven<br>';
}

$success = True;
$db_conn = NULL;
$show_debug_alert_messages = False;

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
    echo "<tr><th>ID</th><th>  </th><th>RECIPE_NAME</th><th>  </th><th>STEPS</th><th>  </th><th>PICTURE</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        $picture = '';
        $picResult = executePlainSQL("SELECT PHOTO_URL FROM PHOTOS_INCLUDES WHERE RECIPE_ID = '" . $row['ID'] . "'");
        if ($pictureRow = OCI_Fetch_Array($picResult, OCI_BOTH)) {
            $picture = trim($pictureRow['PHOTO_URL']);
        }
        echo "<tr><td>" . $row["ID"] . "</td><td>  </td><td>" . $row["RECIPE_NAME"] . "</td><td>  </td><td>" . $row["DETAILS"] . '</td><td>  </td><td><img src="' . $picture . '" style="width: 50px; height: 50px;"/></td></tr>';
    }
    echo "</table>";
}
// print the average score of the selected recipe
function printScoreResult($result) {
    echo "<br>This is the average score for the recipe: ";
    echo "<table>";
    $row = OCI_Fetch_Array($result, OCI_BOTH);
    if ($row[0] != NULL) {

        echo $row[0];

        echo "</table>";
    } else echo "<br>Not be rated yet";
}
//prints the name and steps of the recipe with maximum avg score
function printHRResult($result) {
    echo "<br>Retrieved data from table:<br>";
    echo "<table>";
    echo "<tr><th>RECIPE_NAME</th><th>DETAILS</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["RECIPE_NAME"] . "</td><td>" . $row["DETAILS"] . "</td></tr>";
    }
    echo "</table>";
}

// print the recipe names and corresponding number of comments
function printPRResult($result) {
    //echo "<br>hope it works<br>";
    echo "<table>";
    echo "<tr><th>#COMMENTS</th><th>RECIPE_NAME</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["COUNT(C.ID)"] . "</td><td>" . $row["RECIPE_NAME"] . "</td></tr>";
    }
    echo "</table>";
}
// print the recipe names
function printDRResult($result) {
    //echo "<br>hope it works<br>";
    echo "<table>";
    echo "<tr><th>RECIPE_NAME</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["RECIPE_NAME"] . "</td></tr>";

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

// Select recipes from where keyword likes recipe names
function handleSearchByKeyword() {
    global $db_conn;
    $keyword = $_GET['keyword'];
    $result = executePlainSQL("SELECT * FROM RECIPE_HAS r WHERE r.RECIPE_NAME LIKE '%". $keyword . "%'");
    printSearchResult($result);
}
// project into r.ID and r.recipe_name
function handleSearchByMethod() {
    global $db_conn;
    $keyword = $_GET['keyword'];
    $result = executePlainSQL("SELECT r.ID, r.RECIPE_NAME
    FROM RECIPE_HAS r, typebymethod m, recipeMethod rm
    WHERE m.method LIKE '%". $keyword . "%' and m.ID = rm.method_ID and rm.recipe_ID = r.ID");
    printSearchResult($result);
}
//join contain and recipe_has
function handleSearchByIngredient() {
    global $db_conn;
    $keyword = $_GET['keyword'];
     $ID = $_GET['attr'];
     echo $ID;
    $result = executePlainSQL("SELECT *
    FROM CONTAIN c, RECIPE_HAS r
    WHERE c.INGREDIENT_NAME LIKE '%". $keyword . "%' and c.recipe_ID = r.ID");
    printSearchResult($result);
}
// find the average rating given recipename,
function handleFindAVGScore() {
    global $db_conn;
    $keyword = $_GET['keyword'];
    $result = executePlainSQL("SELECT AVG(score) FROM RATING ra, RECIPE_HAS r where r.id = ra.recipe_id and r.RECIPE_NAME LIKE '%". $keyword . "%'");
    printScoreResult($result);
}

// List the number of comments for each recipe name in decreasing order(Aggregation with group by)
function handleDisplayPopularRequest(){
    global $db_conn;
    $result = executePlainSQL("SELECT count(c.id), r.recipe_name
    from recipe_has r, comment1 c
    where r.id = c.recipe_id
    group by r.recipe_name
    ORDER BY COUNT(c.id) DESC");
    printPRResult($result);}
// return the recipe name and steps with highest average rating(nested Aggregation with group by)
function handleDisplayRequest(){
    global $db_conn;
    $result = executePlainSQL("SELECT r.recipe_name,r.details
    from recipe_has r, rating ra3
    where ra3.recipe_id = r.id and
        ra3.score = (SELECT MAX(AVG(score)) FROM RATING ra, RECIPE_HAS r where r.id = ra.recipe_id group by r.recipe_name)");
    printHRResult($result);}
// LIST THE NUMBER OF COMMENTS FOR EACH RECIPE NAME in decreasing order, ONLY INCLUDES RECIPE NAMES WITH MORE THAN 1 COMMENT.
// Aggregation with having
function handleDisplayMostPopularRequest(){
    global $db_conn;
    $result = executePlainSQL("SELECT count(c.id), r.recipe_name
    from recipe_has r, comment1 c
    where r.id = c.recipe_id
    group by r.recipe_name
    having count(c.id) > 1
    ORDER BY COUNT(c.id) DESC");
    printPRResult($result);}
//list the recipe names without selected gradient(Division)
function handleDivisionRecipeRequest(){
    global $db_conn;
    $keyword = $_POST['ans'];
    echo 'There are the recipes without ', $keyword;
    $result = executePlainSQL("SELECT r1.recipe_name
    from recipe_has r1
    minus
    select r2.recipe_name
    from recipe_has r2, contain c
    where r2.id = c.recipe_id and c.ingredient_name LIKE '%". $keyword . "%'");
    printDRResult($result);}

function handlePostRecipe() {
    global $db_conn;

    // get current maximum recipe id
    $MAX_RECIPE_ID=executePlainSQL("SELECT ID FROM recipe_has WHERE ID >= all(SELECT ID FROM recipe_has)");
    $row = OCI_Fetch_Array($MAX_RECIPE_ID, OCI_BOTH);
        //echo "<tr><td>" . $row["ACCOUNT_ID"] . "</td><td>" . $row["USERNAME"] . "</td></tr>";
    //echo $row[0];
    $ID=(int)$row[0]+1;

    echo "<tr><td>Welcome, your recipe ID is " . $ID . "</td></tr>";

    // get region id
     $RegionID = executePlainSQL("SELECT ID FROM typebyregion1 WHERE LOCAL_DISH ==  ");

    //insert into table RECIPE_HAS
    $RecipeName=$_POST['RecipeName'];
    $CreateTime = date("Y-m-d");
    $RecipeDetails=$_POST['RecipeDetails'];
    $RecipeDifficulty=$_POST['RecipeDifficulty'];
    $_SESSION['UserID'] = $_COOKIE['UserID'];
    $USER_ACCOUNT_ID = $_SESSION['UserID']


    $tuple = array (
         ":bind1" => $ID,
         ":bind2" => $CreateTime,
         ":bind3" => $USER_ACCOUNT_ID,
         ":bind4" => $REGION_ID,
         ":bind5" => $RecipeName,
         ":bind6" => $RecipeDetails,
         ":bind7" => $RecipeDifficulty,
     );


     $alltuples = array (
         $tuple
     );
     executeBoundSQL("insert into RECIPE_HAS values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7)", $alltuples);
//
// //insert into location2
// $tuple_l2 = array (
//     ":bind1" => $_POST['PC'],
//     ":bind2" => $_POST['city'],
//     ":bind3" => $_POST['province'],
// );
// $pc=$_POST['PC'];
// $r=executePlainSQL("SELECT postal_code from location2 where EXISTS(
//     SELECT * FROM location2 WHERE postal_code='". $pc . "')");
// $rr= OCI_Fetch_Array($r, OCI_BOTH);
//
// $alltuples_l2 = array (
//     $tuple_l2
// );
// //echo "<tr><td>" .'postal code'. $rr[0]. "</td></tr>";
// if($rr[0] == NULL){
// executeBoundSQL("insert into LOCATION2 values (:bind1, :bind2, :bind3)", $alltuples_l2);
// }
//
// //insert into location1
// $tuple_l1 = array (
//     ":bind1" => $_POST['PC'],
//     ":bind2" => $_POST['HN'],
//     ":bind3" => $_POST['street'],
// );
//
// $alltuples_l1 = array (
//     $tuple_l1
// );
// executeBoundSQL("insert into LOCATION1 values (:bind1, :bind2, :bind3)", $alltuples_l1);
//
//
//     //insert into useraccount
//     $tuple_u = array (
//         ":bind1" => $ID,
//         ":bind6" => $_POST['CL'],
//         ":bind7" => $_POST['PC'],
//         ":bind8" => $_POST['HN'],
//         ":bind5" => $_POST['street'],
//     );
//
//     $alltuples_u = array (
//         $tuple_u
//     );
//     executeBoundSQL("insert into USER_ACCOUNT values (:bind1, :bind6, :bind7, :bind8, :bind5)", $alltuples_u);

   OCICommit($db_conn);

}

// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        } else if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        } else if (array_key_exists('displayDivisionRecipeRequest', $_POST)) {
            handleDivisionRecipeRequest();
        } else if (array_key_exists('postRecipe', $_POST)){
        handlePostRecipe();
        }
        disconnectFromDB();
    }
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('displaytheHighestAvgScore', $_GET)) {
            handleDisplayRequest();
        } else if (array_key_exists('displaytheMostPopularRecipe', $_GET)) {
            handleDisplayMostPopularRequest();
        } else if (array_key_exists('displaythePopularRecipe', $_GET)) {
            handleDisplayPopularRequest();
        } else if (array_key_exists('searchRecipesByKeywords', $_GET)) {
            handleSearchByKeyword();
        } else if (array_key_exists('searchMethod', $_GET)) {
            handleSearchByMethod();
        }  else if (array_key_exists('searchIngredient', $_GET)) {
            handleSearchByIngredient();
        }  else if (array_key_exists('avgRate', $_GET)) {
            handleFindAVGScore();
        }
        disconnectFromDB();
    }
}

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])
    || isset($_POST['displayDivisionRecipeRequest'])|| isset($_POST['postRecipe'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest']) || isset($_GET['displaytheHighestAvgScoreRequest'])
    || isset($_GET['displaythePopularRecipeRequest']) || isset($_GET['displaytheMostPopularRecipeRequest'])
    || isset($_GET['displayDivisionRecipeRequest1']) || isset($_GET['displayDivisionRecipeRequest2'])
    || isset($_GET['displayDivisionRecipeRequest3']) || isset($_GET['searchRecipesByKeywords'])
    || isset($_GET['searchMethod']) || isset($_GET['searchIngredient']) || isset($_GET['avgRate']))
{
    handleGETRequest();
}

?>
</body>
</html