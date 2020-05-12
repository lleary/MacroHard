<?php session_start();
  if (!isset($_SESSION['user'])){
      header("location: welcome.php");
      die();
   }

    //include 'update_highscore.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Matheroids</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" type="text/css"href="stylesheet.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
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
            padding: 0;
            margin: auto;
            display: block;
        }
        #top-layer { z-index: 5; }
        #mid-layer { z-index: 4; }
        body{
            background-color: #44444B;
            margin: 0px;
        }
        form {
            text-align: center;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>

<body>
    <div id="main" style="position: absolute; left:0px; top:0px"></div>

    <form id="answerForm" onsubmit="shoot(); return false;" autocomplete="off" style="visibility: visible; color:white;">
        <input type="text" name="answer"  id="userAnswer" placeholder="answer" maxlength="2" size="4" autofocus/>
    </form>

    <br />

    <form action="./mainMenu.php" >
        <button type="submit">Main menu</button>
    </form>

    <script type="text/javascript">
        const topCanvas = document.getElementById("top-layer");
        const midCanvas = document.getElementById("mid-layer");
        const topCtx = topCanvas.getContext("2d");
        const midCtx = midCanvas.getContext("2d");

        drawLoadingScreen();

        var gameIsLoaded = false;

        var greyAsteroidSrc = "url(assets/asteroid_square_grey_v1.png)";
        var redAsteroidSrc = "url(assets/asteroid_square_red_v2.png)";

        var fullHeart = new Image();
        fullHeart.src = 'assets/minecraft_heart_full_v1.png';
        var emptyHeart = new Image();
        emptyHeart.src = 'assets/minecraft_heart_empty_v1.png';
        
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

        <?php include 'readClassData.php'; ?>
        var bossAnswerMin = <?php echo $bossAnswerMin ?>; 
        var bossAnswerMax = <?php echo $bossAnswerMax ?>;
        var normalAnswerMin = <?php echo $normalAnswerMin ?>;
        var normalAnswerMax = <?php echo $normalAnswerMax ?>;

        console.log("Game Setting bossAnswerMin: "+bossAnswerMin);
        console.log("Game Setting bossAnswerMax: "+bossAnswerMax);
        console.log("Game Setting normalAnswerMin: "+normalAnswerMin);
        console.log("Game Setting normalAnswerMax: "+normalAnswerMax);

        var gameInterval;

        var matheroids = [];
        var playing = false;
        var score = 0;

        var spawnIntervalMax = initSpawnMax;
        var spawnTimer = 200;
        var bossCountdown = 10;

        // laserFrames is how many frames the laser will be onscreen for
        const laserFrames = 12; // number of frames in the laser's fade
        const laserFramesBrightNum = 3; // number of frames before the laser starts to fade
        var laserCountdown = -1;
        var laserTargetX = 200;
        var laserTargetY = 0;
        var laserReflects = false;
        // default red laser for digit ID
        var laserColor = "#FF0000";
        if(gamemode == 1){
            // yellow laser for addition
            laserColor = "#FFFF00"
        }else if(gamemode == 2){
            // green laser for subtraction
            laserColor = "#00FF00";
        }else if(gamemode == 3){
            // fuschia laser for addition+subtraction
            laserColor = "#FF00FF";
        }

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
            } else if (operator == "-"){
                var ans = num1-num2;
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
            gameInterval = setInterval(updateGameArea, 1000/60);
        }

        function resetGame(){
            clearAllMatheroids();
            playing = true; // true here but not above because the game is already loaded if you're resetting
            score = 0;

            spawnIntervalMax = initSpawnMax;
            spawnTimer = 200;
            bossCountdown = 10;

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
                xSpawn = getRandomNumber(50,350);
            }else{
                xSpawn = getRandomNumber(100,300);
            }

            var myGamePiece = new matheroid(xSpawn);
            //myGamePiece.renderMatheroid();
            matheroids.push(myGamePiece);
            bossCountdown--;
        }

        // a class for digit id problems
        function digitProblem(numberInput){
            this.num = numberInput;
            this.str = "error: dig_str_uninit";
        }

        function removeMatheroid(index){
            matheroids[index].div.remove();
            matheroids.splice(index,1);
        }

        function clearAllMatheroids(){
            var initialLength = matheroids.length;
            for(var i = 0; i < initialLength; i++){
                removeMatheroid(0);
            }
        }

        // an object represeting an entire matheroid
        function matheroid(xCoord){
            this.x = xCoord;
            this.object = new object(xCoord);
            this.div;
            this.width;
            this.height;

            // make a div which will be animated with css
            this.div = document.createElement("div");
            // set the size
            if(this.object.boss){
                if(gamemode == 0){
                    this.div.style.width = 120 + "px";
                    this.div.style.height = 120 + "px";
                    // set text
                    this.div.innerHTML = "<p style=\"text-align:center;\">" + this.object.problem.num + "<br/>" + this.object.problem.str + "</p>";
                }
                else{
                    this.div.style.width = this.div.style.height = 120 + "px";
                    // set text
                    this.div.innerHTML = "" + this.object.problem;
                }
                this.div.style.color = "white";
                this.div.style.fontSize = "28px";
                this.div.style.fontFamily = "Courier New";
                this.div.style.fontWeight = "bold";
            }
            else{
                if(gamemode == 0){
                    this.div.style.width = 70 + "px";
                    this.div.style.height = 70 + "px";
                    // set text
                    this.div.innerHTML = "<p style=\"text-align:center;\">" + this.object.problem.num + "<br/>" + this.object.problem.str + "</p>";
                }
                else{
                    this.div.style.width = this.div.style.height = 60 + "px";
                    // set text
                    this.div.innerHTML = "" + this.object.problem;
                }
                this.div.style.color = "white";
                this.div.style.fontSize = "18px";
                this.div.style.fontFamily = "Courier New";
                this.div.style.fontWeight = "bold";
            }
            // // center text vertically
            // this.div.style.display = "flex";
            // this.div.style.justifyContent = "center";
            // this.div.style.alignItems = "center";

            // set width and height in more accessible terms
            this.width = parseInt(this.div.style.width.slice(0,-2));
            this.height = parseInt(this.div.style.height.slice(0,-2));

            // background image
            this.div.style.backgroundImage = greyAsteroidSrc;
            this.div.style.backgroundSize = "cover";
            // append div to main
            document.getElementById("main").appendChild(this.div);
            // position style
            this.div.style.position = "absolute";
            // position value
            this.div.style.left = Math.floor(((window.innerWidth - topCanvas.width) / 2) + this.x - (this.width / 2)) + "px";
            // set class
            this.div.setAttribute("class", "asteroid");
            // set what to do if the asteroid hits the bottom (finishes its animation)
            this.div.addEventListener("animationend", function(){
                if(playing){
                    youLose();
                }
                bossExplodeSounds[bossExplodeSoundIdx].play();
                if(bossExplodeSoundIdx >= polyphony - 1){
                    bossExplodeSoundIdx = 0;
                }else{
                    bossExplodeSoundIdx++;
                }
                explosions.push(new explosion(matheroids[0].x, matheroids[0].y(), true));
                removeMatheroid(0);
            });

            this.y = function(){
                return parseInt(this.div.getBoundingClientRect().top + window.scrollY + (this.width / 2));
            };

            //Returns an integer which is the answer to the problem
            this.getAnswer = function(){
                return this.object.answer;
            }

            this.getProblem = function(){
                return this.object.problem;
            }

            //Updates the color of an equation
            this.updateColor = function(newColor){
                this.div.style.color = newColor;
            }

            //Returns whether or not the problem is a boss.
            this.getBossStatus = function(){
                return this.object.boss;
            }
        }

        // an object representing each problem
        function object(x) {
            this.x = x;
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
            updateHealth();
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
                    matheroids[0].div.style.backgroundImage = redAsteroidSrc;
                    matheroids[0].updateColor('Aqua');
                    // put the current asteroid on top
                    matheroids[0].div.style.zIndex = "3";
                    // put the asteroid on deck above the ones before it
                    if(matheroids.length > 1){
                        matheroids[1].div.style.zIndex = "2";
                    }
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
            // draw the score
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

            // update the database
            var correctGamemode = parseInt(gamemode) + 1;
            var scoreString = ""+score;
            var gamemodeString = ""+correctGamemode;

            jQuery.ajax({
                type: "POST",
                url: 'update_highscore.php',
                dataType: 'json',
                data: {functionname: 'updateFileScore', arguments: [gamemodeString, scoreString]},

                success: function (obj, textstatus) {
                              if( !('error' in obj) ) {
                                  yourVariable = obj.result;
                              }
                              else {
                                  console.log(obj.error);
                              }
                        }
            });
        }

        function updateHealth(){
            for(var i = 0; i < strikes; i++){
                if(playing && i < strikes - wrongTotal){
                    topCtx.drawImage(fullHeart, (i * 18), 580, 20, 20);
                }
                else{
                    topCtx.drawImage(emptyHeart, (i * 18), 580, 20, 20);
                }
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
            loseSound.play();
            playing = false;
            updateTop();
            drawPlayAgainButton();
            
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
            if(laserCountdown > laserFrames){
                midCtx.globalAlpha = 1;
            }
            else{
                midCtx.globalAlpha = laserCountdown / laserFrames;
            }

            var laserWidth = 10; // must be even number

            midCtx.lineWidth = laserWidth;
            midCtx.strokeStyle = laserColor;
            midCtx.beginPath();
            midCtx.moveTo(200, 600); // center of the bottom of the canvas
            midCtx.lineTo(laserTargetX, laserTargetY + 15);
            midCtx.stroke();

            midCtx.lineWidth = (laserWidth / 2) + 1;
            midCtx.strokeStyle = "#FFFFFF"; // always white regardless of laser color
            midCtx.beginPath();
            midCtx.moveTo(200, 600); // center of the bottom of the canvas
            midCtx.lineTo(laserTargetX, laserTargetY + 15);
            midCtx.stroke();

            if(laserReflects){
                damageX = 200 + ((laserTargetX - 200) / 1.5);
                damageY = 600;

                midCtx.lineWidth = laserWidth;
                midCtx.strokeStyle = laserColor;
                midCtx.moveTo(laserTargetX, laserTargetY + 15); // center of the bottom of the canvas
                midCtx.lineTo(damageX, damageY);
                midCtx.stroke();

                midCtx.lineWidth = (laserWidth / 2) + 1;
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

                var endCannonX = (cannonLength * Math.cos(theta)) + 200;
                var endCannonY = (cannonLength * Math.sin(theta)) + 600;

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
                laserTargetY = matheroids[0].y();
            }

            laserSounds[laserSoundIdx].play();
            if(laserSoundIdx >= polyphony - 1){
                laserSoundIdx = 0;
            }
            else{
                laserSoundIdx++;
            }

            laserCountdown = laserFrames + laserFramesBrightNum;

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

                removeMatheroid(0);
            }
            else{
                if(wrongTotal >= strikes){
                    youLose();
                }

                laserReflects = true;

                damageSounds[damageSoundIdx].play();
                if(damageSoundIdx >= polyphony - 1){
                    damageSoundIdx = 0;
                }
                else{
                    damageSoundIdx++;
                }
            }

            if(playing){
                updateTop();
            }

            document.getElementById("userAnswer").value = "";
        }

    </script>

</body>
</html>
