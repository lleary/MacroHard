<?php session_start();
  if (!isset($_SESSION['user'])){
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
        canvas {
            border:1px solid #a9a9a9;
            background-image: url("assets/stars_v2.jpg");
        }
        body{
            background-color: #44444B;
        }
        form {
            text-align: center;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>

<body onload="startGame()">

    <p style="color:white; text-align:center;" id="score">Score: 0<br/></p>
    <span style="color:white; text-align:center; display:block;" id="prompt"></span>
    <p></p> <!-- for some reason <br/> doesn't work here -->

    <form id="answerForm" onsubmit="shoot(); return false;" autocomplete="off" style="visibility: visible; color:white;">
        Answer:
        <input type="text" name="answer"  id="userAnswer" placeholder="answer" autofocus/>
    </form>

    <br />

    <div style="text-align: center">
        <button type="button" onclick="resetGame()">Reset</button>
    </div>

    <form action="./mainMenu.php" >
        <button type="submit">Main menu</button>
    </form>

    <script type="text/javascript">
        const myCanvas = document.getElementById("sandbox");
        myCanvas.width = 400;
        myCanvas.height = 600;
        const ctx = myCanvas.getContext("2d");
        var astImage1 = new Image();
        astImage1.src = 'assets/asteroid_2_v2_default.png';
        var astImage2 = new Image();
        astImage2.src = 'assets/asteroid_2_v2_red.png';

        var explosionFrames = [];

        for(var idx = 0; idx < 44; idx++){
            explosionFrames.push(new Image());
            explosionFrames[idx].src = "assets/explosion/frame_" + idx + ".png";
        }

        var gamemode = localStorage.getItem("gamemode");
        console.log("Gamemode is " + gamemode);

        var indexToPlace = ["ones", "tens", "hundreds", "thousands"];

        
        // these are variables that should ultimately be retrieved as user data and controlled by the teacher for each user or each classroom. That control will be our version of the "Teacher can add problems" feature. These factors work together to create the overall difficulty of the game.
        var bossAnswerMin = 20; // mininum answer to boss arithmetic problems, inclusive
        var bossAnswerMax = 39; // maximum answer to boss arithmetic problems, inclusive
        var normalAnswerMin = 0; // minimum answer to normal arithmetic problems, inclusive
        var normalAnswerMax = 19; // maximum answer to normal arithmetic problems, inclusive

        var bossDigitMin = 1000; // minimum answer to boss digit problems, inclusive
        var bossDigitMax = 9999; // maximum answer to boss digit problems, inclusive
        var normalDigitMin = 100; // minimum answer to normal digit problems, inclusive
        var normalDigitMax = 999; // maximum answer to normal digit problems, inclusive

        var accelerationCap = 20; // max value of random acceleration applied with each problem solved
        var initSpawnMax = 500; // starting spawn interval. Higher = slower start.

        var gameInterval;

        var matheroids = [];
        var problems = [];
        var playing = true;
        var score = 0;

        var spawnIntervalMax = initSpawnMax;
        var spawnTimer = 200;
        var bossCountdown = 10 - gamemode;

        // laserFrames is how many frames the laser will be onscreen for
        var laserFrames = 10; // this MUST be > 0
        var laserCountdown = -1;
        var laserTargetX;
        var laserTargetY;
        var laserReflects = false;
        var laserColor = "#FF0033";

        var damageFrames = 25;
        var damageCountdown = -1;
        var damageX;
        var damageY;
        var damageColor = "#FF0000";

        var explosions = [];

        var wrongTotal = 0;
        var strikes = 3;

        // for the digit identification mode, a prompt is necessary
        if(gamemode == 0){
            document.getElementById("prompt").innerHTML = "Enter the digit in the stated place";
        }

        // 0 = digit identification
        // 1 = addition only.
        // 2 = subtraction and addition.

        //Generates a random addition problem between min and max. Returns the problem in string form.
        function newMathProblem(min, max) { 
            var sign;

            if(gamemode == 1){
                sign = 1;
            }else if(gamemode == 2){
                sign = 2
            } else {
                sign = getRandomNumber(1, 3);
            }

            var num1 = getRandomNumber(min, max);
            var num2 = getRandomNumber(min, max);

            var problem;

            if(sign == 1){
                problem = num1 + "+" + num2;
            }else if(sign == 2){
                if(num1 > num2){
                    problem = num1 + "-" + num2;
                }
                else{
                    problem = num2 + "-" + num1;
                }
            }

            return problem;
        }
            
        //Solves an addition problem in the form "a+b=". Returns the answer.
        function solveMathProblem(problem){
            var operatorLocation;
            var operator;

            for(var i = 0; i < problem.length; i++){
                if(problem.charAt(i) == "+"){
                    operatorLocation = i;
                    operator = "+";
                } else if (problem.charAt(i) == "-"){
                    operatorLocation = i;
                    operator = "-";
                }
            }

            var num1 = parseInt(problem.slice(0,operatorLocation));
            var num2 = parseInt(problem.slice(operatorLocation+1,problem.length));

            if(operator == "+"){
                var ans = num1+num2;
                // console.log(num1+" + "+num2+"="+ans);
            } else if (operator == "-"){
                var ans = num1-num2;
                // console.log(num1+" - "+num2+"="+ans);
            }

            return ans;
        }

        // Returns a random integer number x: [min,max)
        function getRandomNumber(min, max){
            var num = Math.random() * (+max - +min) + +min; 
            num = Math.floor(num)

            return num;
        }

        function startGame() {
            addProblem();
            document.body.insertBefore(myCanvas, document.body.childNodes[0]);
            gameInterval = setInterval(updateGameArea, 15);
        }

        function resetGame(){
            matheroids = [];
            problems = [];
            playing = true;
            score = 0;

            spawnIntervalMax = initSpawnMax;
            spawnTimer = 200;
            bossCountdown = 10 - gamemode;

            laserFrames = 10; // this MUST be > 0
            laserCountdown = -1;
            laserTargetX = 0;
            laserTargetY = 0;
            laserReflects = false;
            laserColor = "#FF0033";

            damageFrames = 25;
            damageCountdown = -1;
            damageX = 0;
            damageY = 0;
            damageColor = "#FF0000";

            explosions = [];

            wrongTotal= 0;

            answerForm.userAnswer.focus();
            
            clearInterval(gameInterval);

            startGame();

            updateScore();
        }

        //Adds a problem to the array of problems.
        function addProblem() {
            var xSpawn;
            if(gamemode != 0){
                xSpawn = getRandomNumber(50,300); 
            }else{
                xSpawn = getRandomNumber(50,250);
            }

            var myGamePiece = new matheroid(xSpawn, -50);
            matheroids.push(myGamePiece);
            bossCountdown--;
        }

        // a class for digit id problems
        function digitProblem(numberInput){
            this.num = numberInput;
            this.str = "error: dig_str_uninit";
        }

        // an object represeting an entire matheroid
        function matheroid(xCoord, yCoord){
            this.object = new object(xCoord, yCoord);
            this.x = xCoord;
            this.y = yCoord;
            this.objColor = object.color;
            this.image = astImage1;

            this.update = function(){
                if(this.object.boss){
                    ctx.drawImage(this.image, this.x - 50, this.y - 50, 100, 100);
                }else{
                    if(gamemode != 0){
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
            if(gamemode != 0){
                this.problem = newMathProblem(normalAnswerMin, (normalAnswerMax + 1) / 2);
            }else{
                this.problem = new digitProblem(getRandomNumber(normalDigitMin, normalDigitMax + 1));
            }

            this.speed = 1;
            this.color = 'White';
            this.font = "bold 18px Courier New";
            this.boss = false;

            //Checks if it's time to create a boss problem.
            if(bossCountdown <= 0){
                if(gamemode != 0){
                    this.problem = newMathProblem(bossAnswerMin / 2, (bossAnswerMax + 1) / 2);
                    this.font = "bold 24px Courier New";
                    this.boss = true;
                    bossCountdown = getRandomNumber(5,15) - gamemode;
                }
                else{
                    this.problem = new digitProblem(getRandomNumber(bossDigitMin, bossDigitMax + 1));
                    this.font = "bold 24px Courier New";
                    this.boss = true;
                    bossCountdown = getRandomNumber(5,15) - gamemode;
                }
            }

            // set answer for an arithmetic problem
            if(gamemode != 0){
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

                if(gamemode != 0){
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
                this.hitBottom();    
            }

            //Checks to see if the equation has hit the bottom.
            this.hitBottom = function() {
                var bottom = myCanvas.height - 18;
                if (this.y > bottom) {
                    youLose();
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

        // runs every tick (15ms)
        function updateGameArea() {
            if (playing == true){

                ctx.clearRect(0, 0, myCanvas.width, myCanvas.height);
                spawnTimer--;

                // this needs to count down so the 0th problem is drawn last, and is thus on top
                for(var i = matheroids.length - 1; i >= 0; i--){
                    matheroids[i].newPosition();
                    matheroids[i].update();
                }

                if(spawnTimer <= 0){
                    spawnTimer = getRandomNumber(15,spawnIntervalMax);
                    addProblem();
                }

                if(laserCountdown >= 0){
                    updateLaser();
                    laserCountdown--;
                }

                if(damageCountdown >= 0 && laserReflects){
                    updateDamage();
                    damageCountdown--;
                }

                if(explosions.length > 0){
                    updateExplosions();
                }

                if(matheroids.length > 0){
                    matheroids[0].image = astImage2;
                    matheroids[0].updateColor('Aqua');
                }
            }
            else{
                ctx.clearRect(0, 0, myCanvas.width, myCanvas.height);

                // no new position since the game is not playing
                for(var i = matheroids.length - 1; i >= 0; i--){
                    matheroids[i].update();
                }

                // the laser and damage need to continue updating if the player loses because of striking out
                if(laserCountdown >= 0){
                    updateLaser();
                    laserCountdown--;
                }

                if(damageCountdown >= 0 && laserReflects){
                    updateDamage();
                    damageCountdown--;
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
            document.getElementById("score").innerHTML = "Game Over.\n Your final score was: " + score;
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
                            score++; score++;
                        }

                        updateScore();

                        // apply a random, limited acceleration to the spawn rate
                        if (spawnIntervalMax >= 80){
                            spawnIntervalMax = spawnIntervalMax - getRandomNumber(0,accelerationCap);
                        }

                        return true;
                    }
                    else{
                        wrongTotal++;

                        if(wrongTotal >= strikes){
                            youLose();
                        }

                        return false;
                    }
                }
            }
        }

        function updateDamage(){
            ctx.globalAlpha = damageCountdown / damageFrames;
            ctx.lineWidth = 30;
            ctx.strokeStyle = "#FF0000"; // this should always be red, regardless of the laser color
            ctx.moveTo(damageX, damageY);
            ctx.beginPath();
            var tmpRadius = 4 * (damageFrames - damageCountdown);
            ctx.arc(damageX, damageY, tmpRadius, 0, Math.PI, true);
            ctx.stroke();

            ctx.globalAlpha = 1;
        }

        function explosion(xCoord, yCoord, boss, dead){
            this.x = xCoord;
            this.y = yCoord;
            this.frames = 86;
            this.countdown = 86;
            this.isBoss = boss;
            if(dead){
                this.countdown = -1;
            }
        }

        function updateExplosions(){
            for(var i = 0; i < explosions.length; i++){
                var tmpIdx = 0;
                if(!explosions[i].isBoss){
                    tmpIdx = Math.ceil((explosions[i].frames - explosions[i].countdown) / 2);
                    
                    ctx.drawImage(explosionFrames[tmpIdx], explosions[i].x - 320, explosions[i].y - 180, 640, 360);

                    // extra countdown tick because the small explosions are faster
                    explosions[i].countdown--;
                }
                else{
                    // boss explosions are twice as big and take twice as long to happen
                    tmpIdx = Math.ceil((explosions[i].frames - explosions[i].countdown) / 2);

                    ctx.drawImage(explosionFrames[tmpIdx], explosions[i].x - 640, explosions[i].y - 320, 1280, 720);
                }

                explosions[i].countdown--;

                if(explosions[i].countdown < 0){
                    explosions.splice(i,1);
                }
            }
        }

        function updateLaser(){
            // console.log("updating laser. explosions count = " + explosions.length);

            ctx.globalAlpha = laserCountdown / laserFrames;

            ctx.beginPath();
            ctx.lineWidth = 14;
            ctx.strokeStyle = laserColor;
            ctx.moveTo(200, 600); // center of the bottom of the canvas
            ctx.lineTo(laserTargetX, laserTargetY + 15);
            // ctx.filter = 'blur(2px)'; // this makes the laser look better but can make the program lag one you get past around 60 points
            ctx.stroke();

            ctx.lineWidth = 7;
            ctx.strokeStyle = "#FFFFFF"; // always white regardless of laser color
            ctx.moveTo(200, 600); // center of the bottom of the canvas
            ctx.lineTo(laserTargetX, laserTargetY + 15);
            //ctx.filter = 'blur(0px)';
            ctx.stroke();

            if(laserReflects){
                damageX = 200 + ((laserTargetX - 200) / 1.5);
                damageY = 600;

                ctx.lineWidth = 14;
                ctx.strokeStyle = laserColor;
                ctx.moveTo(laserTargetX, laserTargetY + 15); // center of the bottom of the canvas
                ctx.lineTo(damageX, damageY);
                ctx.stroke();

                ctx.lineWidth = 7;
                ctx.strokeStyle = "#FFFFFF"; // always white regardless of laser color
                ctx.moveTo(laserTargetX, laserTargetY + 15); // center of the bottom of the canvas
                ctx.lineTo(damageX, damageY);
                ctx.stroke();
            }

            ctx.globalAlpha = 1;
        }

        function shoot(){
            if(matheroids.length < 1){
                return;
            }
            // console.log("shooting...");

            // set the target coordinates of the laser
            laserTargetX = matheroids[0].x;
            laserTargetY = matheroids[0].y;
            laserCountdown = laserFrames;

            damageCountdown = damageFrames;

            if(checkAnswer()){
                // console.log("pushing new explosion...");
                explosions.push(new explosion(laserTargetX,laserTargetY,matheroids[0].getBossStatus()));
                // console.log("new explosion pushed. explosions length = " + explosions.length);
                // console.log("explosions[0].countdown = " + explosions[0].countdown);
                // console.log("explosions[0].frames = " + explosions[0].frames);
                // console.log("explosions[0].isBoss = " + explosions[0].isBoss);
                // console.log("explosions[0].x = " + explosions[0].x);
                // console.log("explosions[0].y = " + explosions[0].y);
                laserReflects = false;
                matheroids.splice(0, 1);
            }
            else{
                laserReflects = true;
            }

            document.getElementById("userAnswer").value = "";
            // console.log("shooting complete.");
        }

    </script>

</body>
</html>
