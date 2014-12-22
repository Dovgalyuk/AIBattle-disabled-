var canvas;
var context;
var countField;
var timer;
var log;
var dirs = [-Math.PI / 2, 0, Math.PI / 2, -Math.PI];
var steps;
var it;
var block;
var tank1;
var tank2;
var energy;
var bullet;
var cellSize;
var fieldSize = 15;
var logLines = fieldSize + 4 + 2 + 1;

function startup(width, height)
{
    canvas = document.getElementById("cv");
    context = canvas.getContext("2d");
    countField = document.getElementById("cnt");    
    it = 0;

	document.getElementById('hiddenScore').style.display = 'none';
    
    block = new Image();
    block.src = "./images/Wall.png";

    tank1 = new Image();
    tank1.src = "./images/Tank1.png";

    tank2 = new Image();
    tank2.src = "./images/Tank2.png";

    flag1 = new Image();
    flag1.src = "./images/Flag1.png";

    flag2 = new Image();
    flag2.src = "./images/Flag2.png";

    energy = new Image();
    energy.src = "./images/Energy.png";

    bullet = new Image();
    bullet.src = "./images/Bullet.png";

<?php
    echo "setLog(\"" .$log."\");";
?>
    resize(width, height);
}

function resize(width, height)
{
    cellSize = Math.min(width / fieldSize, height / fieldSize);
    canvas.width = cellSize * fieldSize;
    canvas.height = cellSize * fieldSize;
    
    draw();
}

function changeScore(score)
{
	document.getElementById('scoreDiv').innerHTML = score[0] + ":" + score[1];
}

function drawTank(image, flag, tank, r, c)
{
    var sz = cellSize;
    var sz2 = sz / 2;
    context.save();
    context.translate(sz * c + sz2, sz * r + sz2);
    context.rotate(dirs[tank[1] - 1]);
    context.drawImage(image, -sz2, -sz2, sz, sz);
    context.restore();
    if (tank[3] != 0)
    {
        context.drawImage(flag, sz * c, sz * r, sz2, sz2);
    }
    context.fillStyle = "rgb(0, 255, 0)";
    context.fillRect(sz * c, sz * r, sz * tank[0] / 100, 3);
    context.fillStyle = "rgb(0, 0, 255)";
    context.fillRect(sz * c, sz * r, 3, sz * tank[2] / 5);
}

function draw()
{
    context.fillStyle = "rgb(0, 0, 0)";
    //context.clearRect(0, 0, canvas.width, canvas.height);
    context.fillRect(0, 0, canvas.width, canvas.height);

    for (var i = 0; i < fieldSize; ++i)
    {
        for (var j = 0; j < fieldSize ; ++j)
        {
            var sz = cellSize;
            var sz2 = sz / 2;
            var sym = log[it].field[i][j];
            switch (sym)
            {
                case "#":
                    context.drawImage(block, sz * j, sz * i, sz, sz);
                    break;

                case "1":
                case "a":
                    drawTank(tank1, flag2, log[it].tanks[0], i, j);
                    break;

                    case "2":
                case "b":
                    drawTank(tank1, flag2, log[it].tanks[1], i, j);
                    break;

                case "3":
                case "c":
                    drawTank(tank2, flag1, log[it].tanks[2], i, j);
                    break;

                case "4":
                case "d":
                    drawTank(tank2, flag1, log[it].tanks[3], i, j);
                    break;

                case "e":
                    context.drawImage(energy, sz * j, sz * i, sz, sz);
                    break;
                    
                case "<":
                    context.save();
                    context.translate(sz * j + sz2, sz * i + sz2);
                    context.rotate(dirs[0]);
                    context.drawImage(bullet, -sz2, -sz2, sz, sz);
                    context.restore();
                    break;
                    
                case ">":
                    context.save();
                    context.translate(sz * j + sz2, sz * i + sz2);
                    context.rotate(dirs[2]);
                    context.drawImage(bullet, -sz2, -sz2, sz, sz);
                    context.restore();
                    break;
                    
                case "^":
                    context.drawImage(bullet, sz * j, sz * i, sz, sz);
                    break;
                    
                case "v":
                    context.save();
                    context.translate(sz * j + sz2, sz * i + sz2);
                    context.rotate(dirs[3]);
                    context.drawImage(bullet, -sz2, -sz2, sz, sz);
                    context.restore();
                    break;
                case 'A':
                    context.drawImage(flag1, sz * j, sz * i, sz, sz);
                    break;
                case 'B':
                    context.drawImage(flag2, sz * j, sz * i, sz, sz);
                    break;
            }
        }
    }

    changeScore(log[it].score);
}

function setLog(logStrings)
{
    logStrings = logStrings.replace(/\n\n/g, "\n");
    var rawLog = logStrings.split("\n");
	while (rawLog.length % logLines != 0)
		rawLog.pop();
    steps = rawLog.length / logLines;

    log = new Array();
    for (var z = 0 ; z < steps ; ++z)
    {
        var field = new Object;
        for (var i = 0 ; i < fieldSize ; ++i) 
            field[i] = rawLog[logLines * z + i];
        var tanks = new Object;
        for (var i = 0 ; i < 4 ; ++i)
        {
            tanks[i] = rawLog[logLines * z + fieldSize + i].split(" ");
            for (var j = 0 ; j < 4 ; ++j)
                tanks[i][j] = parseInt(tanks[i][j]);
        }
        var flags = new Object;
        for (var i = 0 ; i < 2 ; ++i) 
            flags[i] = rawLog[logLines * z + fieldSize + 4 + i].split(" ");
        var score = rawLog[logLines * z + fieldSize + 4 + 2].split(" ");
        log.push({'field' : field, 'tanks' : tanks, 'flags' : flags, 'score' : score});
    }
    //console.log(log);
}

function play_start()
{
    clearInterval(timer);
    timer = setInterval(function()
    {
        draw();
        if (it == steps - 1)
        {
            clearInterval(timer);
        }
        else
        {
            ++it;
        }
    }, 250);
}

function play_stop()
{
    clearInterval(timer);
}

function play_reset()
{
    clearInterval(timer);
    it = 0;
    draw();
}


function stepPrev()
{
    clearInterval(timer);

    if (it > 0)
    {
        --it;
        draw();
    }
}

function stepNext()
{
    clearInterval(timer);

    if (it < steps - 1)
    {
        ++it;
        draw();
    }
    
	if (it == steps - 1)
		document.getElementById('hiddenScore').style.display = 'block';
}
