<!DOCTYPE html>
<html lang="en">
  <link rel="stylesheet" href="load.css">
  <link rel="stylesheet" href="skeleton.css">
  <?php

  $database = new mysqli("127.0.0.1", "root", "dlrkddn1@","graduation_project");

  if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){ 
  $query = "SELECT MAX(idx) AS last_idx FROM mail";
  $result = $database->query($query);
  $row = $result->fetch_assoc();
  $lastIdx = $row['last_idx'];
  $newIdx = $lastIdx + 1;
  $label = $_POST['label'];
  
  if($label == 0) {
      $collect = $_POST['collect'];
      $result = mysqli_query($database, "INSERT INTO mail (idx,collect,label)
      VALUES ('$newIdx','$collect','$label')");
  }
  else if ($label == 1){
      $generate = $_POST['generate'];
      $result = mysqli_query($database, "INSERT INTO mail (idx,generate,label)
      VALUES ('$newIdx','$generate','$label')");
  }
  else if ($label == 2){
    $img = $_POST['img'];
      $result = mysqli_query($database, "INSERT INTO mail (idx,img,label)
      VALUES ('$newIdx','$img','$label')");
}
else if ($label == 3){
  $collect = $_POST['collect'];
  $img = $_POST['img'];
  $result = mysqli_query($database, "INSERT INTO mail (idx,collect,img,label)
  VALUES ('$newIdx','$collect','$img','$label')");
}
else if ($label == 4){
  $generate = $_POST['generate'];
  $img = $_POST['img'];
  $result = mysqli_query($database, "INSERT INTO mail (idx,generate,img,label)
  VALUES ('$newIdx',' $generate','$img','$label')");
}         
}

  $data_arr = array();
  $result = mysqli_query($database, "SELECT * FROM mail");

  while($row = mysqli_fetch_assoc($result)){
    $data = array("idx"=> $row['idx'], "collect"=> $row['collect'], "generate"=> $row['generate'], "img"=> $row['img'], "predict" => $row['predict'], "feature"=>$row['feature'], "label"=> $row['label']);
    $data_arr[] = $data;
  }

 
  mysqli_close($database);
?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
    <style>
      table {
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
   
    input:invalid {
      color: red;
      border: 2px solid red;  /* input이 패턴에 맞지 않을 경우 border 테두리를 빨간색으로 변경 */
      }
    .error{       /*  error시 출력할 statement */
      display: inline;
      padding: 10px;
      font-size: 0.8em;
      color: red;
      visibility: hidden;
      }
    input:invalid+.error{   /* invalid시 visible combinator */
      visibility:visible;
      }
    #mail{
      font-family: Arial, sans-serif;
  font-size: 20px;
  padding: 8px;
  resize: vertical;
  width: 80%; /* Set width to 80% of the parent container */
  height: 70vh;
  border-radius: 5px;
    }
    label {     /* 라벨 오른쪽으로 align */
    width: 50px;
      display: inline-block;
      text-align: right;
      margin-right: 20px;
    }
    
    input[type="text"],     /* input text나 password 설정 바꿔준다. */
    input[type="password"], /* attrubute selecter */
    select {
      width: 250px;
      padding: 15px 15px;
      margin-bottom: 10px;
      font-size: 1em;
    }
    
    input[type="submit"],    /* input submit과 reset 설정 바꿔준다. */
    input[type="reset"] {
        width: 100px;
        height: 60px;
        font-size: 25px;
        margin : 15px;
      padding: 7px 10px;
      background-color: #4CAF50;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
    }
    section input[type="submit"],    /* input submit과 reset 설정 바꿔준다. */
    section input[type="reset"] {
        width: 200px;
        height: 60px;
        font-size: 25px;
        margin : 15px;
      padding: 7px 10px;
      background-color: #4CAF50;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
    }
    #mailText textarea {
      width: 100%;
        flex: 1;
        font-family: Arial, sans-serif; /* Adjust the font family */
        font-size: 20px; /* Adjust the font size */
        margin-top: 10px; /* Add margin to the top */
        margin-bottom: 10px; /* Add margin to the bottom */
        padding: 8px; /* Adjust the padding if needed */
        resize: vertical;
      }
    #mailText input[type="text"]{
      width: 100px;
      padding: 8px 8px;
      margin-bottom: 10px;
      border-radius: 5px;
    }
    #mailText{
      margin: 10px; 
      padding :40px;
      display: none;
      font-size: 20px;
      width: 30%; /* Set width to 30% of the parent container */
      height : 70%;
      border-radius: 5px;
      overflow: auto; /* Add scrollbars when the content overflows the table */
      background-color: whitesmoke;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0));
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      justify-content: center;
      align-items: center;
      flex-direction: column; /* Stack children vertically */
    }
    #generatedTable{
      padding :40px;
      display: none;
      font-size: 20px;
      overflow: auto; /* Add scrollbars when the content overflows the table */
      margin: 10px; 
      width: 30%; /* Set width to 30% of the parent container */
      height : 70%;
      border-radius: 5px;
      background-color: whitesmoke;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0));
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      justify-content: center;
      align-items: center;
      flex-direction: column; /* Stack children vertically */
    }
    #sMail{
      width: 30%; /* Set width to 30% of the parent container */
      height : 70%;
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

