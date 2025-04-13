<?php
include("connect.php");

$chiefName = $_GET["chief"];

try {
    $sqlSelect = "SELECT d.chief, COUNT(w.ID_WORKER) FROM department d
LEFT JOIN worker w ON d.ID_DEPARTMENT = w.FID_DEPARTMENT WHERE d.chief = :chiefName";

    $sth = $dbh->prepare($sqlSelect);
    $sth->bindValue(":chiefName", $chiefName);
    $sth->execute();
    $res = $sth->fetchAll();

    echo "<table border='1'>";
    echo "<thead>";
    echo "<tr><th>Chief</th><th>Employee count</th></tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach($res as $row) {
        echo "<tr><td>$row[0]</td><td>$row[1]</td></tr>";
    }

    echo "</tbody>";
    echo "</table>";
}
catch (PDOException $ex) {
    echo $ex->GetMessage();
}

$dbh = null;