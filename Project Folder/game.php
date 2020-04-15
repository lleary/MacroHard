<?php
    session_start();
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

    <br />

    <script type="text/javascript">

        var difficulty = localStorage.getItem("difficulty");
        console.log("Difficulty is "+difficulty);
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

        //Generates a random numbers between a given a min/max. Returns the number.
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

        function object(x, y) {
            this.problem = createMathProblem(0, 10);

            this.x = x;
            this.y = y;
            this.speed = 1;
            this.color = 'White';
            this.font = "16px Courier New";
            this.boss = false;

            //Checks if it's time to create a boss problem.
            if(bossCountdown <= 0){
                this.problem = createMathProblem(10,20);
                //this.color = "Yellow";
                this.font = "22px Courier New";
                this.boss = true;
                bossCountdown = getRandomNumber(5,15) - difficulty;
            }

            this.answer = solveMathProblem(this.problem);

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

            //Returns the answer of the equation.
            this.getAnswer = function(){
                return this.answer;
            }

            //Updates the color of an equation
            this.updateColor = function(newColor){
                this.color = newColor;
            }

            //Returns wether or not the problem is a boss.
            this.getBossStatus = function(){
                return this.boss;
            }
        }

        //Reutns every tick.
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

                if((problems.length >= 1)&&(problems[0].getBossStatus() == false)){
                    problems[0].updateColor('Aqua');
                }else{
                    //problems[0].updateColor('Orange');
                    problems[0].updateColor('Aqua');
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
            document.getElementById("score").innerHTML = "Game Over. Try again!\n Your final score was: "+score;
        }

        //Checks the users given answer.
        function checkAnswer(){
            if(playing == true){
                var ans = document.getElementById("userAnswer").value;

                //for(var i = 0; i < problems.length; i++){

                    if(ans == problems[0].getAnswer()){
                        problems.splice(0, 1);
                        score++;
                        updateScore();

                        if (spawnMax >= 80){
                            spawnMax = spawnMax - getRandomNumber(0,20);
                        }

                        //break;
                    }else{
                        wrongSequence = 0;
                    }
                //}
                document.getElementById("userAnswer").value = "";
            }
        }

    </script>

    <p id="score">Score: 0</p>

    <form onsubmit="checkAnswer(); return false;" autocomplete="off">
        Answer:
        <input type="text" name="answer"  id="userAnswer" placeholder="answer" autofocus/>
    </form>

    <br />

    <form action="../Project Folder/mainMenu.php/" >
        <button type="submit">Return to main menu</button>
    </form>

</body>
</html>