#mailButton{
    margin: 13px;
    display: flex; 
    justify-content: center; 
    align-items: center; 
}

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

section {
  display: flex;
  flex-direction: row;
}

#tst{
  visibility : hidden;
  float:right;
  padding: 10px;
  font-size: 1em;
  color: red;
}
tr{
  transition: transform 0.3s, filter 0.3s, border-width 0.3s ease, box-shadow 0.3s ease;
}
.red:hover {
  transform: scale(1.05); /* 살짝 확대 효과 */
  filter: brightness(1.2); /* Brightness increase effect */
  box-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000, 0 0 30px #ff0000;
  transition: background-color 0.5s ease; /* Add a transition effect */
  cursor: pointer; /* Add a pointer cursor to indicate interactivity */
}

.blue:hover {
  transform: scale(1.05); /* 살짝 확대 효과 */
  filter: brightness(1.2); /* Brightness increase effect */
  box-shadow: 0 0 10px #00ff00, 0 0 20px #00ff00, 0 0 30px #00ff00;
  transition: background-color 0.5s ease; /* Add a transition effect */
  cursor: pointer; /* Add a pointer cursor to indicate interactivity */
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
@keyframes blink {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0;
  }
}
.blink {
  animation: blink 1.5s step-start infinite;
}
.hamImage {
    width: 300px;
    height: 300px;
    margin: 50px;
    cursor: pointer;
    border: 1px; 
    border-radius: 8px; 
    transition: transform 0.3s, filter 0.3s, border-width 0.3s ease, box-shadow 0.3s ease;
}
.hamImage:hover {
            transform: scale(1.05); /* 살짝 확대 효과 */
            filter: brightness(1.2); /* 밝기 증가 효과 */
            box-shadow: 0 0 20px #00ff00, 0 0 40px #00ff00, 0 0 60px #00ff00;
        }
.hamImage.zoomed {
    transform: scale(2); /* Increase scale for zoomed effect */
    filter: brightness(1.2); /* Increase brightness for zoomed effect */
    z-index: 1; /* Ensure the zoomed image is on top */
}
.spamImage {
    width: 300px;
    height: 300px;
    margin: 50px;
    cursor: pointer;
    border: 1px; 
    border-radius: 8px; 
    transition: transform 0.3s, filter 0.3s, border-width 0.3s ease, box-shadow 0.3s ease;
}
.spamImage:hover {
            transform: scale(1.05); /* 살짝 확대 효과 */
            filter: brightness(1.2); /* 밝기 증가 효과 */
            box-shadow: 0 0 20px #ff0000, 0 0 40px #ff0000, 0 0 60px #ff0000;
            
        }
.spamImage.zoomed {
    transform: scale(2); /* Increase scale for zoomed effect */
    filter: brightness(1.2); /* Increase brightness for zoomed effect */
    z-index: 1; /* Ensure the zoomed image is on top */
}
#imageGrid {
  border: 1px solid #ccc;
  width: 60%; /* 이미지 그리드의 최대 너비 제한 */
  height : 80%;
    margin-top: 20px;
    overflow: auto; /* 내용이 컨테이너를 넘어갈 경우 스크롤바 추가 */
    margin: 10px; 
    display: none;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}
