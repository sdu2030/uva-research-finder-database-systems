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
    return $projs;

}

function getInterestedValue($uid, $pid)
{
    global $db;
    $query = "SELECT * FROM Marks_interest WHERE UID=:uid AND PID=:pid";
    $statement = $db->prepare($query);
    $statement->bindValue(':uid', $uid);
    $statement->bindValue(':pid', $pid);
    $statement->execute();
    $found = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    if (empty($found)){
        return false;
    }

    return true;
    

}

function getKeywords($pid)
{
    global $db;

    $query = "SELECT keyword FROM Project_keywords WHERE PID=:pid";

    $statement = $db->prepare($query);

    $statement->execute([':pid' => $pid]);

    $keywords = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    $statement->closeCursor();

    
    $string = implode(", ",$keywords);

    return $string;

}

function getQualifications($pid)
{
    global $db;

    $query = "SELECT qualification FROM Project_qualifications WHERE PID=:pid";

    $statement = $db->prepare($query);

    $statement->execute([':pid' => $pid]);

    $qualifications = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    $statement->closeCursor();

    $string = implode(", ",$qualifications);

    return $string;
    
}

function applyFilters($keyword, $researcher, $pcr)
{
    global $db;
    echo $keyword;
    echo "\n";

    if ($keyword == "" && $researcher == "" && $pcr == "none"){
        return getAllProjects();
    }
    //if ($keyword == "-none-" && $researcher == "" &&$pcr!= "none"){
    if ($keyword == "none" && $researcher == "" && $pcr!= "none"){
        
        $statement = $db->prepare("SELECT * FROM Project WHERE paid_credit=:pcr ORDER BY title DESC");
        $statement->bindValue(':pcr', $pcr);
        $statement->execute();
        $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $projs;

    }
    if ($keyword =="none" && $researcher != "" && $pcr!="none"){
        $uids = queryResearcherUID($researcher);
        $names = implode(", ",$uids);
        if (empty($uids)) {
            return [];  // No matches, return empty result
        }

    // Build placeholder list: (:uid0, :uid1, :uid2 ...)
        $placeholders = [];
        foreach ($uids as $i => $uid) {
            $placeholders[] = ":uid$i";
        }
        $inClause = implode(",", $placeholders);
        echo "researcher + pcr args \n\n";
        echo $names;
        $statement = $db->prepare("SELECT * FROM Project WHERE paid_credit=:pcr AND UID IN ($inClause) ORDER BY title DESC;");
        $statement->bindValue(':pcr', $pcr);
        foreach ($uids as $i => $uid) {
            $statement->bindValue(":uid$i", $uid);
        }
        $statement->execute();
        $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $projs;

    }
    echo "unsuccessful\n";
    return getAllProjects();
}

function queryResearcherUID($nameQ){
    global $db;

    $statement = $db->prepare("SELECT UID FROM Person WHERE name LIKE :nameQ;");
    $statement->bindValue(':nameQ', '%'.$nameQ.'%');
    $statement->execute();
    $uids = $statement->fetchAll(PDO::FETCH_COLUMN,0);
    $statement->closeCursor();
    

    return $uids;
}

function getDescription($pid)
{
    global $db;

    $statement = $db->prepare("SELECT Description FROM Project WHERE PID=:pid;");
    $statement->bindValue(':pid',$pid);
    $statement->execute();
    $desc = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    return $desc;
}

function getTitle($pid)
{
    global $db;

    $statement = $db->prepare("SELECT Title FROM Project WHERE PID=:pid LIMIT 1;");
    $statement->bindValue(':pid',$pid);
    $statement->execute();
    $title = $statement->fetchAll(PDO::FETCH_COLUMN,0);
    $statement->closeCursor();

    $string = implode("",$title);
    return $string;
}
?>