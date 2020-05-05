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
    <div id="stage">
        <canvas id="top-layer" width="400" height="600"></canvas>
        <canvas id="mid-layer" width="400" height="600"></canvas>
    </div>
    <style>
        #stage{
            width: 400px;
            height: 600px;
            position: relative;
            border: 1px solid #a9a9a9;
            margin: auto;
            background-image: url("assets/earth_v1.jpg");
        }
        canvas {
            position: absolute;
        }
        #top-layer { z-index: 3; }
        #mid-layer { z-index: 2; }
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

<body>

    <form id="answerForm" onsubmit="shoot(); return false;" autocomplete="off" style="visibility: visible; color:white;">
        <input type="text" name="answer"  id="userAnswer" placeholder="answer" maxlength="2" size="4" autofocus/>
    </form>

    <br />

    <form action="./mainMenu.php" >
        <button type="submit">Main menu</button>
    </form>

    <script src="howler/dist/howler.js"></script>
    <script type="text/javascript">
        const topCanvas = document.getElementById("top-layer");
        const midCanvas = document.getElementById("mid-layer");
        const topCtx = topCanvas.getContext("2d");
        const midCtx = midCanvas.getContext("2d");

        drawLoadingScreen();

        var gameIsLoaded = false;

        var astImage1 = new Image();
        astImage1.src = 'assets/asteroid_2_v2_default.png';
        var astImage2 = new Image();
        astImage2.src = 'assets/asteroid_2_v2_red.png';
        var musicOnIcon = new Image();
        musicOnIcon.src = 'assets/music_on_icon.png';
        var musicOffIcon = new Image();
        musicOffIcon.src = 'assets/music_off_icon.png';
        var sfxOnIcon = new Image();
        sfxOnIcon.src = 'assets/sfx_on_icon.png';
        var sfxOffIcon = new Image();
        sfxOffIcon.src = 'assets/sfx_off_icon.png';

        var clickSound = new Audio("assets/click_v1.mp3");
        var loseSound = new Audio("assets/youLose_v1.mp3");

        var music = new Audio("assets/music_full_v1.mp3");

        music.addEventListener("canplaythrough", function(){
            gameIsLoaded = true;
            music.volume = 0.4;
            music.loop = true;
            clearLoadingScreen();
            drawPlayButton();
        });

        var askingToPlayGameForTheFirstTime = false;

        var bossExplodeSounds = [];
        var normalExplodeSounds = [];
        var laserSounds = [];
        var damageSounds = [];

        var polyphony = 8;

        // multiple audio objects to support polyphony
        for(var i = 0; i < polyphony; i++){
            bossExplodeSounds.push(new Audio("assets/explosion_boss_v1.mp3"));
            normalExplodeSounds.push(new Audio("assets/explosion_normal_v1.mp3"));
            laserSounds.push(new Audio("assets/laser_sfx_v1.mp3"));
            damageSounds.push(new Audio("assets/damage_v1.mp3"));
        }

        bossExplodeSoundIdx = 0;
        normalExplodeSoundIdx = 0;
        laserSoundIdx = 0;
        damageSoundIdx = 0;

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

        var accelerationCap = 15; // max value of random acceleration applied with each problem solved
        var initSpawnMax = 400; // starting spawn interval. Higher = slower start.

        var gameInterval;

        var matheroids = [];
        var problems = [];
        var playing = false;
        var score = 0;

        var spawnIntervalMax = initSpawnMax;
        var spawnTimer = 200;
        var bossCountdown = 10 - gamemode;

        // laserFrames is how many frames the laser will be onscreen for
        var laserFrames = 10; // this MUST be > 0
        var laserCountdown = -1;
        var laserTargetX = 200;
        var laserTargetY = 0;
        var laserReflects = false;
        var laserColor = "#FF0033";

        var damageFrames = 25;
        var damageCountdown = -1;
        var damageX;
        var damageY;
        var damageColor = "#FF0000";

        var explosions = [];

        var wrongTotal = 0;
        var strikes = 5;

        // this is used to ensure the focus is properly put on the answerForm when resetting by clicking Play Again in the canvas (otherwise the canvas gets the focus because you clicked on it)
        var focusCountdown = 1;

        // gamemodes are as follows: 0: digit identification. 1: additon. 2: subtraction. 3: addition and subtraction

        var mouse = { x: undefined, y: undefined}

        window.addEventListener('mousemove', 
            function(event){
                // convert client coordinates to canvas coordinates
                // this depends on the canvas being centered
                mouse.x = event.pageX - (window.innerWidth / 2) + 200 + 8;
                mouse.y = event.pageY - 8;

                if(askingToPlayGameForTheFirstTime && mouse.x > 100 && mouse.x < 300 && mouse.y > 250 && mouse.y < 350){
                    highlightPlayButton();
                }
                else if(askingToPlayGameForTheFirstTime){
                    unhighlightPlayButton();
                }
        });

        window.addEventListener('mousedown',
            function(event){
                // detect pressing play
                if(askingToPlayGameForTheFirstTime && mouse.x > 100 && mouse.x < 300 && mouse.y > 250 && mouse.y < 350){
                    clickSound.play();
                    music.play();
                    clearPlayButton();
                    askingToPlayGameForTheFirstTime = false;
                    startGame();
                }

                // detect pressing play again
                if(!askingToPlayGameForTheFirstTime && !playing && mouse.x > 80 && mouse.x < 320 && mouse.y > 350 && mouse.y < 400){
                    clickSound.play();
                    resetGame();
                }

                // detect pressing music toggle
                if(gameIsLoaded && mouse.x > topCanvas.width - 110 && mouse.x < topCanvas.width - 55 && mouse.y < 60){
                    toggleMusic();
                    updateSoundControls();
                    focusCountdown = 1;
                }

                // detect pressing sfx toggle
                if(gameIsLoaded && mouse.x > topCanvas.width - 55 && mouse.x < topCanvas.width && mouse.y < 60){
                    toggleSfx();
                    updateSoundControls();
                    focusCountdown = 1;
                }
        });

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

        function drawLoadingScreen(){
            topCtx.fillStyle = "#00FF00";
            topCtx.font = "bold 36px Courier New";
            topCtx.textAlign = 'center';
            topCtx.textBaseline = 'middle';
            topCtx.fillText("loading...",200,280);
        }

        function clearLoadingScreen(){
            topCtx.clearRect(0,0,topCanvas.width,topCanvas.height);
        }

        function drawPlayButton(){
            askingToPlayGameForTheFirstTime = true;

            topCtx.fillStyle = "#FF0066";
            topCtx.font = "bold 48px Courier New";
            topCtx.textAlign = 'center';
            topCtx.textBaseline = 'middle';
            topCtx.fillText("Play",200,300);

            topCtx.strokeStyle = "#FF0066";
            topCtx.lineWidth = 5;
            topCtx.rect(100, 250, 200, 100);
            topCtx.stroke();
        }

        function clearPlayButton(){
            topCtx.clearRect(0,0,topCanvas.width,topCanvas.height);
        }

        function highlightPlayButton(){
            midCtx.beginPath();
            midCtx.lineWidth = 1;
            midCtx.fillStyle = "#FFFFCC";
            midCtx.rect(100, 250, 200, 100);
            midCtx.fill();
        }

        function unhighlightPlayButton(){
            midCtx.clearRect(0,0,midCanvas.width,midCanvas.height);
        }

        function startGame() {
            playing = true;
            addProblem();
            updateGameArea();
            updateTop();
            gameInterval = setInterval(updateGameArea, 15);
        }

        function resetGame(){
            matheroids = [];
            problems = [];
            playing = true; // true here but not above because the game is already loaded if you're resetting
            score = 0;

            spawnIntervalMax = initSpawnMax;
            spawnTimer = 200;
            bossCountdown = 10 - gamemode;

            laserFrames = 10; // this MUST be > 0
            laserCountdown = -1;
            laserTargetX = 200;
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

            focusCountdown = 1;
            
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
                xSpawn = getRandomNumber(100,250);
            }

            var myGamePiece = new matheroid(xSpawn, -50);
            myGamePiece.renderMatheroid();
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
            this.render = document.createElement('canvas');
            var renCtx = this.render.getContext('2d');

            this.renderMatheroid = function(){
                renCtx.clearRect(0, 0, this.render.width, this.render.height);

                // normal arithmetic
                if(gamemode != 0 && !this.object.getBossStatus()){
                    this.render.width = 50;
                    this.render.height = 50;

                    //draw asteroid
                    renCtx.drawImage(this.image,0,0,50,50);

                    //draw arithmetic problem
                    renCtx.fillStyle = this.object.color;
                    renCtx.font = this.object.font;
                    renCtx.textAlign = 'center';
                    renCtx.textBaseline = 'middle';
                    renCtx.fillText(this.object.problem,25,25);
                    console.log("normal problem rendered: " + this.object.problem);
                }
                // boss arithmetic
                else if(gamemode != 0 && this.object.getBossStatus()){
                    this.render.width = 100;
                    this.render.height = 100;

                    //draw asteroid
                    renCtx.drawImage(this.image,0,0,100,100);

                    //draw arithmetic problem
                    renCtx.fillStyle = this.object.color;
                    renCtx.font = this.object.font;
                    renCtx.textAlign = 'center';
                    renCtx.textBaseline = 'middle';
                    renCtx.fillText(this.object.problem,50,50);
                    console.log("boss problem rendered: " + this.object.problem);
                }
                else{
                    // normal digit ID
                    if(!this.object.getBossStatus()){
                        this.render.width = 100;
                        this.render.height = 60;

                        //draw asteroid
                        renCtx.drawImage(this.image,20,0,60,60);

                        //draw text
                        renCtx.fillStyle = this.object.color;
                        renCtx.font = this.object.font;
                        renCtx.textAlign = 'center';
                        renCtx.textBaseline = 'middle';
                        renCtx.fillText(this.object.problem.num, 50, 30 - 8);
                        renCtx.fillText(this.object.problem.str, 50, 30 + 10);
                    }
                    // boss digit ID
                    else{
                        this.render.width = 200;
                        this.render.height = 100;

                        //draw asteroid
                        renCtx.drawImage(this.image,50,0,100,100);

                        //draw text
                        renCtx.fillStyle = this.object.color;
                        renCtx.font = this.object.font;
                        renCtx.textAlign = 'center';
                        renCtx.textBaseline = 'middle';
                        renCtx.fillText(this.object.problem.num, 100, 60 - 12);
                        renCtx.fillText(this.object.problem.str, 100, 60 + 10);
                    }
                }
            }

            this.update = function(){
                if(this.object.boss){
                    if(gamemode != 0){
                        midCtx.drawImage(this.render, this.x - 50, this.y - 50);
                    }
                    else{
                        midCtx.drawImage(this.render, this.x - 100, this.y - 50);
                    }
                }
                else{
                    if(gamemode != 0){
                        midCtx.drawImage(this.render, this.x - 25, this.y - 25);
                    }
                    else{
                        midCtx.drawImage(this.render, this.x - 50, this.y - 30);
                    }
                }
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
                    bossCountdown = getRandomNumber(5,10);
                }
                else{
                    this.problem = new digitProblem(getRandomNumber(bossDigitMin, bossDigitMax + 1));
                    this.font = "bold 24px Courier New";
                    this.boss = true;
                    bossCountdown = getRandomNumber(5,10);
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

            //Finds the new location for an equation
            this.newPosition = function() {
                this.y += this.speed;
                this.hitBottom();    
            }

            //Checks to see if the equation has hit the bottom.
            this.hitBottom = function() {
                if (this.y == midCanvas.height - 40) {
                    youLose();
                    updateTop();
                    drawPlayAgainButton();
                    bossExplodeSounds[bossExplodeSoundIdx].play();
                    if(bossExplodeSoundIdx >= polyphony - 1){
                        bossExplodeSoundIdx = 0;
                    }else{
                        bossExplodeSoundIdx++;
                    }
                    explosions.push(new explosion(this.x,this.y,true));
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

        // update the top canvas, including the cannon and score
        function updateTop(){
            topCtx.clearRect(0, 0, topCanvas.width, topCanvas.height);
            updateScore();
            updateCannon();
            updateSoundControls();
        }

        // runs every tick (15ms)
        function updateGameArea() {
            if(focusCountdown >= 0){
                if(focusCountdown == 0){
                    answerForm.userAnswer.focus();
                }
                focusCountdown--;
            }

            midCtx.clearRect(0, 0, midCanvas.width, midCanvas.height);

            // this needs to count down so the 0th problem is drawn last, and is thus on top
            for(var i = matheroids.length - 1; i >= 0; i--){
                matheroids[i].newPosition();
                matheroids[i].update();
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

            if (playing){
                spawnTimer--;

                if(spawnTimer <= 0){
                    spawnTimer = getRandomNumber(15,spawnIntervalMax);
                    addProblem();
                }

                if(matheroids.length > 0){
                    matheroids[0].image = astImage2;
                    matheroids[0].updateColor('Aqua');
                    matheroids[0].renderMatheroid();
                }
            }
            else{
                if(mouse.x > 80 && mouse.x < 320 && mouse.y > 350 && mouse.y < 400){
                    highlightPlayAgainButton();
                }
            }
        }

        //Updates the printed Score
        function updateScore(){
            if(playing){
                topCtx.beginPath();
                topCtx.fillStyle = "#FF0000";
                topCtx.font = "bold 46px Impact";
                topCtx.textAlign = 'left';
                topCtx.textBaseline = 'top';
                topCtx.fillText(score,10,10);
            }
            else{
                topCtx.beginPath();
                topCtx.fillStyle = "#FF0000";
                topCtx.font = "bold 80px Impact";
                topCtx.textAlign = 'center';
                topCtx.textBaseline = 'middle';
                topCtx.fillText("Game Over", 200, 225);

                topCtx.beginPath();
                topCtx.font = "bold 40px Courier New";
                topCtx.fillText("Final score: " + score, 200, 300);
            }
        }

        function toggleMusic(){
            if(music.muted){
                music.muted = false;
            }
            else{
                music.muted = true;
            }
        }

        function toggleSfx(){
            if(loseSound.muted){
                loseSound.muted = false;
                clickSound.muted = false;
                for(var idx = 0; idx < polyphony; idx++){
                    bossExplodeSounds[idx].muted = false;
                    normalExplodeSounds[idx].muted = false;
                    damageSounds[idx].muted = false;
                    laserSounds[idx].muted = false;
                }
            }
            else{
                loseSound.muted = true;
                clickSound.muted = true;
                for(var idx = 0; idx < polyphony; idx++){
                    bossExplodeSounds[idx].muted = true;
                    normalExplodeSounds[idx].muted = true;
                    damageSounds[idx].muted = true;
                    laserSounds[idx].muted = true;
                }
            }
        }

        function updateSoundControls(){
            topCtx.clearRect(topCanvas.width - 110, 5, 110, 50);
            if(music.muted){
                topCtx.drawImage(musicOffIcon, topCanvas.width - 110, 5, 50, 50);
            }
            else{
                topCtx.drawImage(musicOnIcon, topCanvas.width - 110, 5, 50, 50);
            }
            if(loseSound.muted){
                topCtx.drawImage(sfxOffIcon, topCanvas.width - 55, 5, 50, 50);
            }
            else{
                topCtx.drawImage(sfxOnIcon, topCanvas.width - 55, 5, 50, 50);
            }
        }

        function highlightPlayAgainButton(){
            midCtx.beginPath();
            midCtx.lineWidth = 1;
            midCtx.fillStyle = "#FFFFCC";
            midCtx.rect(80, 350, 240, 50);
            midCtx.fill();
        }

        function drawPlayAgainButton(){
            topCtx.beginPath();
            topCtx.lineWidth = 5;
            topCtx.strokeStyle = "#FF0000";
            topCtx.rect(80, 350, 240, 50);
            topCtx.stroke();

            topCtx.beginPath();
            topCtx.fillStyle = "#FF0000";
            topCtx.font = "bold 36px Courier New";
            topCtx.fillText("Play Again", 200, 375);
        }

        //Tells the player they lose
        function youLose(){
            if(playing){
                loseSound.play();
            }
            playing = false;
            matheroids.splice(0,1);
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
            midCtx.globalAlpha = damageCountdown / damageFrames;
            midCtx.lineWidth = 30;
            midCtx.strokeStyle = "#FF0000"; // this should always be red, regardless of the laser color
            midCtx.moveTo(damageX, damageY);
            midCtx.beginPath();
            var tmpRadius = 4 * (damageFrames - damageCountdown);
            midCtx.arc(damageX, damageY, tmpRadius, 0, Math.PI, true);
            midCtx.stroke();

            midCtx.globalAlpha = 1;
        }

        function explosion(xCoord, yCoord, boss){
            this.x = xCoord;
            this.y = yCoord;
            this.frames = 86;
            this.countdown = 86;
            this.isBoss = boss;
            this.size = 1;
            if(this.isBoss){
                this.size = 2;
            }
        }

        function updateExplosions(){
            for(var i = 0; i < explosions.length; i++){
                var tmpIdx = 0;
                tmpIdx = Math.ceil((explosions[i].frames - explosions[i].countdown) / 2);

                var xPos = explosions[i].x - (320 * explosions[i].size);
                var yPos = explosions[i].y - (180 * explosions[i].size);
                var xScale = 640 * explosions[i].size;
                var yScale = 360 * explosions[i].size;
                
                midCtx.drawImage(explosionFrames[tmpIdx], xPos, yPos, xScale, yScale);

                if(!explosions[i].isBoss){
                    // extra countdown tick because the small explosions are faster
                    explosions[i].countdown--;
                }

                explosions[i].countdown--;

                if(explosions[i].countdown < 0){
                    explosions.splice(i,1);
                }
            }
        }

        function updateLaser(){
            // console.log("updating laser. explosions count = " + explosions.length);

            midCtx.globalAlpha = laserCountdown / laserFrames;

            midCtx.lineWidth = 10;
            midCtx.strokeStyle = laserColor;
            midCtx.beginPath();
            midCtx.moveTo(200, 600); // center of the bottom of the canvas
            midCtx.lineTo(laserTargetX, laserTargetY + 15);
            // midCtx.filter = 'blur(2px)'; // this makes the laser look better but can make the program lag one you get past around 60 points
            midCtx.stroke();

            midCtx.lineWidth = 5;
            midCtx.strokeStyle = "#FFFFFF"; // always white regardless of laser color
            midCtx.beginPath();
            midCtx.moveTo(200, 600); // center of the bottom of the canvas
            midCtx.lineTo(laserTargetX, laserTargetY + 15);
            //midCtx.filter = 'blur(0px)';
            midCtx.stroke();

            if(laserReflects){
                damageX = 200 + ((laserTargetX - 200) / 1.5);
                damageY = 600;

                midCtx.lineWidth = 10;
                midCtx.strokeStyle = laserColor;
                midCtx.moveTo(laserTargetX, laserTargetY + 15); // center of the bottom of the canvas
                midCtx.lineTo(damageX, damageY);
                midCtx.stroke();

                midCtx.lineWidth = 5;
                midCtx.strokeStyle = "#FFFFFF"; // always white regardless of laser color
                midCtx.moveTo(laserTargetX, laserTargetY + 15); // center of the bottom of the canvas
                midCtx.lineTo(damageX, damageY);
                midCtx.stroke();
            }

            midCtx.globalAlpha = 1;
        }

        function updateCannon(){
            var radius = 30;

            topCtx.beginPath();
            topCtx.lineWidth = 1;
            topCtx.fillStyle = "#444444"; // some sort of grey
            topCtx.moveTo(200, 600);
            topCtx.arc(200, 610, radius, 0, Math.PI, true);
            topCtx.fill();

            topCtx.beginPath();
            topCtx.lineWidth = 16;
            topCtx.strokeStyle = "#444444";
            topCtx.moveTo(200, 600);

            var cannonLength = 38;

            var dx = laserTargetX - 200;
            var dy = 600 - laserTargetY;

            if(dx == 0){
                topCtx.lineTo(200, 600 - cannonLength);
                topCtx.stroke();
            }
            else{
                var theta = Math.atan(dy / dx);
                //console.log("theta: " + theta);

                var endCannonX = (cannonLength * Math.cos(theta)) + 200;
                var endCannonY = (cannonLength * Math.sin(theta)) + 600;
                //console.log("endCannonY = " + endCannonY);

                //topCtx.beginPath();

                if(dx < 0){
                    topCtx.lineTo(400 - endCannonX, endCannonY);
                }
                else{
                    topCtx.lineTo(endCannonX, 1200 - endCannonY);
                }
                topCtx.stroke();
            }
        }

        function shoot(){
            if(matheroids.length < 1){
                laserTargetX = 200;
                laserTargetY = 0;
            }
            else{
                laserTargetX = matheroids[0].x;
                laserTargetY = matheroids[0].y;
            }

            laserSounds[laserSoundIdx].play();
            if(laserSoundIdx >= polyphony - 1){
                laserSoundIdx = 0;
            }
            else{
                laserSoundIdx++;
            }

            laserCountdown = laserFrames;

            damageCountdown = damageFrames;

            if(checkAnswer()){
                explosions.push(new explosion(laserTargetX,laserTargetY,matheroids[0].getBossStatus()));
                laserReflects = false;

                if(matheroids[0].getBossStatus()){
                    bossExplodeSounds[bossExplodeSoundIdx].play();
                    if(bossExplodeSoundIdx >= polyphony - 1){
                        bossExplodeSoundIdx = 0;
                    }
                    else{
                        bossExplodeSoundIdx++;
                    }
                }
                else{
                    normalExplodeSounds[normalExplodeSoundIdx].play();
                    if(normalExplodeSoundIdx >= polyphony - 1){
                        normalExplodeSoundIdx = 0;
                    }
                    else{
                        normalExplodeSoundIdx++;
                    }
                }

                matheroids.splice(0, 1);
            }
            else{
                laserReflects = true;

                damageSounds[damageSoundIdx].play();
                if(damageSoundIdx >= polyphony - 1){
                    damageSoundIdx = 0;
                }
                else{
                    damageSoundIdx++;
                }
            }

            updateTop();

            document.getElementById("userAnswer").value = "";
            // console.log("shooting complete.");
        }

    </script>

</body>
</html>