#voiceRecord{
        display: none;
        width: 40vw; /* 이미지 그리드의 최대 너비 제한 */
    height: 70vh; /* 이미지 그리드의 최대 높이 제한 */
    overflow: auto; /* Add scrollbars when the content overflows the table */
    margin: 0 auto; /* Center the table horizontally */
        background-color: whitesmoke;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0));
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
  justify-content: center;
    align-items: center;
    flex-direction: column; /* Stack children vertically */
    }
#temp{
  display : none;
  margin: 0 auto;
  justify-content: space-evenly;
  align-items: center;
}
#lft, #rgt{
  visibility: hidden;
  margin: 0 auto;
  font-size: 3em;
  margin: 30px;
  font-style: italic; /* Add italic font style */
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Soft glow effect */
  
}
#lft{
  color: blue;
}
#rgt{
  color: orange;
}

    @keyframes ripple {
  from {
    transform: scale(1);
  }
  to {
    transform: scale(1.02);
  }
}
/* component */
.checkbox {
  display: none;
}

.checkbox + label {
  --size: 90%;
  --radius: 50%;
  border: 6px solid black;
  border-radius: var(--radius);
  cursor: pointer;
  display: inline-block;
  height: 80px;
  width: 80px;
  position: relative;
  transition: all 0.3s; 
}
.checkbox + label:hover{
  transform: scale(1.1); /* Enlarge the label slightly */
  filter: brightness(1.2); /* Make the label brighter */
}

.checkbox + label:before {
  background-color: red;
  border-radius: var(--radius);
  bottom: 0;
  content: '';
  height: var(--size);
  left: 0;
  margin: auto;
  position: absolute;
  right: 0;
  top: 0;
  transition: all .25s cubic-bezier(1, 0, 0, 0.2);
  width: var(--size);
}

.checkbox:checked + label {
  animation: .15s ripple .25s;
}

.checkbox:checked + label:before {
  --size: 50%;
  --radius: 10%;
  transform: rotateZ(180deg);
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
#sel{
  position: relative;
  font-size: 20px;
  display: none;
}

.loader-wrapper {
    position: relative;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    margin: auto;
  }
  
  .loader-wrapper .packman::before {
  content: '';
  position: absolute;
  width: 120px; /* Increase the width */
  height: 60px; /* Increase the height */
  background-color: #EFF107;
  border-radius: 240px 240px 0 0; /* Increase the border-radius */
  transform: translate(-50%, -50%);
  animation: pac-top 0.5s linear infinite;
  transform-origin: center bottom;
}

.loader-wrapper .packman::after {
  content: '';
  position: absolute;
  width: 120px; /* Increase the width */
  height: 60px; /* Increase the height */
  background-color: #EFF107;
  border-radius: 0 0 240px 240px; /* Increase the border-radius */
  transform: translate(-50%, 50%);
  animation: pac-bot 0.5s linear infinite;
  transform-origin: center top;
}

@keyframes pac-top {
  0% {
    transform: translate(-50%, -50%) rotate(0)
  }

  50% {
    transform: translate(-50%, -50%) rotate(-30deg)
  }

  100% {
    transform: translate(-50%, -50%) rotate(0)
  }
}

@keyframes pac-bot {
  0% {
    transform: translate(-50%, 50%) rotate(0)
  }

  50% {
    transform: translate(-50%, 50%) rotate(30deg)
  }

  100% {
    transform: translate(-50%, 50%) rotate(0)
  }
}

.dots .dot {
  position: absolute;
  z-index: -1;
  top: 18px; /* Increase the top position */
  width: 20px; /* Increase the width */
  height: 20px; /* Increase the height */
  border-radius: 50%;
  background: #fff;
}

.dots .dot:nth-child(1) {
  left: 105px;
  animation: dot-stage1 0.5s infinite;
}

.dots .dot:nth-child(2) {
  left: 70px;
  animation: dot-stage1 0.5s infinite;
}

.dots .dot:nth-child(3) {
  left: 35px;
  animation: dot-stage1 0.5s infinite;
}

.dots .dot:nth-child(4) {
  left: 10px;
  animation: dot-stage2 0.5s infinite;
}

@keyframes dot-stage1 {
  0% {
    transform: translate(0, 0);
  }

  100% {
    transform: translate(-24px, 0);
  }
}

