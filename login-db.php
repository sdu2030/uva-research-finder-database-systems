<?php
require_once("connect-db.php");

function verify_user($name, $pass) {
    global $db;
    $query = "SELECT * FROM Person WHERE name=:name AND pass=:pass";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':name', $name);
        $statement->bindValue(':pass', $pass);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();
        return $result;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
}

function is_researcher($UID) {
    global $db;
    $query = "SELECT * FROM Researcher WHERE UID=:UID";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':UID', $UID);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();
        return !empty($result);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
}

?>