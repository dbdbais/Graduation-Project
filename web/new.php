<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="load.css">
<link rel="stylesheet" href="skeleton.css">

<?php
  $database = new mysqli("127.0.0.1", "root", "dlrkddn1@","graduation_project");

  $data_arr = array();
  $result = mysqli_query($database, "SELECT * FROM mail");

  while($row = mysqli_fetch_assoc($result)){
    $data = array("idx"=> $row['idx'], "collect"=> $row['collect'], "generate"=> $row['generate'], "img"=> $row['img'],
    "predict" => $row['predict'], "feature"=>$row['feature'], "label"=> $row['label']);
    $data_arr[] = $data;
  }
mysqli_close($database);

?>
<style>
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
.fade-in {
  animation: fadeIn 0.5s ease-in;
}
#dbTable {
    max-width: 90vw; /* Limit the maximum width of the table to 80% of the viewport */
    max-height: 70vh; 
    overflow: auto; /* Add scrollbars when the content overflows the table */
    margin: 0 auto; /* Center the table horizontally */
    border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); /* Add a subtle shadow */
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0)); /* Add a gradient background */
  }
#dbTable table {
  width:95%;
  margin: 0 auto; /* Center the table horizontally within dbTable */
  border-collapse: collapse;
  margin-top: 20px;
    }
th, td {
      font-size : 20px;
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
      border-right: 1px solid #ddd; 
    }
    th:last-child,
    td:last-child {
      border-right: none; /* Remove right border for the last column */
    }
    th {
      text-align: center;
      font-weight: bold;
      background-color: #f2f2f2;
    }
    .center-align {
    text-align: center;
  }
  tr{
  transition: transform 0.3s, filter 0.3s, border-width 0.3s ease, box-shadow 0.3s ease;
}

.red:hover {
  transform: scale(1.02); /* 살짝 확대 효과 */
  filter: brightness(1.2); /* Brightness increase effect */
  box-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000, 0 0 30px #ff0000;
  transition: background-color 0.5s ease; /* Add a transition effect */
  cursor: pointer; /* Add a pointer cursor to indicate interactivity */
}

.blue:hover {
  transform: scale(1.02); /* 살짝 확대 효과 */
  filter: brightness(1.2); /* Brightness increase effect */
  box-shadow: 0 0 10px #00ff00, 0 0 20px #00ff00, 0 0 30px #00ff00;
  transition: background-color 0.5s ease; /* Add a transition effect */
  cursor: pointer; /* Add a pointer cursor to indicate interactivity */
}

    
  #sMail{
    width: 30vw;
    height : 60vh;
    background: linear-gradient(135deg, #f0f0f0, #d8d8d8);
  border: 1px solid #999;
  box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
    display: none;
    flex-direction: column;
    justify-content: center; /* Center the child elements vertically */
    padding: 50px; /* Add some padding for better appearance */
    position: relative;
  }

  #sMail p {
    margin: 10px; /* Add margin for spacing between paragraphs */
    font-size: 30px; /* Adjust font size for the mail content */
    font-family: Arial, sans-serif; /* Adjust the font family */
    font-weight: bold;
  }

  #sMail div {
    background: linear-gradient(180deg, #f8f8f8, #e0e0e0);
    padding: 10px;
    width: 30vw;
    height : 60vh;
    margin: 10px; /* Add margin for spacing between the mail text and paragraphs */
    font-size: 30px; /* Adjust font size for the mail text */
    font-family: Arial, sans-serif; /* Adjust the font family */
    line-height: 1.5; /* Set line-height for better readability */
    overflow: auto;
  }
  .overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.spam-legend {
  display: inline-block;
  margin-left: 10px;
  padding: 2px 5px;
  background-color: rgba(255, 150, 150, 0.5);
  font-weight: bold;
  font-size: 30px;
  border-radius: 3px;
  float: right; /* Add this line to align the legend to the right */
}
    </style>
    <script>
       var database = <?php echo json_encode($data_arr);?>; 
    
       const filePath = "spamKeyword.txt";
       const spam = [];
       function readTextFileToArray(filePath) {
    fetch(filePath)
        .then(response => response.text())
        .then(data => {
            const lines = data.split('\n');
            const filteredLines = lines.filter(line => line.trim() !== '');
            // 소문자로 변환후 spam배열에 push
            spam.push(...filteredLines.map(line => line.trim().toLowerCase()));

            //window.alert(spam);
        })
        .catch(error => {
  
        });
}