@keyframes dot-stage2 {
  0% {
    transform: scale(1);
  }

  5%, 100% {
    transform: scale(0);
  }
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
function printTable(){
          event.preventDefault();
          var st = "<table><thead><tr><th>idx</th><th>generate</th></tr></thead><tbody>";
          var cnt = 1;
            //add database element
            for (var i = 0; i < database.length; i++) {
              if(database[i].generate && !database[i].label ){
            
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
              st += '</tr>';
              cnt++;
              }
            }
            st+= "</tbody></table>";
            document.getElementById("generatedTable").innerHTML = st;
        }
        function handleRowClick(row){
          var rowId = row.getAttribute("id");
          var idx = rowId.substring(4);
          var rowData = database.find(item => item.idx == idx);
          openDBMail(rowData);
        }

        var mailSelected = false;
        var sendText;
  
        function openDBMail(data){
          var lft = document.getElementById("lft");
          document.getElementById("generatedTable").style.display = "none";
          var sel = document.getElementById("sel");
          var sMail = document.getElementById("sMail");
          sMail.classList.add("fade-in"); 
          sMail.style.display = "flex";
          sMail.innerHTML = "";
          sMail.innerHTML += '<p><span class="spam-legend">SPAM KEYWORD</span></p>';
          sendText = data.generate;
          //window.alert(sendText);
          var textWithHighlights = data.generate;
          for (var i = 0; i < spam.length; i++) {
            var regex = new RegExp("\\b" + spam[i] + "\\b", "gi"); // Create a regex pattern to match whole words
            textWithHighlights = textWithHighlights.replace(regex, "<span style='background-color: rgba(255, 150, 150, 0.5); font-weight: bold;'>$&</span>");
          }
          sMail.innerHTML += "<div>" + textWithHighlights + "</div>";
          mailSelected = true;
          lft.style.visibility = "visible";
          lft.classList.add("fade-in"); 
        }
        function MAIL(collect,generate,img){
            this.collect = collect;
            this.generate = generate;
            this.img = img;
        }
        function openGeneratedTable(){
          event.preventDefault();
      
          var img = document.getElementById("img");
          var txt = document.getElementById("txt");
          img.style.display ="none";
          txt.style.display ="none";
          var genTb = document.getElementById("generatedTable");
          var tmp = document.getElementById("temp");
          openImage();
          printTable();
          genTb.style.display = "block";
          genTb.classList.add("fade-in");
          tmp.style.display = "flex";
          tmp.classList.add("fade-in");
  }
        function openMail(){
            event.preventDefault();
            openImage();
            var img = document.getElementById("img");
            var txt = document.getElementById("txt");
            img.style.display ="none";
            txt.style.display ="none";
            var tmp = document.getElementById("temp");
            var mailText = document.getElementById("mailText");
            mailText.classList.add("fade-in"); 
            mailText.style.display="flex";
            tmp.style.display = "flex";
          tmp.classList.add("fade-in");
          
        }
        
       
        function openImage(){
          event.preventDefault();
          var imageGrid = document.getElementById("imageGrid");
          var img = document.getElementById("img");
          var txt = document.getElementById("txt");
          img.style.display ="none";
          txt.style.display ="none";

          
          for (var i = 1; i <= 20; i++) {
        var imageElement = document.createElement("img");
        imageElement.id = i-1;
        imageElement.src = "imageSrc/" + i + ".jpg"; // 이미지가 image/1.jpg, image/2.jpg, ..., image/10.jpg와 같은 경로에 있다고 가정합니
        if(i % 2){
          imageElement.classList.add("hamImage"); 
        }
        else{
          imageElement.classList.add("spamImage"); 
        }  
        imageElement.addEventListener("click", function() {
        
        toggleZoomImage(this);
});
        imageGrid.appendChild(imageElement);
    }
    // imageGrid를 보여줍니다.
    imageGrid.style.display = "flex";
    imageGrid.classList.add("fade-in"); 
    }

    var imgSelected = false;
    const hamText = ["ST. ANNS EPISCOPAL SUNDAY SERVICES 8:00 HOLY COMMUNION 10:00 FAMILY SERVICE ALL WELCOME","Canadian Pharmacy for you! Viagra - $3.33 Cialis $3.75 Viagra soft tabs $2.40 Cialis soft tabs $5.78 and more The best quality and the best price! Don t click type in your browser: www.4pharm.net ","WARNING! HAZARDOUS OCEAN CONDITIONS MAY EXIST AT THIS BEACH SUDDEN SHARP JELLYFISH STRONG HIGH SLIPPERY DROP OFF CORAL CURRENT SURF ROCKS","U.S.★ DRUGS Licensed American Pharmacy NO PRESCRIPTION REQUIRED! SAVE UP TO 80% NOW, FAST DELIVERY TO YOUR DOOR! CLICK HERE TO ORDER!","WOMEN WHO STEPPED UP WERE MEASURED AS CITIZENS OF THE NATION, NOT AS WOMEN... THIS WAS A PEOPLES WAR, AND EVERYONE WAS IN IT. COLONEL OVETA CULP HOBBY","www.22rx.com STER 100% Viagra $3.33 Valium $1.21 C200 Cialis $3.75 AMB 5 Ambien $2.89 SOMA Soma- $1.13 SAMAX Xanax $1.42","ONE WAY STOP GPS DO NOT CITY OF CHICAGO 2005 ENTER ALL WAY","Hoodia Maximum Strength Finally... A Natural Suppressant That Works For Everyone! You ve seen it on 60 Minutes and read the BBC News report... now find out just what everyone is talking about and get yourself some Hoodia Maximum Strength today! ■ Suppress your appetite and feel full and satisfied all day long • Increase your energy levels • Lose excess weight • Increase your metabolism Burn body fat Burn calories Attack obesity No known side effects Maximum results seen after only a few weeks Suitable for vegetarians and vegans MAINTAIN your weight loss Make losing weight a sure guarantee Look your best during the summer months CLICK HERE TO LEARN MORE","DONT Cry Over Spilt Milk, Find Another Cow. DAPPY DOPE","Almost 1000 low priced drugs from Canada available for purchase with NO PRESCRIPTION. FREE SHIPPING is available, and most orders are shipped the same day they are ordered. ####################: # # Viagra - Highest quality!!! # Levitra - bestseller! # Cialis BEST BUY # Prozac # Vioxx # Propecia # Soma - BEST PRICE! # Paxil # Celebrex - LOWEST PRICE! # ####### ################################### =>TYPE http://www.artespo.com TO ENTER <= WWW.ARTESPO.COM","LEGO","Southridge Ethanol Inc WATCH SORD TRADE AS MASSIVE PR CAMPAIGN BEGINS. THIS ONE IS SURE TO BE SEEN BY MILLIONS OF INVESTORS! GET ON THE TRAIN BEFORE IT LEAVES! ADD SORD TO YOUR RADAR ON THUR OCT 26! Headlines Oct 19, 2006 SORD.OB Market Info Company Name: SOUTHRIDGE ENTERPRISES Symbol: SORD.OB Tues Close: $1.57 Change: (Trading At Discount) Up 1.27% 3-Day Target: $4 Recommendalton: Strong Buy Southridge Enterprises Retains Investor Relations Firm Oct 17, 2006 Southridge Enterprises Selects Stockwire to Expand Investor Community Expo- sure Visit your favourite financial site to read the complete articles","+ IN LOVING MEMORY OF RICHARD DUGGAN CURTEEN DIED 28 MARCH 1949 AGED 84 YEARS HIS WIFE MARY DUGGAN DIED 21 DEC-1946 ACED 85 YEARS AND THEIR DAUGHTER MARCARET DIED 4 JULY 1920 ACED 18 YEARS.","CONSUMER REPORT • Cant find good drug store? Dont know where to buy pills? • Need to buy medications but dont know where? The answer is simple: Online pharmacy store. We have cheapest prices and best quality drugs. We ship instantly worldwide in unmarked packing, so you dont need to go to your local drug store. US quality only! Type www.titikako.com to Enter! And make secure, confidential purchase!","This IS your brain on chicken dogs","Most Popular Generic Cialis C20 Tadalafil 20mg PRICE per pill $3.00 iDetails BUY NOW! Generic Viagra Sildenafil 50mg/ Pfizer 100mg Details PRICE per pail $1.78 BUY NOW! Generic Levitra PRICE BAYER Vardenafil 20mg per pill $3.33 iDetails BUY NOW!","BEER: 3 SO MUCH MORE than just a Breakfast Drink.","C20 type in your browser www.20pills.com Generic Cialis Tadalafil 20mg Details $99.95 Generic Viagra Sildenafil 50mg/ 100mg Details $69.95 Other Pills With Discount! Just Type in your browser window. http://www.20pills.com -Best Prices -Fast Shipping -Best Quality","Happy Birthday Bastian!!","HoodiaLife TH) Lose 25 pounds in 1 Month! Discover the New Miracle HoodiaLife Supplement for Safe, Effective Weight Loss! Click Here To Order! Featured on: OPRAH 50 bc ABC7 TODAY MINUTES"];
    var imgText;
    function toggleZoomImage(imageElement) {
    var allImages =document.querySelectorAll(".spamImage, .hamImage");
    var rgt = document.getElementById("rgt");
    if (imageElement.classList.contains("zoomed")) {
        imageElement.classList.remove("zoomed");
        allImages.forEach(function (img) {
            img.classList.add("fade-in");
            img.style.display = "block"; // Restore all images
        });
        imgSelected = false;
        imgText = "";
        rgt.style.visibility = "hidden";
        rgt.classList.remove("fade-in"); 
  
    } else {
        allImages.forEach(function(img){
            if (img !== imageElement) {
                img.style.display = "none"; // Hide non-zoomed images
            }
        });
        var idx = imageElement.getAttribute("id");
        imgText = hamText[idx];
        imgSelected = true;
        rgt.style.visibility = "visible";
        rgt.classList.add("fade-in"); 
        imageElement.classList.add("zoomed");
        imageElement.classList.add("fade-in"); // Add the fade-in class
      
    }
}
document.addEventListener("DOMContentLoaded", function() {
  var textarea = document.getElementById("mail");
  var lft = document.getElementById("lft");
  textarea.addEventListener("keyup", function() {
    var textContent = textarea.value.trim();
  // Check if the textarea is empty
if (textContent.length === 0) {
  lft.style.visibility = "hidden";
  lft.classList.remove("fade-in");
} else {
  lft.style.visibility = "visible";
  lft.classList.add("fade-in");
}
  });
});


function getLabel(text,img,create){ //텍스트 0, 생성 1, 이미지 2, 텍스트 + 이미지 3, 생성 + 이미지 4, 
  var label = -1;
  if(text){ //텍스트가 존재하면
    if(img) label = 3;
    else label = 0; 

  }
  else if(create){
    if(img) label = 4;
    else label = 1;
  }
  else if(img){
    label = 2;
  }
  return label;
}
function convert(inputString) {
  const resultString = inputString.replace(/'/g, "''");
  return resultString;
}


    function send(){

    var mailText =document.getElementById("mail").value;

    var isText;
    if(mailText ==""){
      isText = false;
    }
    else{
      isText =true;
    }
    var label = getLabel(isText,imgSelected,mailSelected); 
    //window.alert(label);
           var mail;
           const blank = "";
            var formData = new FormData();
            if(label == 0){
              
              formData.append('collect', convert(mailText));
              mail = new MAIL(blank,blank,mailText);
            }
            else if(label == 1){ 
              formData.append('generate', convert(sendText));
              mail = new MAIL(blank,sendText,blank);
            }
            else if(label == 2){
              formData.append('img', imgText);
              mail = new MAIL(imgText,blank,blank);
            }
            else if(label == 3){
              formData.append('collect', convert(mailText));
              formData.append('img', imgText);
              mail = new MAIL(imgText,blank,mailText);
            }
            else if (label ==4){
              formData.append('generate', convert(sendText));
              formData.append('img', imgText);
              mail = new MAIL(imgText,sendText,blank);
            }
            formData.append('label',label);

            localStorage.setItem("mail",JSON.stringify(mail)); //로컬 스토리지에 문자열 형태로 저장

            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "", true);
            xhttp.onreadystatechange = function () {
              if (this.readyState === 4) {
                if (this.status === 200) {
            } else {
                window.alert("error!");
            }
        }

    };
    // Send the request with the form data
    xhttp.send(formData);

    showBar(); // Show the loading overlay
    setTimeout(function () {
                hideOverlay(); // Hide the loading overlay
                location.href = 'mainPage.php'; 
            }, 5000); // 5-second delay
    
    }
    function refresh(){
      event.preventDefault();
      showOverLay(); // Show the loading overlay
             setTimeout(function () {
                hideOverlay(); // Hide the loading overlay
                location.reload(); // Redirect to the info page
            }, 200); // 1-second delay
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
    function showBar(){
      var overlay = document.createElement("div");
      overlay.classList.add("overlay");
      var loader = document.createElement("div"); 
      loader.classList.add("loader-wrapper");
      var packman =  document.createElement("div"); 
      packman.classList.add("packman");
      var dots = document.createElement("div");
      dots.classList.add("dots");
      for(var i =0;i<4;i++){
        var dot = document.createElement("div");
        dot.classList.add("dot");
        dots.appendChild(dot);
      }
      loader.appendChild(packman);
      loader.appendChild(dots);
      overlay.appendChild(loader);
      document.body.appendChild(overlay);
    }
    
    </script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
  const checkbox = document.querySelector(".checkbox");

  checkbox.addEventListener("change", function() {
    if (this.checked) {
      startRecord(); // Call the record() function when checkbox is checked
    } else {
      endRecord(); // Call the endRecord() function when checkbox is unchecked
    }
  });
});
  // ----- 현재 브라우저에서 API 사용이 유효한가를 검증
  function availabilityFunc() {
    //현재 SpeechRecognition을 지원하는 크롬 버전과 webkit 형태로 제공되는 버전이 있으므로 둘 중 해당하는 생성자를 호출한다.
    recognition = new webkitSpeechRecognition() || new SpeechRecognition();
    speechRecognitionList = new SpeechGrammarList();
    recognition.grammars = speechRecognitionList;
    recognition.interimResults = true; //실시간 인식
    recognition.lang = "ko-KR"; // 음성인식에 사용되고 반환될 언어를 설정한다.
    recognition.continuous = true;
    recognition.maxAlternatives = 10; //음성 인식결과 텍스트 보여주는 것
    if (!recognition) {
      alert("현재 브라우저는 사용이 불가능합니다.");
    }
  }
  // --- 음성녹음을 실행하는 함수
  function startRecord() {
      event.preventDefault();
      window.alert("녹음 시작");
      const tstElement = document.getElementById("tst");
    recognition.addEventListener("speechstart", () => {
      tstElement.style.visibility = "visible";
      tstElement.classList.add("blink");
    });
    recognition.addEventListener("speechend", () => {
      tstElement.classList.remove("blink");
      tstElement.style.visibility ="hidden";
      const checkbox = document.querySelector(".checkbox");
      checkbox.checked = false; // Set the checkbox state to unchecked
    });
  
    //음성인식 결과를 반환
    // SpeechRecognitionResult 에 담겨서 반환된다.
    recognition.addEventListener("result", (e) => {
      var searchConsole = document.getElementById("mail");
      searchConsole.value = e.results[0][0].transcript;
      var lft = document.getElementById("lft");
      lft.style.visibility = "visible";
      lft.classList.add("fade-in");
    });
    recognition.start();
  }
  function endRecord() {
    recognition.stop(); // 음성인식을 중단하고 중단까지의 결과를 반환
    const checkbox = document.querySelector(".checkbox");
    checkbox.checked = false; // Set the checkbox state to unchecked
  }
  window.addEventListener("load", availabilityFunc);
  </script>

    <title>Multi-Modal Mail</title>
</head>
<body>
<header>
<img src = "arrow.png" onclick="refresh()" id="arrow">
<h1 class="stylish-header">Multi-Modal Mail</h1>
<img src = "home.png" onclick="homePage()" id="hicon">
</header>
<section>
<div class="button-container">
            <button id="img" class="action-button" onclick="openGeneratedTable()">생성 & 이미지</button>
            <button id="txt" class="action-button" onclick="openMail()">텍스트 & 이미지</button>
        </div>
    <div id ="mailText" class="fade-in">
    <textarea id = "mail" name="mail" rows="38" cols="80" placeholder="Write Message."></textarea>
    <div id = mailButton>
    <input type="checkbox" name="checkbox" class="checkbox" id="checkbox">
<label for="checkbox"></label>
    </div>
    <div id="tst">음성 감지</div>
</div>

<div id ="generatedTable"></div>
<div id ="sMail" ></div>
<div id="imageGrid" ></div>
</section>
<div id ="temp">
<p id = "lft">text selected</p>
<button id="both" class="action-button" onclick="send()">제출</button>
<p id ="rgt">image selected</p>
</div>

<footer>
<h4> Copyright © 2023 Team_49 All rights reserved.</h4>
</footer>

</body>
</html>