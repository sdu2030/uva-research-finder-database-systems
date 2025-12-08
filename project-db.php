<?php



require_once("connect-db.php");

function getAllProjects()
{
    global $db;
    $query = "SELECT * FROM Project WHERE filled = 0 ORDER BY title DESC";
    $statement = $db->prepare($query);
    $statement->execute();
    $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $projs;
}


function searchProjects($search)

{

    global $db;
    $query = "SELECT * FROM Project WHERE !filled AND title LIKE :search ORDER BY title DESC;";
    $statement = $db->prepare($query);
    $statement->bindValue(':search', '%'.$search.'%');
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
    if ($keyword == "" && $researcher == "" && $pcr == "none"){
        return getAllProjects();
    }

    //just paid or credit filter
    if ($keyword == "none" && $researcher == "" && $pcr!= "none"){
        $statement = $db->prepare("SELECT * FROM Project WHERE paid_credit=:pcr ORDER BY title DESC");
        $statement->bindValue(':pcr', $pcr);
        $statement->execute();
        $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $projs;
    }

    //just researcher filter
    if ($keyword =="none" && $researcher != "" && $pcr == "none"){
        $uids = queryResearcherUID($researcher);
        $names = implode(", ",$uids);
        if (empty($uids)) {
            return [];  // No matches, return empty result
        }

        $placeholders = [];
        foreach ($uids as $i => $uid) {
            $placeholders[] = ":uid$i";
        }
        $inClause = implode(",", $placeholders);
        $statement = $db->prepare("SELECT * FROM Project WHERE UID IN ($inClause) ORDER BY title DESC;");
        foreach ($uids as $i => $uid) {
            $statement->bindValue(":uid$i", $uid);
        }
        $statement->execute();
        $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $projs;
    }

    //just keyword filter
    if ($keyword != "none" && $researcher =="" && $pcr =="none"){
        $starter = $db->prepare("SELECT PID FROM Project_keywords WHERE keyword=:keyword");
        $starter->bindValue(':keyword', $keyword);
        $starter->execute();
        $pids = $starter-> fetchAll(PDO::FETCH_COLUMN,0);
        $starter->closeCursor();
        if (empty($pids)){
            return [];
        }
        $subset = [];
        foreach ($pids as $i => $pid) {
            $subset[] = ":pid$i";
        }
        $inClause = implode(",", $subset);
       //echo $inClause;
        $statement = $db->prepare("SELECT * FROM Project WHERE PID IN ($inClause) ORDER BY title DESC;");
        foreach ($pids as $i => $pid) {
            $statement->bindValue(":pid$i", $pid);
        }
        $statement->execute();
        $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $projs;
    }


    //researcher and paid/credit filter
    if ($keyword =="none" && $researcher != "" && $pcr!="none"){
        $uids = queryResearcherUID($researcher);
        $names = implode(", ",$uids);
        if (empty($uids)) {
            return [];  // no matches
        }

        $placeholders = [];
        foreach ($uids as $i => $uid) {
            $placeholders[] = ":uid$i";
        }
        $inClause = implode(",", $placeholders);
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


    //keyword and paid/credit filter
    if ($keyword != "none" && $researcher == "" && $pcr!= "none"){
        $starter = $db->prepare("SELECT PID FROM Project_keywords WHERE keyword=:keyword");
        $starter->bindValue(':keyword', $keyword);
        $starter->execute();
        $pids = $starter-> fetchAll(PDO::FETCH_COLUMN,0);
        $starter->closeCursor();
        if (empty($pids)){
            return [];
        }
        $subset = [];
        foreach ($pids as $i => $pid) {
            $subset[] = ":pid$i";
        }
        $inClause = implode(",", $subset);
        // $inClause;
        $statement = $db->prepare("SELECT * FROM Project WHERE paid_credit=:pcr AND PID IN ($inClause) ORDER BY title DESC;");
        $statement->bindValue(':pcr', $pcr);
        foreach ($pids as $i => $pid) {
            $statement->bindValue(":pid$i", $pid);
        }
        $statement->execute();
        $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $projs;



    }



    //researcher and keyword

    if ($keyword != "none" && $researcher != "" && $pcr == "none") {
        $starter = $db->prepare("SELECT PID FROM Project_keywords WHERE keyword = :keyword");
        $starter->bindValue(':keyword', $keyword);
        $starter->execute();
        $pids = $starter->fetchAll(PDO::FETCH_COLUMN, 0);
        $starter->closeCursor();

        if (empty($pids)) return [];
        $subset = [];
        foreach ($pids as $i => $pid) {
            $subset[] = ":pid$i";
        }
        $inClause1 = implode(",", $subset);

        $uids = queryResearcherUID($researcher);
        if (empty($uids)) return [];
        $placeholder = [];
        foreach ($uids as $i => $uid) {
            $placeholder[] = ":uid$i";
        }
        $inClause2 = implode(",", $placeholder);
        $statement = $db->prepare("SELECT * FROM Project WHERE PID IN ($inClause1) AND UID IN ($inClause2) ORDER BY title DESC");



        //bind pids
        foreach ($pids as $i => $pid) {
            $statement->bindValue(":pid$i", $pid, PDO::PARAM_INT);
        }

        //bind uids
        foreach ($uids as $i => $uid) {
            $statement->bindValue(":uid$i", $uid, PDO::PARAM_STR); 
        }



        $statement->execute();
        $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();



        return $projs;

    }



    //all three

    if ($keyword != "none" && $researcher != "" && $pcr != "none") {
        $starter = $db->prepare("SELECT PID FROM Project_keywords WHERE keyword = :keyword");
        $starter->bindValue(':keyword', $keyword);
        $starter->execute();
        $pids = $starter->fetchAll(PDO::FETCH_COLUMN, 0);
        $starter->closeCursor();

        if (empty($pids)) return [];
        $subset = [];
        foreach ($pids as $i => $pid) {
            $subset[] = ":pid$i";
        }
        $inClause1 = implode(",", $subset);

        $uids = queryResearcherUID($researcher);
        if (empty($uids)) return [];
        $placeholder = [];
        foreach ($uids as $i => $uid) {
            $placeholder[] = ":uid$i";
        }

        $inClause2 = implode(",", $placeholder);
        $statement = $db->prepare("SELECT * FROM Project WHERE paid_credit=:pcr AND PID IN ($inClause1) AND UID IN ($inClause2) ORDER BY title DESC");

        //bind pids
        foreach ($pids as $i => $pid) {
            $statement->bindValue(":pid$i", $pid, PDO::PARAM_INT);
        }

        //bind uids
        foreach ($uids as $i => $uid) {
            $statement->bindValue(":uid$i", $uid, PDO::PARAM_STR); 
        }
        $statement->bindValue(":pcr", $pcr);
        $statement->execute();
        $projs = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return $projs;

    }
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

    $statement = $db->prepare("SELECT description FROM Project WHERE PID=:pid;");
    $statement->bindValue(':pid',$pid);
    $statement->execute();
    $desc = $statement->fetchAll(PDO::FETCH_COLUMN,0);
    $statement->closeCursor();

    $string = implode("",$desc);
    return $string;
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

function getPaid_Credit($pid)
{
    global $db;

    $statement = $db->prepare("SELECT paid_credit FROM Project WHERE PID=:pid;");
    $statement->bindValue(':pid',$pid);
    $statement->execute();
    $pcr = $statement->fetchAll(PDO::FETCH_COLUMN,0);
    $statement->closeCursor();

    $string = implode("",$pcr);
    return $string;
}

function getResearcher($pid)
{
    global $db;

    $statement = $db->prepare("SELECT UID FROM Project WHERE PID=:pid;");
    $statement->bindValue(':pid',$pid);
    $statement->execute();
    $uid = $statement->fetchAll(PDO::FETCH_COLUMN,0);
    $statement->closeCursor();

    $input = implode("",$uid);

    $statement = $db->prepare("SELECT name FROM Person WHERE UID=:input;");
    $statement->bindValue(':input',$input);
    $statement->execute();
    $researcher = $statement->fetchAll(PDO::FETCH_COLUMN,0);
    $statement->closeCursor();

    $result = implode("",$researcher);

    return $result;
}

function getNumStudents($pid)
{
    global $db;

    $statement = $db->prepare("SELECT num_students FROM Project WHERE PID=:pid;");
    $statement->bindValue(':pid',$pid);
    $statement->execute();
    $numstuds = $statement->fetchAll(PDO::FETCH_COLUMN,0);
    $statement->closeCursor();

    $string = implode("",$numstuds);
    return $string;
}

function getProjectById($PID)
{
    global $db;
    $query = "SELECT * FROM Project WHERE PID = :PID";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':PID', $PID);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $result[0];
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
}

function createProject($title, $paid_credit, $num_students, $description, $UID)
{
    global $db;
    $query = "INSERT INTO Project (title, paid_credit, num_students, description, UID) VALUES (:title, :paid_credit, :num_students, :description, :UID)";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':paid_credit', $paid_credit);
        $statement->bindValue(':num_students', $num_students);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':UID', $UID);
        $statement->execute();
        $statement->closeCursor();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
}

