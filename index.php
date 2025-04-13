<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lab3</title>
    <style>
      .container {
         display: flex;
         gap: 30px;
      }
      section {
        border: 1px solid black;
        padding-left: 15px;
        width: 30%;
        height: 500px;
        box-shadow: 1px 1px 1px 1px grey;
        padding-bottom: 20px;
      }

      .result-container {
        margin-top: 15px;
        min-height: 50px;
      }
    </style>
  </head>
  <body>
    <h2>Варіант 2. БД для зберігання інформації про процес роботи співробітників над проектами</h2>
  
    <div class="container">
        <section>
          <h3>Інформація про виконані завдання за обраним проєктом на зазначену дату</h3>
            <label for="nameProject">Введіть назву проекта</label>
            <select name="nameProject" id="nameProject">
              <?php
              include("connect.php");
              $sqlSelect = "SELECT DISTINCT name_project FROM project";
              foreach ($dbh->query($sqlSelect) as $row) {
                echo "<option>$row[0]</option>";
              }
              ?>
            </select>
            <label for="date_project">Оберіть дату</label>
            <select name="date_project" id="date_project">
              <?php
              include("connect.php");
              $sqlSelect = "SELECT DISTINCT date_project FROM work_table";
              foreach ($dbh->query($sqlSelect) as $row) {
                echo "<option>$row[0]</option>";
              }
              ?>
            </select>
           <input type="button" id="infoSubmit" value="Отримати результат">
        <div id="infoResult" class="result-container"></div>
      </section>
    
      <section>
        <h3>Загальний час роботи над обраним проєктом</h3>
          <label for="projectName">Введіть назву проекта</label>
          <select name="nameProject" id="projectName">
            <?php
            include("connect.php");
            $sqlSelect = "SELECT DISTINCT name_project FROM project";
            foreach ($dbh->query($sqlSelect) as $row) {
              echo "<option>$row[0]</option>";
            }
            ?>
          </select>
           <input type="button" id="timeSubmit" value="Отримати результат">
        <div id="timeResult" class="result-container"></div>
      </section>
    
      <section>
      <h3>Кількість співробітників відділу обраного керівника</h3>
          <label for="chief">Введіть ім'я керівника</label>
          <select name="chief" id="chief">
            <?php
            include("connect.php");
            $sqlSelect = "SELECT DISTINCT chief FROM department";
            foreach ($dbh->query($sqlSelect) as $row) {
              echo "<option>$row[0]</option>";
            }
            ?>
          </select>
           <input type="button" id="countSubmit" value="Отримати результат">
      <div id="countResult" class="result-container"></div>
      </section>
    </div>

    <script>
      // у форматі простого тексту
      document.getElementById('countSubmit').addEventListener('click', function() {
        const chief = document.getElementById('chief').value;
        const xhr = new XMLHttpRequest();
        
        xhr.onreadystatechange = function() {
          if (this.readyState === 4 && this.status === 200) {
            document.getElementById('countResult').innerHTML = this.responseText;
          }
        };
        
        xhr.open('GET', `count_workers.php?chief=${encodeURIComponent(chief)}`, true);
        xhr.send();
      });
      
      // у форматі XML
      document.getElementById('timeSubmit').addEventListener('click', function() {
        const nameProject = document.getElementById('projectName').value;
        const xhr = new XMLHttpRequest();
        
        xhr.onreadystatechange = function() {
          if (this.readyState === 4 && this.status === 200) {
            const xmlData = this.responseXML;
            const projects = xmlData.getElementsByTagName('project');
            
            let output = "<table border='1'>";
            output += "<thead><tr><th>Name project</th><th>Total count days</th><th>Manager</th></tr></thead>";
            output += "<tbody>";
            
            for (let i = 0; i < projects.length; i++) {
              const name = projects[i].getElementsByTagName('name')[0].textContent;
              const days = projects[i].getElementsByTagName('days')[0].textContent;
              const manager = projects[i].getElementsByTagName('manager')[0].textContent;
              
              output += `<tr><td>${name}</td><td>${days}</td><td>${manager}</td></tr>`;
            }
            
            output += "</tbody></table>";
            document.getElementById('timeResult').innerHTML = output;
          }
        };
        
        xhr.open('GET', `time_for_project.php?nameProject=${encodeURIComponent(nameProject)}&format=xml`, true);
        xhr.send();
      });
      
      // у форматі  JSON
      document.getElementById('infoSubmit').addEventListener('click', function() {
        const nameProject = document.getElementById('nameProject').value;
        const dateProject = document.getElementById('date_project').value;
        
        fetch(`information_for_date.php?nameProject=${encodeURIComponent(nameProject)}&date_project=${encodeURIComponent(dateProject)}&format=json`)
          .then(response => response.json())
          .then(data => {
            if (data.length > 0) {
              let output = "<table border='1'>";
              output += "<thead><tr><th>ID worker</th><th>Date project</th><th>Date start</th><th>Date end</th><th>Description</th></tr></thead>";
              output += "<tbody>";
              
              data.forEach(row => {
                output += `<tr>
                  <td>${row.worker_id}</td>
                  <td>${row.date_project}</td>
                  <td>${row.time_start}</td>
                  <td>${row.time_end}</td>
                  <td>${row.description}</td>
                </tr>`;
              });
              
              output += "</tbody></table>";
              document.getElementById('infoResult').innerHTML = output;
            } else {
              document.getElementById('infoResult').innerHTML = "<h2>Немає даних за обраними параметрами.</h2>";
            }
          })
          .catch(error => {
            console.error('Error:', error);
            document.getElementById('infoResult').innerHTML = "Сталася помилка при отриманні даних.";
          });
      });
    </script>
  </body>
  </body>
</html>
