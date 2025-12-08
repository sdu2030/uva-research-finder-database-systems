<?php

function createPerson($UID, $pass, $description, $name)
{
    global $db;
    $query = "INSERT INTO Person (UID, pass, description, name) VALUES (:UID, :pass, :description, :name)";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':UID', $UID);
        $statement->bindValue(':pass', $pass);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':name', $name);
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

function addStudentQual($UID, $qualification)
{
    global $db;
    $query = "INSERT INTO Student_qualifications (UID, qualification) VALUES (:UID, :qualification)";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':UID', $UID);
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

function addResearchAreas($UID, $researchAreas)
{
    $researchAreas = explode(",", $researchAreas);
    foreach ($researchAreas as $researchArea) {
        global $db;
        $query = "INSERT INTO Researcher_researchAreas (UID, researchAreas) VALUES (:UID, :researchAreas)";
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':UID', $UID);
            $statement->bindValue(':researchAreas', $researchArea);
            $statement->execute();
            $statement->closeCursor();
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

function addPublications($UID, $publications)
{
    $publications = explode(",", $publications);
    foreach ($publications as $publication) {
        global $db;
        $query = "INSERT INTO Researcher_researchAreas (UID, publications) VALUES (:UID, :publication)";
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':UID', $UID);
            $statement->bindValue(':publication', $publication);
            $statement->execute();
            $statement->closeCursor();
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}


function addCoursesTaught($UID, $coursesTaught)
{
    $coursesTaught = explode(",", $coursesTaught);
    foreach ($coursesTaught as $course_taught) {
        global $db;
        $query = "INSERT INTO Researcher_coursesTaught (UID, course_taught) VALUES (:UID, :course_taught)";
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':UID', $UID);
            $statement->bindValue(':course_taught', $course_taught);
            $statement->execute();
            $statement->closeCursor();
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
?>