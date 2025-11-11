<?php

require_once("connect-db.php");


function addRequests($reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
{
    global $db;

    $query = "INSERT INTO requests (reqDate, roomNumber, reqBy, repairDesc, reqPriority) VALUES (:reqDate, :roomNumber, :reqBy, :repairDesc, :reqPriority)";


    $statement = $db->prepare($query);
    $statement->bindValue(':reqDate', $reqDate);
    $statement->bindValue(':roomNumber', $roomNumber);
    $statement->bindValue(':reqBy', $reqBy);
    $statement->bindValue(':repairDesc', $repairDesc);
    $statement->bindValue(':reqPriority', $reqPriority);
    $statement->execute();
    $statement->closeCursor();
}

function getAllRequests()
{
    global $db;

    $query = "SELECT * FROM requests ORDER BY reqId DESC";


    $statement = $db->prepare($query);

    $statement->execute();

    $reqs = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $reqs;

}

function getRequestById($id)  
{
    global $db;

    $query = "SELECT * FROM requests WHERE reqId = :id";


    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);

    $statement->execute();
    $reqs = $statement->fetch();

    $statement->closeCursor();
    return $reqs;

}

function updateRequest($reqId, $reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
{
    global $db;

    $query = "UPDATE requests SET reqDate = :reqDate, roomNumber = :roomNumber, reqBy = :reqBy, repairDesc = :repairDesc, reqPriority = :reqPriority WHERE reqId = :reqId";


    $statement = $db->prepare($query);
    $statement->bindValue(':reqId', $reqId);
    $statement->bindValue(':reqDate', $reqDate);
    $statement->bindValue(':roomNumber', $roomNumber);
    $statement->bindValue(':reqBy', $reqBy);
    $statement->bindValue(':repairDesc', $repairDesc);
    $statement->bindValue(':reqPriority', $reqPriority);
    $statement->execute();
    $statement->closeCursor();

}

function deleteRequest($reqId)
{
    global $db;

    $query = "DELETE FROM requests WHERE reqId = :reqId";


    $statement = $db->prepare($query);
    $statement->bindValue(':reqId', $reqId);
    $statement->execute();
    $statement->closeCursor();
    
}

?>
