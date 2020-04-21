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
    <style>
        canvas {border:1px solid #a9a9a9; background-color: #000000}
    </style>
</head>

<body onload="startGame()">

    <br/>

    <p id="score">Score: 0<br/></p>
    <span id="prompt"></span>
    <span id="digitPrompt"></span>
    <span id="digitPromptPt2"></span>
    <p></p> <!-- for some reason <br/> doesn't work here -->

    <script type="text/javascript">

        var difficulty = localStorage.getItem("difficulty");
        console.log("Difficulty is "+difficulty);

        var indexToPlace = ["ones", "tens", "hundreds", "thousands"];
        // our game doesn't use thousands place right now but later we might want to
        // this can be expanded for bigger numbers, maybe as a way of leveling up in digit identification

        // for the digit identification mode, a prompt is necessary
        if(difficulty == 0){
            document.getElementById("prompt").innerHTML = "Enter the digit in the ";
            document.getElementById("digitPromptPt2").innerHTML = " place";
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
                var problem = num1 +"+"+num2 +"=";
            }else if(randomSign == 2){
                var problem = num1 +"-"+num2 +"=";
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

        var problems = [];
        var playing = true;
        var score = 0;

        var spawnMax = 300;
        var spawnTimer = 200;
        var bossCountdown = 10 - difficulty;

        var wrongSequence = 0;

        // this will be used to make the digit identification game less cheatable
        // for every wrong answer in a row the user will lose a point
        // this will make the cheat method of typing in each digit of each number
        // for the digit identification game useless
        // There will be a grace number of 1 for the digit game, and 2 for other modes
        var wrongInARow = 0;

        function startGame() {
            addProblem();
            gameArena.start();
        }

        //Adds a problem to the array of problems.
        function addProblem() {
            var xSpawn = getRandomNumber(10,300); 

            var myGamePiece = new object(xSpawn, -50);
            problems.push(myGamePiece);
            bossCountdown--;
        }

        // Jack should comment this
        var gameArena = {
            canvas : document.createElement("canvas"),
            start : function() {
                this.canvas.width = 400;
                this.canvas.height = 600;
                this.context = this.canvas.getContext("2d");
                document.body.insertBefore(this.canvas, document.body.childNodes[0]);
                this.interval = setInterval(updateGameArea, 15);        
            },
            clear : function() {
                this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
            }
        }

        // an object representing each problem and all it's properties
        function object(x, y) {
            if(difficulty != 0){
                this.problem = createMathProblem(0, 10);
            }else{
                this.problem = getRandomNumber(100, 1000);
            }

            this.x = x;
            this.y = y;
            this.speed = 1;
            this.color = 'White';
            this.font = "16px Courier New";
            this.boss = false;

            //Checks if it's time to create a boss problem.
            if((bossCountdown <= 0)&&(difficulty !=0)){
                this.problem = createMathProblem(10,20);
                //this.color = "Yellow";
                this.font = "22px Courier New";
                this.boss = true;
                bossCountdown = getRandomNumber(5,15) - difficulty;
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
                ones = this.problem % 10; // could be Math.floor(this.problem / 1) % 10, same thing
                tens = Math.floor(this.problem / 10) % 10;
                hundreds = Math.floor(this.problem / 100) % 10;
                placeValues = [ones, tens, hundreds];

                // rnd will be 0, 1, or 2
                rnd = getRandomNumber(0,3);
                this.answerIdx = rnd;
                this.answer = placeValues[rnd];
                console.log(this.answer);
            }

            //Deletes and redraws the equation at its new location.
            this.update = function() {
                ctx = gameArena.context;
                ctx.fillStyle = this.color;
                ctx.font = this.font;
                ctx.fillText(this.problem,this.x,this.y);
            }

            //Finds the new location for an equation
            this.newPosition = function() {
                this.y += this.speed;    
                this.wrong();
                this.hitBottom();    
            }

            //Checks to see if the equation has hit the bottom.
            this.hitBottom = function() {
                var bottom = gameArena.canvas.height - 18;
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

                gameArena.clear();
                spawnTimer--;

                for(var i = 0; i < problems.length; i++){
                problems[i].newPosition();
                problems[i].update();
                }

                if(spawnTimer <= 0){
                    spawnTimer = getRandomNumber(10,spawnMax);
                    addProblem();
                }

                // make the problem at hand aqua, but no need if it already is
                if(problems[0].color != 'Aqua'){
                    problems[0].updateColor('Aqua');
                }

                // for digit identification, update which place you are asking for
                if(difficulty == 0){
                    document.getElementById("digitPrompt").innerHTML = indexToPlace[problems[0].answerIdx];
                }
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

                if(problems.length >= 1){
                    if(userAns == problems[0].getAnswer()){
                        problems.splice(0, 1);
                        score++;
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
