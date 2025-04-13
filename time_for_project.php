<?php
include("connect.php");

$nameProject = $_GET["nameProject"];
$format = isset($_GET["format"]) ? $_GET["format"] : "html";

try {
    $sqlSelect = "SELECT p.name_project, SUM(w.time_end - w.time_start + 1) AS total_days, p.manager
FROM work_table w
JOIN project p ON w.FID_Projects = p.ID_Projects
WHERE p.name_project = :nameProject;";

    $sth = $dbh->prepare($sqlSelect);
    $sth->bindValue(":nameProject", $nameProject);
    $sth->execute();
    $res = $sth->fetchAll();

    if ($format === "xml") {
        header('Content-Type: application/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<projects>';
        
        foreach($res as $row) {
            echo '<project>';
            echo '<name>' . htmlspecialchars($row[0]) . '</name>';
            echo '<days>' . htmlspecialchars($row[1]) . '</days>';
            echo '<manager>' . htmlspecialchars($row[2]) . '</manager>';
            echo '</project>';
        }
        
        echo '</projects>';
    } else {
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
}
catch (PDOException $ex) {
    if ($format === "xml") {
        header('Content-Type: application/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<error>' . htmlspecialchars($ex->GetMessage()) . '</error>';
    } else {
        echo $ex->GetMessage();
    }
}

$dbh = null;