// Doesn't take in UID, because there's no reason to update the researcher associated
function updateProject($PID, $title, $paid_credit, $num_students, $description)
{
    global $db;
    $query = "UPDATE Project SET title = :title, paid_credit = :paid_credit, num_students = :num_students, description=:description WHERE PID=:PID";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':paid_credit', $paid_credit);
        $statement->bindValue(':num_students', $num_students);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':PID', $PID);
        $statement->execute();
        $statement->closeCursor();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
}

function deleteProject($PID)
{
    global $db;
    $query = "DELETE FROM Project WHERE PID=:PID";
    $statement = $db->prepare($query);
    $statement->bindValue(':PID', $PID);
    $statement->execute();
    $statement->closeCursor();
}

function addProjectKeyword($PID, $keyword)
{
    global $db;
    $query = "INSERT INTO Project_keywords (keyword) VALUES (:keyword) WHERE PID=:PID";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':keyword', $keyword);
        $statement->bindValue(':PID', $PID);
        $statement->execute();
        $statement->closeCursor();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
}

function addProjectQual($PID, $qualification)
{
    global $db;
    $query = "INSERT INTO Project_qualifications (PID, qualification) VALUES (:PID, :qualification)";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':PID', $PID);
        $statement->bindValue(':qualification', $qualification);
        $statement->execute();
        $statement->closeCursor();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
}

function markInterested($uid, $pid)
{
   global $db;
   $query = "INSERT INTO Marks_interest (UID, PID) VALUES (:uid, :pid)";
   try {
        $statement = $db->prepare($query);
        $statement->bindValue(':uid', $uid);
        $statement->bindValue(':pid', $pid);
        $statement->execute();
        $statement->closeCursor();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }

}

function unmarkInterested($uid, $pid)
{
    global $db;
    $query = "DELETE FROM Marks_interest WHERE PID=:uid AND UID=:uid";
    $statement = $db->prepare($query);
    $statement->bindValue(':uid', $uid);
    $statement->bindValue(':pid', $pid);
    $statement->execute();
    $statement->closeCursor();
}
?>