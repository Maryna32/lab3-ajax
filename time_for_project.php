<?php
include("connect.php");

$nameProject = $_GET["nameProject"];

try {
    $sqlSelect = "SELECT p.name_project, SUM(w.time_end - w.time_start + 1) AS total_days, p.manager
FROM work_table w
JOIN project p ON w.FID_Projects = p.ID_Projects
WHERE p.name_project = :nameProject;";

$sth = $dbh->prepare($sqlSelect); //підготовлений запит
$sth -> bindValue(":nameProject", $nameProject); //прив'язка
$sth -> execute(); //виконання
$res = $sth -> fetchAll();

echo "<table border='1'>";
echo "<thead>";
echo "<tr><th>Name project</th><th>Total count days</th><th>Manager</th></tr>";
echo "</thead>";
echo "<tbody>";

foreach($res as $row) {
    echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td></tr>";
}

echo "</tbody>";
echo "</table>";
}

catch (PDOException $ex) {
    echo $ex->GetMessage();
}

$dbh = null;
