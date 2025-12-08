<?php 
require_once("connect-db.php");

function getResearcherId($pid)
{
    global $db;

    $statement = $db->prepare("SELECT UID FROM Project WHERE PID=:pid;");
    $statement->bindValue(':pid',$pid);
    $statement->execute();
    $uid = $statement->fetchAll(PDO::FETCH_COLUMN,0);
    $statement->closeCursor();

    $result = implode("",$uid);


    return $result;
}

function getRole($uid)
{
    global $db;

    $statement = $db->prepare("SELECT role FROM Researcher WHERE UID=:uid;");
    $statement->bindValue(':uid',$uid);
    $statement->execute();
    $role = $statement->fetchAll(PDO::FETCH_COLUMN,0);
    $statement->closeCursor();

    $result = implode("",$role);


    return $result;
}

function getName($uid)
{
    global $db;

    $statement = $db->prepare("SELECT name FROM Person WHERE UID=:uid;");
    $statement->bindValue(':uid',$uid);
    $statement->execute();
    $name = $statement->fetchAll(PDO::FETCH_COLUMN,0);
    $statement->closeCursor();

    $result = implode("",$name);


    return $result;
}


function getResAreas($uid)
{
    global $db;

    $query = "SELECT researchAreas FROM Researcher_researchAreas WHERE UID=:uid";

    $statement = $db->prepare($query);

    $statement->execute([':uid' => $uid]);

    $areas = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    $statement->closeCursor();


    $string = implode(", ",$areas);

    return $string;

}

function getProjs($uid)
{
    global $db;
    $query = "SELECT * FROM Project WHERE UID=:uid ORDER BY title DESC";
    $statement = $db->prepare($query);
    $statement->execute([':uid' => $uid]);
    $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $projs;
}

function getPublications($uid)
{
    global $db;

    $query = "SELECT publication FROM Researcher_publications WHERE UID=:uid";

    $statement = $db->prepare($query);

    $statement->execute([':uid' => $uid]);

    $papers = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();


    return $papers;
}

function getCourses($uid)
{
    global $db;

    $query = "SELECT course_taught FROM Researcher_coursesTaught WHERE UID=:uid";

    $statement = $db->prepare($query);

    $statement->execute([':uid' => $uid]);

    $courses = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();


    return $courses;
}



?>