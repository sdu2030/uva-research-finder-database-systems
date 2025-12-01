<?php
require_once("connect-db.php");

function getAllProjects()
{
    global $db;

    $query = "SELECT * FROM Project ORDER BY title DESC";


    $statement = $db->prepare($query);

    $statement->execute();

    $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $orjs;

}

?>