// Call the function to read the text file and populate the spam array
readTextFileToArray(filePath);

       function printWebTable(){
          event.preventDefault();
          var st = "<table><thead><tr><th>idx</th><th>collect</th><th>image</th><th>spam</th></tr></thead><tbody>";
            //add database element
            var cnt = 1;
            for (var i = 0; i < database.length; i++) {
              if(database[i].collect){
              st += '<tr id="row-' + database[i].idx;
              if(database[i].predict==1){
                st+= '"class = "red"';
              }
              else{
                st+= '"class = "blue"';
              } 
              st+= '"  onclick="handleRowClick(this)">';
              st += '<td class="center-align">' + cnt + '</td>';
              st += '<td>' + database[i].collect + '</td>';
              st += '<td class="center-align">' + database[i].img + '</td>';
              st += '<td class="center-align">' + database[i].predict + '</td>';
              st += '</tr>';
              cnt++;
              }
            }
            st+= "</tbody></table>";
            document.getElementById("dbTable").innerHTML = st;
        }
        function printGenTable(){
          event.preventDefault();
          var st = "<table><thead><tr><th>idx</th><th>generate</th><th>image</th><th>spam</th></tr></thead><tbody>";
            //add database element
            var cnt = 1;
            for (var i = 0; i < database.length; i++) {
              if(database[i].generate && (database[i].label == 1 || database[i].label == 4)){
              st += '<tr id="row-' + database[i].idx;
              if(database[i].predict==1){
                st+= '"class = "red"';
              }
              else{
                st+= '"class = "blue"';
              } 
              
              st+= '"  onclick="handleRowClick(this)">';
              st += '<td class="center-align">' + cnt + '</td>';
              st += '<td>' + database[i].generate + '</td>';
              st += '<td class="center-align">' + database[i].img + '</td>';
              st += '<td class="center-align">' + database[i].predict + '</td>';
              st += '</tr>';
              cnt++;
              }
            }
            st+= "</tbody></table>";
            document.getElementById("dbTable").innerHTML = st;
        }
        function handleRowClick(row){
          var rowId = row.getAttribute("id");
          var idx = rowId.substring(4);
          var rowData = database.find(item => item.idx == idx);
          openDBMail(rowData);
        }
        function escapeRegExp(string) {
          return string.replace(/[.*+\-?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
        }
        function openDBMail(data){
          document.getElementById("dbTable").style.display = "none";
          var sMail = document.getElementById("sMail");
          sMail.classList.add("fade-in"); 
          sMail.style.display = "flex";
          sMail.innerHTML = "";
          sMail.innerHTML += '<p><span class="spam-legend">SPAM KEYWORD</span></p>';
          var textWithHighlights;
          if(data.collect){
          textWithHighlights = "유저: "+data.collect;
          }
          else{
            textWithHighlights = "생성: " +data.generate;
          }
          var imageHighlights="";
          if(data.img){
            imageHighlights += "\n이미지: "+data.img;
          }
          for (var i = 0; i < spam.length; i++) {
            var regex = new RegExp("\\b" + escapeRegExp(spam[i]) + "\\b", "gi"); // Create a regex pattern to match whole words
        textWithHighlights = textWithHighlights.replace(regex, "<span style='background-color: rgba(255, 150, 150, 0.5); font-weight: bold;'>$&</span>");
          }
          if(imageHighlights){
            for (var i = 0; i < spam.length; i++) {
            var regex = new RegExp("\\b" + escapeRegExp(spam[i]) + "\\b", "gi"); // Create a regex pattern to match whole words
            imageHighlights = imageHighlights.replace(regex, "<span style='background-color: rgba(255, 150, 150, 0.5); font-weight: bold;'>$&</span>");
          }
          }
          sMail.innerHTML += "<div>" + textWithHighlights + "</div>";
          if(imageHighlights){
            sMail.innerHTML += "<div>" + imageHighlights + "</div>";
          }
        }
       function openWebDB(){
            event.preventDefault();
            showOverLay(); // Show the loading overlay
             setTimeout(function () {
                hideOverlay(); // Hide the loading overlay
            }, 300); // 1-second delay
            
            var snd = document.getElementById("gen");
            var wt = document.getElementById("wt");
            document.getElementById("sMail").style.display ="none";
            gen.style.display ="none";
            wt.style.display ="none";
            var db = document.getElementById("dbTable");
            printWebTable();
            db.classList.add("fade-in"); 
            db.style.display="block";
        }
        function openGenDB(){
            event.preventDefault();
            showOverLay(); // Show the loading overlay
             setTimeout(function () {
                hideOverlay(); // Hide the loading overlay
            }, 300); // 1-second delay
            
            var snd = document.getElementById("gen");
            var wt = document.getElementById("wt");
            document.getElementById("sMail").style.display ="none";
            gen.style.display ="none";
            wt.style.display ="none";
            var db = document.getElementById("dbTable");
            printGenTable();
            db.classList.add("fade-in"); 
            db.style.display="block";
        }
        function homePage(){
          event.preventDefault();
          showOverLay(); // Show the loading overlay
             setTimeout(function () {
                hideOverlay(); // Hide the loading overlay
                location.href = 'FirstPage.php'; // Redirect to the info page
            }, 500); // 1-second delay
        }
        function showOverLay(){
        var overlay = document.createElement("div");
        overlay.classList.add("overlay");
        var loadingContainer = document.createElement("div");
        loadingContainer.classList.add("loading-container");
        var loadingDiv = document.createElement("div");
        loadingDiv.classList.add("loading");
        var loadingText = document.createElement("div");
        loadingText.id = "loading-text";
        loadingText.textContent = "loading";

        loadingContainer.appendChild(loadingDiv);
        loadingContainer.appendChild(loadingText);
        overlay.appendChild(loadingContainer);
        document.body.appendChild(overlay);
    }
      function hideOverlay() {
        var overlay = document.querySelector(".overlay");
        if (overlay) {
            document.body.removeChild(overlay);
        }
    }
    function refresh(){
      event.preventDefault();
      showOverLay(); // Show the loading overlay
             setTimeout(function () {
                hideOverlay(); // Hide the loading overlay
                location.reload(); // Redirect to the info page
            }, 200); // 1-second delay
    }
    </script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataBase</title>
</head>
<body>
  <header>
  <img src = "arrow.png" onclick="refresh()" id="arrow">
<h1 class="stylish-header">DataBase</h1>
<img src = "home.png" onclick="homePage()" id="hicon">
</header>
    <section>
    <div class="button-container">
            <button id="gen" class="action-button" onclick="openGenDB()">생성 메일 조회</button>
            <button id="wt" class="action-button" onclick="openWebDB()">작성 메일 조회</button>
        </div>
    <div id ="dbTable"></div>

    <div id ="sMail" ></div>
</section>
<footer>
<h4> Copyright © 2023 Team_49 All rights reserved.</h4>
    </footer>
</body>
</html>