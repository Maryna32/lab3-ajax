<?php
include("connect.php");

$date_project = $_GET["date_project"];
$nameProject = $_GET["nameProject"];

try {
    $sqlSelect = "SELECT w.FID_Worker, w.date_project, w.time_start, w.time_end, w.description
FROM work_table w
JOIN project p ON w.FID_Projects = p.ID_Projects
WHERE p.name_project = :nameProject AND w.date_project = :date_project;";

    $sth = $dbh->prepare($sqlSelect); //підготовлений запит
    $sth->execute([":nameProject" => $nameProject, ":date_project" => $date_project]);
    $res = $sth -> fetchAll();

    if (count($res) > 0) {
        echo "<table border='1'>";
        echo "<thead>";
        echo "<tr>
            <th>ID worker</th>
            <th>Date project</th>
            <th>Date start</th>
            <th>Date end</th>
            <th>Description</th>
          </tr>";
        echo "</thead>";
        echo "<tbody>";

    foreach($res as $row) {
        echo "<tr>
                <td>$row[0]</td>
                <td>$row[1]</td>
                <td>$row[2]</td>
                <td>$row[3]</td>
                <td>$row[4]</td>
              </tr>";
    }

    echo "</tbody>";
    echo "</table>";
    
    } 
    else {
        echo "<h2>Немає даних за обраними параметрами.</h2>";
    }

}
catch (PDOException $ex) {
    echo $ex->GetMessage();
}

$dbh = null;