<!DOCTYPE html>
<html lang="en">

<head>
    <title>css animation test</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="cssAnimTestStylesheet.css">
</head>

<body>
    <div id="stage">
        <canvas id="top-layer" width="400" height="600"></canvas>
        <canvas id="mid-layer" width="400" height="600"></canvas>
    </div>
    <div id="main" style="position:absolute; left:0px; top:0px"></div>
    <button onclick="createDiv()">create div</button>
    <button onclick="moveDiv()">move the div</button>
    <button onclick="deleteDiv()">delete the div</button>
    <button onclick="changeDivBackground()">change div background</button>
    <button onclick="clearAll()">clear all asteroids</button>
    <button onclick="drawExplosionFrame()">draw explosion frame</button>

    <!--<div class="asteroid"></div>-->
    <script type="text/javascript">
        var div;

        const midCanvas = document.getElementById("mid-layer");
        const midCtx = midCanvas.getContext('2d');

        var explosionImage = new Image();
        explosionImage.src = 'assets/explosion/frame_7.png';

        var asteroids = [];

        var asteroid;

        function createDiv(){
            asteroid = new asteroidDiv();
            setTimeout(function(){console.log(parseInt(asteroid.div.getBoundingClientRect().top + window.scrollY))}, 1500);
        }

        function drawExplosionFrame(){
            midCtx.drawImage(explosionImage, -450, 0, 1280, 720);
        }

        function asteroidDiv(){
            // create it
            this.div = document.createElement("div");
            var tmpInt = 100;
            // set its size
            this.div.style.width = tmpInt + "px";
            this.div.style.height = tmpInt + "px";
            // set its background image
            this.div.style.backgroundImage = "url(assets/asteroid_square_grey_v1.png)";
            this.div.style.backgroundSize = "cover";
            // set text style attributes
            this.div.style.color = "aqua";
            this.div.style.fontSize = "24px";
            this.div.style.fontFamily = "Courier New";
            this.div.style.fontWeight = "bold";
            // set position style
            this.div.style.position = "absolute";
            // set x-position
            this.div.style.left = Math.floor(500 + Math.random() * 200) + "px";
            // set class
            this.div.setAttribute("class", "asteroid");
            // set text
            this.div.innerHTML = "<br/>3+6";
            this.div.id = "myDiv";

            console.log("rect.top: " + this.div.getBoundingClientRect().top);

            // set what to do when it reaches the bottom
            this.div.addEventListener("animationend", function(){
                alert("yo");
                console.log("rect.top: " + parseInt(asteroid.div.getBoundingClientRect().top + window.scrollY));
            });

            // append div to main
            document.getElementById("main").appendChild(this.div);

            asteroids.push(this.div);
        }

        function moveDiv(){
            div.style.left = "600px";
        }

        function deleteDiv(){
            document.getElementById("myDiv").remove();
        }

        function clearAll(){
            for(var i = 0; i < asteroids.length; i++){
                asteroids[i].remove();
            }
        }

        function changeDivBackground(){
            div.style.backgroundImage = "url(assets/asteroid_2_v2_red.png)";
        }
    </script>
</body>
</html>