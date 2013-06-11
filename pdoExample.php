<!-- index.php -->

<form action="" method="GET">

Search:<input type="text" name="search">

<br/>

New:<input type="text" placeholder="Title" name="title">

<br/>

<input type="submit" value="Submit">

</form>

<?php

/********************

* Helper Functions *

********************/

function m($msg) {

    echo $msg."<br/>";

}

function p($obj, $msg = null) {

    m("<b>$msg</b><pre>".print_r($obj, true)."</pre>");

}

function h($heading) {

    m("<br/><br/>===================================");

    m($heading);

    m("===================================<br/><br/>");

}

function tryEx($PDOq, $values = null) {

    try {

        p($values, $PDOq->queryString . "<br/>values:");

        $success = $PDOq->execute($values); //Run prepared query, injecting user-supplied data

        if(stristr($PDOq->queryString, "select"))

            p($PDOq->fetchAll(PDO::FETCH_ASSOC), "results:"); //Display prepared query results

        else {

            $result = ($success) ? "Success!" : "Failure!";

            p("<b>".$result."</b>", "results:");

        }

    } catch(PDOException $e) {

        p($e, $PDOq->queryString);

    }

}

/*********************************

* Define connection information *

*********************************/

$config = array(

    'db' => array(

        'host'         => 'localhost',

        'username'     => 'root',

        'password'     => 'root',

        'dbname'     => 'PDOtest'

    )

);

// Initialize PDO

$db = new PDO('mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'], $config['db']['username'], $config['db']['password']);

//Set error reporting

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/******************

* Normal Queries *

******************/

h("Normal Queries");

//Perform a simple select statement

$query = $db->query("SELECT `pdotable`.`title` FROM `pdotable` LIMIT 5");

$rows = $query->fetchAll(PDO::FETCH_ASSOC); //Fetch the results of that query

p($rows, $query->queryString); //Display results of select

//Get additional data about query

m("Num rows effected by query: ".$query->rowCount()."\n\n");

/********************

* Prepared Queries *

********************/

h("Prepared Queries");

//Prepare a query

$preparedQuery = $db->prepare("SELECT title FROM pdotable WHERE title LIKE :search");

$search = (isset($_GET['search'])) ? $_GET['search'] : ''; //Get input from user

//$preparedQuery->bindValue(':varName', "%$search%", PDO::PARAM_STR);

//Use error reporting to create fallbacks

tryEx($preparedQuery, array(

    ':search' => "%$search%"

));

//Get latest ID from database

$lastId = $db->query("SELECT `pdotable`.`id` FROM `pdotable` ORDER BY `id` desc LIMIT 1")->fetchAll(PDO::FETCH_NUM)[0][0];

//Insert a record title as 'test' + its id or user string

$title = (isset($_GET['title']) && trim($_GET['title']) !== "") ? $_GET['title'] : 'title'.($lastId + 1); //Get input from user

//Create insert statement

$preparedInsert = $db->prepare("INSERT INTO  `PDOtest`.`pdotable` (`title`) VALUES (:title)");

//Use error reporting to create fallbacks

tryEx($preparedInsert, array(

    'title' => $title

));


