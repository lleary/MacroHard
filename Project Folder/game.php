<?php 
session_start();
  if (!isset($_SESSION['user']))
   {
      header("location: welcome.php");
      die();
   }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Matheroids</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" type="text/css"href="stylesheet.css">
    <canvas id="sandbox"></canvas>
    <style>
        canvas {border:1px solid #a9a9a9; background-color: #000000}
    </style>
</head>

<body onload="startGame()">

    <br/>

    <p id="score">Score: 0<br/></p>
    <span id="prompt"></span>
    <p></p> <!-- for some reason <br/> doesn't work here -->

    <script type="text/javascript">
        const myCanvas = document.getElementById("sandbox");
        myCanvas.width = 400;
        myCanvas.height = 600;
        const ctx = myCanvas.getContext("2d");
        var bgImage = new Image(400,600);
        bgImage.src = 'assets/stars_v1.jpg';
        ctx.drawImage(bgImage,0,0);
        var astImage1 = new Image();
        astImage1.src = 'assets/asteroid_2_v2_default.png';
        var astImage2 = new Image();
        astImage2.src = 'assets/asteroid_2_v2_red.png';


        var difficulty = localStorage.getItem("difficulty");
        console.log("Difficulty is "+difficulty);

        var indexToPlace = ["ones", "tens", "hundreds", "thousands"];
        // boss problems can use the thousands place

        // for the digit identification mode, a prompt is necessary
        if(difficulty == 0){
            document.getElementById("prompt").innerHTML = "Enter the digit in the stated place";
        }

        // 0 = digit identification
        // 1 = addition only.
        // 2 = subtraction and addition.

        //Generates a random addition problem between min and max. Returns the problem in string form.
        function createMathProblem(min, max) { 

            if(difficulty == 1){
                var randomSign = 1;
            }else if(difficulty == 2){
                var randomSign = 2
            } else {
                var randomSign = getRandomNumber(1, 3);
            }

            var num1 = getRandomNumber(min, max);
            var num2 = getRandomNumber(min, num1);

            if(randomSign == 1){
                var problem = num1 +"+"+num2;
            }else if(randomSign == 2){
                var problem = num1 +"-"+num2;
            }

            return problem;
        }
            
        //Solves an addition problem in the form "a+b=". Returns the answer.
        function solveMathProblem(problem){
            var operatorLocation;
            var equalLocation;
            var operator;

            for(var i = 0; i < problem.length; i++){
                if(problem.charAt(i) == "+"){
                    operatorLocation = i;
                    operator = "+";
                } else if (problem.charAt(i) == "="){
                    equalLocation = i;
                } else if (problem.charAt(i) == "-"){
                    operatorLocation = i;
                    operator = "-";
                }
            }

            var num1 = parseInt(problem.slice(0,operatorLocation));
            var num2 = parseInt(problem.slice(operatorLocation+1,equalLocation));

            if(operator == "+"){
                var ans = num1+num2;
                console.log(num1+" + "+num2+"="+ans);
            } else if (operator == "-"){
                var ans = num1-num2;
                console.log(num1+" - "+num2+"="+ans);
            }

            return ans;
        }

        // Returns a random integer number x: [min,max)
        function getRandomNumber(min, max){
            var num = Math.random() * (+max - +min) + +min; 
            num = Math.floor(num)

            return num;
        }

        var matheroids = [];
        var problems = [];
        var playing = true;
        var score = 0;

        var spawnMax = 300;
        var spawnTimer = 200;
        var bossCountdown = 10 - difficulty;

        var wrongSequence = 0;

        var gameInterval;

        // this will be used to make the digit identification game less cheatable
        // for every wrong answer in a row the user will lose a point
        // this will make the cheat method of typing in each digit of each number
        // for the digit identification game useless
        // There will be a grace number of 1 for the digit game, and 2 for other modes
        var wrongInARow = 0;

        function startGame() {
            addProblem();
            document.body.insertBefore(myCanvas, document.body.childNodes[0]);
            gameInterval = setInterval(updateGameArea, 15);
        }

        //Adds a problem to the array of problems.
        function addProblem() {
            var xSpawn;
            if(difficulty != 0){
                xSpawn = getRandomNumber(50,300); 
            }else{
                xSpawn = getRandomNumber(50,250);
            }

            var myGamePiece = new matheroid(xSpawn, -50, 20, 'grey');
            matheroids.push(myGamePiece);
            bossCountdown--;
        }

        // a class for digit id problems
        function digitProblem(numberInput){
            this.num = numberInput;
            this.str = "error: dig_str_uninit";
        }

        // an object represeting an entire matheroid
        function matheroid(xCoord, yCoord, r, c){
            this.object = new object(xCoord, yCoord);
            this.x = xCoord;
            this.y = yCoord;
            this.radius = r;
            if(this.object.boss){
                this.radius = r * 2;
            }
            this.astColor = c;
            this.objColor = object.color;
            this.image = astImage1;

            this.update = function(){
                if(this.object.boss){
                    ctx.drawImage(this.image, this.x - 50, this.y - 50, 100, 100);
                }else{
                    if(difficulty != 0){
                        ctx.drawImage(this.image, this.x - 25, this.y - 25, 50, 50);
                    }
                    else{
                        ctx.drawImage(this.image, this.x - 30, this.y - 30, 60, 60);
                    }
                }

                // update the math problem inside
                this.object.update();
            }

            this.newPosition = function(){
                this.object.newPosition();
                this.x = this.object.x;
                this.y = this.object.y;

            }

            //Returns an integer which is the answer to the problem
            this.getAnswer = function(){
                return this.object.answer;
            }

            this.getProblem = function(){
                return this.object.problem;
            }

            this.setProblem = function(newProblem){
                this.object.problem = newProblem;
            }

            //Updates the color of an equation
            this.updateColor = function(newColor){
                this.object.color = newColor;
            }

            //Returns whether or not the problem is a boss.
            this.getBossStatus = function(){
                return this.object.boss;
            }
        }

        // an object representing each problem
        function object(x, y) {
            this.x = x;
            this.y = y;
            if(difficulty != 0){
                this.problem = createMathProblem(0, 10);
            }else{
                this.problem = new digitProblem(getRandomNumber(100, 1000));
            }

            this.speed = 1;
            this.color = 'White';
            this.font = "bold 18px Courier New";
            this.boss = false;

            //Checks if it's time to create a boss problem.
            if(bossCountdown <= 0){
                if(difficulty != 0){
                    this.problem = createMathProblem(10,20);
                    //this.color = "Yellow";
                    this.font = "bold 24px Courier New";
                    this.boss = true;
                    bossCountdown = getRandomNumber(5,15) - difficulty;
                }
                else{
                    this.problem = new digitProblem(getRandomNumber(1000, 10000));
                    this.font = "bold 24px Courier New";
                    this.boss = true;
                    bossCountdown = getRandomNumber(5,15) - difficulty;
                }
            }

            // set answer for an arithmetic problem
            if(difficulty != 0){
                this.answer = solveMathProblem(this.problem);
            }
            // set answer for a digit identification problem
            else{
                // for digit identification, the answer is going to be one of the digits
                // we can extract each digit from the problem with a modulus function and floored division
                // equation for the Ks digit of n:
                // floor(n / K) % 10
                // ex: floor(1234 / 100) % 10 = floor(12.34) % 10 = 12 % 10 = 2; 2 is the 100s place of 1234
                ones = Math.floor(this.problem.num / 1) % 10;
                tens = Math.floor(this.problem.num / 10) % 10;
                hundreds = Math.floor(this.problem.num / 100) % 10;
                thousands = Math.floor(this.problem.num / 1000) % 10;
                placeValues = [ones, tens, hundreds, thousands];

                numStr = "" + this.problem.num;

                // rnd will be 0, 1, or 2; 3 is an option only for boss problems, whose length is 4
                rnd = getRandomNumber(0,numStr.length);
                this.answer = placeValues[rnd];
                this.problem.str = indexToPlace[rnd];
                console.log(this.answer);
            }

            //Deletes and redraws the equation at its new location.
            this.update = function() {
                ctx.fillStyle = this.color;
                ctx.font = this.font;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                if(difficulty != 0){
                    ctx.fillText(this.problem,this.x,this.y);
                }
                else{
                    ctx.fillText(this.problem.num, this.x, this.y - 8);
                    ctx.fillText(this.problem.str, this.x, this.y + 10);
                }
            }

            //Finds the new location for an equation
            this.newPosition = function() {
                this.y += this.speed;    
                this.wrong();
                this.hitBottom();    
            }

            //Checks to see if the equation has hit the bottom.
            this.hitBottom = function() {
                var bottom = myCanvas.height - 18;
                if (this.y > bottom) {
                    youLose();
                }
            }

            this.wrong = function() {
                if (wrongSequence == 1){
                    this.x += 2;
                    wrongSequence++;
                } else if (wrongSequence == 2){
                    this.x -= 2;
                    wrongSequence++;
                } else if (wrongSequence == 3){
                    this.x -= 2;
                    wrongSequence++;
                } else if (wrongSequence == 4){
                    this.x += 2;
                    wrongSequence = 0;
                }
            }

            //Returns an integer which is the answer to the problem
            this.getAnswer = function(){
                return this.answer;
            }

            this.getProblem = function(){
                return this.problem;
            }

            this.setProblem = function(newProblem){
                this.problem = newProblem;
            }

            //Updates the color of an equation
            this.updateColor = function(newColor){
                this.color = newColor;
            }

            //Returns whether or not the problem is a boss.
            this.getBossStatus = function(){
                return this.boss;
            }
        }

        //Runs every tick.
        function updateGameArea() {
            if (playing == true){

                ctx.clearRect(0, 0, myCanvas.width, myCanvas.height);
                spawnTimer--;

                // redraw background
                ctx.drawImage(bgImage,0,0);

                // this needs to count down so the 0th problem is drawn last, and is thus on top
                for(var i = matheroids.length - 1; i >= 0; i--){
                    matheroids[i].newPosition();
                    matheroids[i].update();
                }

                if(spawnTimer <= 0){
                    spawnTimer = getRandomNumber(10,spawnMax);
                    addProblem();
                }


                matheroids[0].image = astImage2;
                matheroids[0].updateColor('Aqua');
            }
        }

        //Updates the printed Score
        function updateScore(){
            document.getElementById("score").innerHTML = "Score: "+score;
        }

        //Tells the player they lose
        function youLose(){
            playing = false;
            document.getElementById("score").innerHTML = "Game Over.\n Your final score was: "+score;
        }

        //Checks the users given answer.
        function checkAnswer(){
            if(playing == true){
                var userAns = document.getElementById("userAnswer").value;

                if(matheroids.length >= 1){
                    if(userAns == matheroids[0].getAnswer()){
                        score++;
                        // add another 2 to get a total of 3 points for a boss problem
                        if(matheroids[0].getBossStatus()){
                            score++;
                            score++;
                        }
                        matheroids.splice(0, 1);
                        updateScore();

                        if (spawnMax >= 80){
                            spawnMax = spawnMax - getRandomNumber(0,20);
                        }

                        wrongInARow = 0;
                    }
                    else{
                        wrongSequence = 0;
                        wrongInARow++;
                        // for the digit identification gamemode, penalize consecutive wrong answers
                        // with a grace number of 1
                        if(difficulty == 0 && wrongInARow > 1){
                            score--;
                            updateScore();
                        }
                        // for arithmetic, penalize consecutive wrong answers with a grace number of 2
                        else if(wrongInARow > 2){
                            score--;
                            updateScore();
                        }
                    }

                    if(score < 0){
                        score = 0;
                        youLose();
                    }
                }
                document.getElementById("userAnswer").value = "";
            }
        }

    </script>

    <form id="answerForm" onsubmit="checkAnswer(); return false;" autocomplete="off" style="visibility: visible;">
        Answer:
        <input type="text" name="answer"  id="userAnswer" placeholder="answer" autofocus/>
    </form>

    <br />

    <form action="./mainMenu.php" >
        <button type="submit">Return to main menu</button>
    </form>

</body>
</html>
