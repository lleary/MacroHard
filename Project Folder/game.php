<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="stylesheet" type="text/css"href="stylesheet.css">
<style>
canvas {
    border:1px solid #d3d3d3;
    background-color: #000000;
}
</style>
</head>
<body onload="startGame()">

<?php

   function prepare_query_string(){
        $re = [];
        $query_array = explode("&", $_SERVER["QUERY_STRING"]);
        foreach ($query_array as $key => $value) {
        $temp = explode("=", $value);
        $re[$temp[0]] = $temp[1]; 
        }
        return $re;
    }
?>

<script type="text/javascript"> 
    function createAdditionProblem() {
            var min=1; 
            var max=10;  
            var num1 = Math.random() * (+max - +min) + +min; 
            num1 = Math.floor(num1)
            var num2 = Math.random() * (+max - +min) + +min; 
            num2 = Math.floor(num2)
            var ans = num1+num2; 
            var problem = num1 +"+"+num2 +"=";
            return problem;
        }
        createAdditionProblem();
</script> 
<br />


<script>

var myGamePieces = [];
var playing = true;
var score = 0;

    var spawnTimer = 200; 

function startGame() {
    addPiece();
    myGameArea.start();
}

function addPiece() {
    var min=10; 
    var max=300;  
    var xSpawn = Math.random() * (+max - +min) + +min; 
    xSpawn = Math.floor(xSpawn)

    var myGamePiece = new component(30, 30, "white", xSpawn, -50);
    myGamePieces.push(myGamePiece);
}

var myGameArea = {
    canvas : document.createElement("canvas"),
    start : function() {
        this.canvas.width = 400;
        this.canvas.height = 600;
        this.context = this.canvas.getContext("2d");
        document.body.insertBefore(this.canvas, document.body.childNodes[0]);
        this.interval = setInterval(updateGameArea, 15);        
    },
    stop : function() {
        clearInterval(this.interval);
    },    
    clear : function() {
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
    }
}

function component(width, height, color, x, y, type) {
    this.problem = createAdditionProblem();

    //this.problem = color;
    //console.log(mathProblem)
    this.type = type;
    this.width = width;
    this.height = height;
    this.x = x;
    this.y = y;    
    this.speedX = 0;
    this.speedY = 0;    
    this.gravity = 0.03;
    this.gravitySpeed = 1;
    this.update = function() {
        ctx = myGameArea.context;
        ctx.fillStyle = color;
        //ctx.fillRect(this.x, this.y, this.width, this.height);
        ctx.font = "16px Courier New";
        ctx.fillText(this.problem,this.x,this.y);
        //createAdditionProblem();
    }
    this.newPos = function() {
        this.gravitySpeed += 0;
        this.x += this.speedX;
        this.y += this.speedY + this.gravitySpeed;    
        this.hitBottom();    
    }
    this.hitBottom = function() {
        var rockbottom = myGameArea.canvas.height - this.height;
        if (this.y > rockbottom) {
            youLose();
        }
    }
}

function updateGameArea() {
    if (playing == true){
        myGameArea.clear();
        for(var i = 0; i < myGamePieces.length; i++){
        myGamePieces[i].newPos();
        myGamePieces[i].update();
        }
        spawnTimer--;
        if(spawnTimer <=0){
            spawnTimer = 200;
            addPiece();
        }
    }
}

function increaseScore(){
    document.getElementById("score").innerHTML = "Score: "+score;
}

function youLose(){
    document.getElementById("score").innerHTML = "You Lose! Loser! You are not smart! You will never accomplish anything. Santa Claus isn't real";
    playing = false;
}



</script>

<p id="score">Score: 0</p>

<br />
    <form>
        Answer:
        <input type="text" name="answer" id="answer" placeholder="answer" autofocus/>
        <br/>
        <br/>
    </form>

    <form action="../mainMenu.php/?user=$user">
        <button type="submit">Return to main menu</button></a>
    </form>


</body>
</html>
