var canvas;
var context;

var fieldSize = 10;
var logLines = fieldSize + 2;

var steps;
var log;

var timer;

// images
var tower;
var gold;
var player1;
var player2;
var cannonFirst;
var cannonSecond;
var cannonThird;

function startup(width, height)
{
	canvas = document.getElementById("cv");
	context = canvas.getContext("2d");
	context.lineWidth = 2;
	it = 0;
	
	document.getElementById('hiddenScore').style.display = 'none';
	
	tower = new Image();
    tower.src = "./images/Tower.png";
	
	gold = new Image();
	gold.src = "./images/Gold.png";
	
	player1 = new Image();
	player1.src = "./images/PlayerTW1.png";
	
	player2 = new Image();
	player2.src = "./images/PlayerTW2.png";
	
	cannonFirst = new Image();
	cannonFirst.src = "./images/cannonFirst.png";
	
	cannonSecond = new Image();
	cannonSecond.src = "./images/cannonSecond.png";
	
	cannonThird = new Image();
	cannonThird.src = "./images/cannonThird.png";
	
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
	
	widthStep = canvas.width / 10.0;
	heightStep = canvas.height / 10.0;
	
	draw();
}

function isCorrectPoint(i, j)
{
	return i >=0 && i < fieldSize && j >= 0 && j < fieldSize;
}

function drawCannonHealth(i, j, health, maxHealth)
{
	context.fillStyle = "rgb(0, 255, 0)";
	context.fillRect(cellSize * j, cellSize * i, cellSize * health / maxHealth, 3);
}

function drawTowerHealth(i, j, health, maxHealth)
{
	context.fillStyle = "rgb(0, 255, 0)";
	context.fillRect(cellSize * j, cellSize * (i + 1) - 4, cellSize * health / maxHealth, 3);
}

function drawCannonRange(i, j, radius, player, health, maxHealth)
{
	var cannonCheckDX = [1, -1, -1, 1];
	var cannonCheckDY = [1, 1, -1, -1];
	
	for (var k = 1; k <= radius; ++k)
	{
		var dx = 0;
		var dy = -k;
		for (var checkIndex = 0; checkIndex < 4; ++checkIndex)
		{
			for (var p = 0; p < k; ++p)
			{
				if (isCorrectPoint(i + dy, j + dx))
				{
					if (player == 1)
						context.fillStyle = "rgba(150, 0, 0, 0.1)";
					else
						context.fillStyle = "rgba(0, 0, 150, 0.1)";
					
					context.fillRect(cellSize * (j + dx), cellSize * (i + dy), cellSize, cellSize);
				}
				
				dx += cannonCheckDX[checkIndex];
				dy += cannonCheckDY[checkIndex];
			}
		}
	}
	
	drawCannonHealth(i, j, health, maxHealth);
}

