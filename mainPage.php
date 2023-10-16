<html> 
    <head> 
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="load.css">
      <link rel="stylesheet" href="skeleton.css"> 

      <?php
  $database = new mysqli("127.0.0.1", "root", "dlrkddn1@","graduation_project");

  $query = "SELECT feature, predict FROM mail WHERE idx = (SELECT MAX(idx) FROM mail)";
$result = mysqli_query($database, $query);
$row = $result->fetch_assoc();
/* echo "<script>alert('" . json_encode($row) . "');</script>"; */
if ($row) {
  $feature = $row['feature'];
  $predict = $row['predict'];
}
else{
  $feature = "";
  $predict = "";
}
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
.fade-in {
  animation: fadeIn 0.5s ease-in;
}
#sMail p {
    margin: 10px; 
    font-size: 20px; 
    font-family: Arial, sans-serif; 
    font-weight: bold;
  }

  #sMail div {
    background: linear-gradient(180deg, #f8f8f8, #e0e0e0);
    padding: 10px;
    width: 30vw;
    height : 60vh;
    margin: 10px; 
    font-size: 20px; 
    font-family: Arial, sans-serif; 
    line-height: 1.5; 
    overflow: auto;
  }
  section {
    flex-direction : row;
    padding-top: 80px; 
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: calc(100vh - 150px); 
}


  #pieContainer{
 
    width:800px; 
    height:800px;
  }
  #barContainer{
    display:none;
    width:55%; 
    height:55%;
  }
  .mj-ambulnace-light {
    position: absolute;
    top:50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transform:scale(10);
    }
    
    .mj-ambulnace-light #light-lamp {
    width: 20px;
    height: 26px;
    background: red;
    border: 1px solid #fff;
    border-radius: 20px 20px 0 0;
    display: flex;
    justify-content: center;
    align-items: center;
    animation: light infinite 600ms ease-in ;
    }
    
    .mj-ambulnace-light #lamp-spinner {
    background: #fff;
    width: 12px;
    height: 18px;
    border-radius: 20px 20px 0 0;
    animation: ambulance infinite linear 600ms;
    }
    
    .mj-ambulnace-light #light-lamp-bottom {
    background: #ffffffa6;
    width: 32px;
    height: 6px;
    border-radius: 1px;
    }
    
    @keyframes ambulance {
    0% {
    transform: rotateY(0deg);
    }
    100%{
    transform: rotateY(180deg);
    }
    }
    @keyframes light {
    0% {
    box-shadow: 0 0 7px red;
    }
    50%{
    box-shadow: 0 0 0px red;
    }
    100%{
     box-shadow: 0 0 7px red;
    }
    }
    </style>
      <script>
 
       const filePath = "spamKeyword.txt";
       const spamKeywords = [];
      var predict = <?php echo $predict; ?>;
      var wordCount;
      var ky;
      var dt;
        window.onload = function(){
          if(predict){
            event.preventDefault();
            spamAnimation();
             setTimeout(function () {
                hideOverlay(); 
                window.alert("SPAM MESSAGE DETECTED!")
            }, 3000); // 3-second delay
          ;
          }
          readTextFileToArray(filePath);
        }
      
    function readTextFileToArray(filePath) {
    fetch(filePath)
        .then(response => response.text())
        .then(data => {
            const lines = data.split('\n');
            const filteredLines = lines.filter(line => line.trim() !== '');

            // Convert each line to lowercase and push to the spamKeywords array
            spamKeywords.push(...filteredLines.map(line => line.trim().toLowerCase()));
            /* window.alert(spamKeywords); */
            // Call countOccurrences after updating the spamKeywords array
            const wordCount = countOccurrences(bonmun, spamKeywords);
            var ky = Object.keys(wordCount);
            var dt = Object.values(wordCount);
            if(dt || ky){
            var pieChart = new Chart(document.getElementById("pie-chart"), {
        type: 'pie',
        data: {
          labels: ky,
          datasets: [{
            label: "spam keywords",
            backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850",'#9BD0F5','#FFB1C1','#FF6384','#C0C81A','#80C024'],
            data: dt
          }]
        },
        options: {
          responsive: true, // Disable responsiveness
          title: {
            display: true,
            text: 'Top 10 Words in Spam Mail',
            fontSize: 30
          },
          tooltips: {
      titleFontSize: 18, // Set the title font size
      bodyFontSize: 30,  // Set the body font size
    }
        }
    });
  }
  
        })
        .catch(error => {
          window.alert("error");
            // Handle error if needed
        }); 
}
        
        const mail=JSON.parse(localStorage.getItem("mail")); 
        var database = <?php echo json_encode($data_arr);?>;    // php 배열을 javascript 배열에 바로 대입하고 싶을 때는 json을 활용
        var bonmun = mail.collect + mail.generate + mail.img;
        bonmun = bonmun.replace(/[^\w\s]/g, '').split(/\s+/).map(word => word.trim().toLowerCase());
        
        function countOccurrences(bonmun, spamKeywords) {
            const res = {};
            bonmun.forEach(word => {
              if(spamKeywords.includes(word)){
                res[word] = (res[word] || 0) + 1;
              }
            });
              const resArray = Object.entries(res);
             resArray.sort((a, b) => b[1] - a[1]);
             const top10 = resArray.slice(0, 10);
             const top10Obj = Object.fromEntries(top10);
             return top10Obj; 
        }
        function homePage(){
          event.preventDefault();
          showOverLay();
             setTimeout(function () {
                hideOverlay();
                location.href = 'FirstPage.php';
            }, 500);
          
        }
        
        function spamAnimation(){
        var overlay = document.createElement("div");
        overlay.classList.add("overlay");
        var container = document.createElement("div");
        container.classList.add ("mj-ambulnace-light");
        var lightLamp = document.createElement("div");
        lightLamp.setAttribute("id", "light-lamp");
        var lampSpinner = document.createElement("div");
        lampSpinner.setAttribute("id", "lamp-spinner");
        var lightLampBottom = document.createElement("div");
        lightLampBottom.setAttribute("id", "light-lamp-bottom");
        lightLamp.appendChild(lampSpinner);
        container.appendChild(lightLamp);
        container.appendChild(lightLampBottom);
        overlay.appendChild(container);
        document.body.appendChild(overlay);
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
    function openGraph(){
      event.preventDefault();
      var pie = document.getElementById("pieContainer");
      var bar = document.getElementById("barContainer");
      bar.style.display = "none";
      bar.classList.remove("fade-in");
      pie.classList.add("fade-in");
      pie.style.display = "block";
      readTextFileToArray(filePath);
    }
    var featureArray = JSON.parse(<?php echo json_encode($feature); ?>);
      function openBar(){
      event.preventDefault();
      var pie = document.getElementById("pieContainer");
      var bar = document.getElementById("barContainer");
      pie.style.display = "none";
      pie.classList.remove("fade-in");
      bar.classList.add("fade-in");
      bar.style.display = "block";
      var barChart = new Chart(document.getElementById("bar-chart"), {
    type: 'bar',
    data: {
      labels: ["특수문자","숫자", "URL", "대문자", "공백", "개행","명사","대명사","동사","형용사","부사","문장의 단어 수","문장의 문자 수","문단의 단어 수","문단의 문자 수"],
      datasets: [
        {
          label: "개수",
          backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850", "#9B3E25", "#FFAABD", "#FF6384","#C0C81A", "#80C024",  "#E9850C", "#6ECDDB", "#9EE796", "#E1DD7D","#FF5733" ],
          data: featureArray
        }
      ]
    },
    options: {
      legend: { display: false },
      title: {
        display: true,
        text: 'SPAM Feature 특성',
        fontSize: 30
      },
      scales:{
        xAxes:[{
          ticks:{
            fontSize: 16
          }
        }]
      },
      tooltips: {
      titleFontSize: 18,
      bodyFontSize: 30,  
    }
    }
});
      }

        function refresh(){
      event.preventDefault();
      showOverLay(); 
             setTimeout(function () {
                hideOverlay(); 
                location.reload(); 
            }, 200);
    }
    var idx = 0;
    function control(right){
      if(right){
        if(idx ==1) {window.alert("이동 할 수 없습니다.");  return;}
        idx++;
      }
      else{
        if(idx ==0) {window.alert("이동 할 수 없습니다.");  return;}
        idx--;
      }
      switch(idx){
        case 0:
          openGraph();
          break;
        case 1:
          openBar();
          break;
        default:
          break;
      }

    }
      </script>
      <title>분석 결과</title>
    </head>
  <body>
     
    <header>
    <img src = "arrow.png" onclick="refresh()" id="arrow">
    <h1 class="stylish-header">분석 결과</h1>
    <img src = "home.png" onclick="homePage()" id="hicon">
      </header>
    <section>
    <img src = "left.png" onclick="control(0)" id="iter">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <div id = "pieContainer">
        <canvas id="pie-chart"></canvas>
</div>
<div id="barContainer">
  <canvas id="bar-chart" ></canvas>
  </div>
      <img src = "right.png" onclick="control(1)" id="iter">
      </section>
    <footer>
    <h4> Copyright © 2023 Team_49 All rights reserved.</h4>
    </footer>
  </body> 
</html>

