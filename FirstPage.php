<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="load.css">
<link rel="stylesheet" href="skeleton.css">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
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
    </style>
    <script>
        function writeMail(){
            event.preventDefault();
            showOverLay();
            setTimeout(function () {
                hideOverlay(); // Hide the loading overlay
                location.href = "mail.php"; // Redirect to the info page
            }, 1000); // 1-second delay
        }
      
        function openInfo(){
            event.preventDefault();
             showOverLay(); // Show the loading overlay
             setTimeout(function () {
                hideOverlay(); // Hide the loading overlay
                location.href = "info.html"; // Redirect to the info page
            }, 1000); // 1-second delay
    
        }
        function openDbPage(){
            event.preventDefault();
            showOverLay(); // Show the loading overlay
             setTimeout(function () {
                hideOverlay(); // Hide the loading overlay
                location.href = "new.php"; // Redirect to the info page
            }, 1000); // 1-second delay
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
    </script>
    <title>HomePage</title>
</head>
<body>
<header>
<img src = "arrow.png" onclick="homePage()" id="arrow">
<h1 class="stylish-header">HomePage</h1>
<img src = "home.png" onclick="homePage()" id="hicon">
</header>
<section>
<div class="button-container">
            <button class="action-button" onclick="openInfo()">머신러닝 소개</button>
            <button class="action-button" onclick="writeMail()">메일 쓰기</button>
            <button class="action-button" onclick="openDbPage()">DB 조회</button>
        </div>
</section>
<footer>
    <h4> Copyright © 2023 Team_49 All rights reserved.</h4>
</footer>
    
</body>
</html>