function draw()
{
	context.clearRect(0, 0, canvas.width, canvas.height);
	
	context.beginPath();
	
	for (var i = 0; i < 11; ++i)
	{
		context.moveTo(heightStep * i, 0);
		context.lineTo(heightStep * i, canvas.height);
		context.moveTo(0, widthStep * i);
		context.lineTo(canvas.width, widthStep * i);
	}
	
	context.stroke();
	
	for (var i = 0; i < fieldSize; ++i)
	{
		for (var j = 0; j < fieldSize; ++j)
		{
			var sz = cellSize;
            var sz2 = sz / 2;
            var sym = log[it].field[i][j];
			
			if (i == 2 && j == 2)
			{
				context.fillStyle = "rgba(200, 0, 0, 0.5)";
				context.fillRect(sz * j, sz * i, sz, sz);
				context.drawImage(tower, sz * j, sz * i, sz, sz);
				drawTowerHealth(i, j, Number(sym), 100);
			} 
			else if (i == 7 && j == 7)
			{
				context.fillStyle = "rgba(0, 0, 200, 0.5)";
				context.fillRect(sz * j, sz * i, sz, sz);
				context.drawImage(tower, sz * j, sz * i, sz, sz);
				drawTowerHealth(i, j, Number(sym), 100);
			}
			// first player's cannons
			else if (sym >= "201" && sym <= "209")
			{
				context.fillStyle = "rgba(200, 0, 0, 0.5)";
				context.fillRect(sz * j, sz * i, sz, sz);
				context.drawImage(cannonFirst, sz * j, sz * i, sz, sz);
				drawCannonRange(i, j, 1, 1, Number(sym) - 200, 9);
			} 
			else if (sym >= "301" && sym <= "312")
			{
				context.fillStyle = "rgba(200, 0, 0, 0.5)";
				context.fillRect(sz * j, sz * i, sz, sz);
				context.drawImage(cannonSecond, sz * j, sz * i, sz, sz);
				drawCannonRange(i, j, 2, 1, Number(sym) - 300, 12);
			}
			else if (sym >= "401" && sym <= "405")
			{
				context.fillStyle = "rgba(200, 0, 0, 0.5)";
				context.fillRect(sz * j, sz * i, sz, sz);
				context.drawImage(cannonThird, sz * j, sz * i, sz, sz);
				drawCannonRange(i, j, 3, 1, Number(sym) - 400, 5);
			} 
			// second player's cannons
			else if (sym >= "601" && sym <= "609")
			{
				context.fillStyle = "rgba(0, 0, 200, 0.5)";
				context.fillRect(sz * j, sz * i, sz, sz);
				context.drawImage(cannonFirst, sz * j, sz * i, sz, sz);
				drawCannonRange(i, j, 1, 2, Number(sym) - 600, 9);
			} 
			else if (sym >= "701" && sym <= "712")
			{
				context.fillStyle = "rgba(0, 0, 200, 0.5)";
				context.fillRect(sz * j, sz * i, sz, sz);
				context.drawImage(cannonSecond, sz * j, sz * i, sz, sz);
				drawCannonRange(i, j, 2, 2, Number(sym) - 700, 12);
			}
			else if (sym >= "801" && sym <= "805")
			{
				context.fillStyle = "rgba(0, 0, 200, 0.5)";
				context.fillRect(sz * j, sz * i, sz, sz);
				context.drawImage(cannonThird, sz * j, sz * i, sz, sz); 
				drawCannonRange(i, j, 3, 2, Number(sym) - 800, 5);
			}
			
			switch (sym)
			{
				case "1000":
					context.drawImage(gold, sz * j, sz * i, sz, sz);
					break;
				case "500":
					context.drawImage(player1, sz * j, sz * i, sz, sz);
					break;
				case "900":
					context.drawImage(player2, sz * j, sz * i, sz, sz);
					break;
			}
			
			
		}
	}
	
	changeData(log[it].firstPlayer[0], log[it].secondPlayer[0]);
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
            field[i] = rawLog[logLines * z + i].split(" ");
		
		var firstPlayerData = new Object; firstPlayerData = rawLog[logLines * z + fieldSize].split(" ");
		var secondPlayerData = new Object; secondPlayerData = rawLog[logLines * z + fieldSize + 1].split(" ");
		
		var firstPlayer = new Array(); firstPlayer.push({'gold' : firstPlayerData[0], 'score' : firstPlayerData[1]});
		var secondPlayer = new Array(); secondPlayer.push({'gold' : secondPlayerData[0], 'score' : secondPlayerData[1]});
		
		log.push({'field' : field, 'firstPlayer' : firstPlayer, 'secondPlayer' : secondPlayer});
	}
	
	//console.log(log);
}

function changeData(firstPlayer, secondPlayer)
{
	document.getElementById('scoreDiv').innerHTML = "G: " + firstPlayer.gold + ", S: " + firstPlayer.score + " - G: " + secondPlayer.gold + ", S: " + secondPlayer.score; 
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

function stepPrev()
{
    clearInterval(timer);

    if (it > 0)
    {
        --it;
        draw();
    }
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