var canvas;
var context;
var timer;
var currentLog;
var steps;
var it;
var heightStep;
var widthStep;
var drawStartX;

var cell = [1, 3, 5, 7, 9, 11, 13];
var cellSize = 14.0;

var xColor = "rgb(255, 0, 0)";
var missColor = "rgb(0, 0, 255)";
var normalShip = "rgb(240, 220, 130)";
var deadShip = "rgb(105, 105, 105)";

function startup(width, height)
{
	canvas = document.getElementById("cv");
	context = canvas.getContext("2d");
	context.lineWidth = 1;
	it = 0;
	
	document.getElementById('hiddenScore').style.display = 'none';
	
	<?php
		echo "setLog(\"" .$log."\");";
	?>
	
	resize(width, height);
}

function map(x, in_min, in_max, out_min, out_max)
{
	return (x - in_min) * (out_max - out_min) / (in_max - in_min) + out_min;
}

function resize(width, height)
{
	canvas.width = width - 100;
	canvas.height = height;
	widthStep = (1.5 * height - 20) / cellSize;
	heightStep = 1.5 * canvas.height / cellSize;
	context.font = Math.round(map(canvas.height, 300, 800, 10, 40)) + "px Times New Roman";
	drawStartX = canvas.width / 2 - (7 * widthStep + 10);
	
	draw();
}

function draw()
{
	context.clearRect(0, 0, canvas.width, canvas.height);
	
	drawFieldState(currentLog[it].firstPlayerField, drawStartX, 0);
	drawFieldState(currentLog[it].secondPlayerField, drawStartX + 7 * widthStep + 20, 0);
	
	drawField(drawStartX, 0);
	drawField(drawStartX + 7 * widthStep + 20, 0);
	
	context.fillStyle = "rgb(0, 0, 0)";
	
	context.fillText("Корабли первого игрока", drawStartX + 2 * widthStep, 8 * heightStep);
	context.fillText("Корабли второго игрока", drawStartX + 9 * widthStep, 8 * heightStep);
	
	changeScore(currentLog[it].score);
}

function drawField(startX, startY)
{
	context.beginPath();
	
	for (var i = 0; i < 8; ++i)
	{
		context.moveTo(startX + widthStep * i, startY);
		context.lineTo(startX + widthStep * i, startY + heightStep * 7);
		context.moveTo(startX, startY + heightStep * i);
		context.lineTo(startX + widthStep * 7, startY + heightStep * i);
	}
	
	context.stroke();
}

function drawFieldState(state, startX, startY)
{
	for (var i = 0; i < 7; ++i)
	{
		for (var j = 0; j < 7; ++j)
		{
			switch (state[i][j])
			{
				case "1":
					drawFigureO(startX + (cell[j] * widthStep / 2), startY + (cell[i] * heightStep / 2));
					break;
				case "2":
					drawShip(startX + widthStep * j, startY + heightStep * i, normalShip);
					drawFigureX(startX + (cell[j] * widthStep / 2), startY + (cell[i] * heightStep / 2));
					break;
				case "3":
					drawShip(startX + widthStep * j, startY + heightStep * i, deadShip);
					drawFigureX(startX + (cell[j] * widthStep / 2), startY + (cell[i] * heightStep / 2));
					break;
				case "4":
					drawShip(startX + widthStep * j, startY + heightStep * i, normalShip);
					break;
				case "5":
					drawFigureQ(startX + (cell[j] * widthStep / 2), startY + (cell[i] * heightStep / 2));
					break;
			}
		}
	}
}

function drawFigureX(x, y)
{
	context.beginPath();
	
	var dX = widthStep 	* 0.5;
	var dY = heightStep * 0.5;
	
	context.moveTo(x - dX, y - dY);
	context.lineTo(x + dX, y + dY);
	context.moveTo(x + dX, y - dY);
	context.lineTo(x - dX, y + dY);
	
	context.strokeStyle = xColor;
	context.stroke();
	context.strokeStyle = "rgb(0, 0, 0)";
}

function drawFigureO(x, y)
{
	context.beginPath();
	
	var radius = 4;
	
	context.arc(x, y, radius, 0, 2 * Math.PI);
	
	context.fillStyle = missColor;
	context.fill();
	context.fillStyle = "rgb(255, 255, 255)";
	
	context.strokeStyle = missColor;
	context.stroke();
	context.strokeStyle = "rgb(0, 0, 0)";
}

function drawFigureQ(x, y)
{
	context.beginPath();
	
	var radius = 4;
	
	context.arc(x, y, radius, 0, 2 * Math.PI);
	
	context.strokeStyle = missColor;
	context.stroke();
	context.strokeStyle = "rgb(0, 0, 0)";
}

function drawShip(x, y, color)
{
	context.fillStyle = color;
	context.fillRect(x, y, widthStep, heightStep);
	context.fillStyle = "rgb(255, 255, 255)";
}

function changeScore(score)
{
	$('#scoreDiv').html(score[0] + ":" + score[1]);
}

function checkDeadShip(field)
{
	for (var i = 0; i < 7; ++i)
	{
		for (var j = 0; j < 7; ++j)
		{
			if (field[i][j] == "3")
			{
				if (i - 1 >= 0 && j - 1 >= 0 && field[i - 1][j - 1] == "0")
					field[i - 1][j - 1] = "5";
				
				if (j - 1 >= 0 && field[i][j - 1] == "0")
					field[i][j - 1] = "5";
				
				if (i + 1 < 7 && j - 1 >= 0 && field[i + 1][j - 1] == "0")
					field[i + 1][j - 1] = "5";
				
				if (i - 1 >= 0 && field[i - 1][j] == "0")
					field[i - 1][j] = "5";
				
				if (i + 1 < 7 && field[i + 1][j] == "0")
					field[i + 1][j] = "5";
				
				if (i - 1 >= 0 && j + 1 < 7 && field[i - 1][j + 1] == "0")
					field[i - 1][j + 1] = "5";
				
				if (j + 1 < 7 && field[i][j + 1] == "0")
					field[i][j + 1] = "5";
				
				if (i + 1 < 7 && j + 1 < 7 && field[i + 1][j + 1] == "0")
					field[i + 1][j + 1] = "5";
			}
		}
	}
}

function setLog(logStrings)
{
	logStrings = logStrings.replace(/\n\n/g, "\n");
	var rawLog = logStrings.split("\n");
	
	if (rawLog.length / 15 != 0 && rawLog[rawLog.length - 1] == "")
		rawLog.pop();
	
	steps = rawLog.length / 15;
	currentLog = new Array;
	
	for (var z = 0; z < steps; ++z)
	{
		var firstPlayerField = new Array(7);
		var secondPlayerField = new Array(7);
		var score = new Object;
		
		for (var i = 0; i < 7; ++i)
			firstPlayerField[i] = rawLog[15 * z + i].split(" ");
		
		for (var i = 7; i < 14; ++i)
			secondPlayerField[i - 7] = rawLog[15 * z + i].split(" ");
		
		checkDeadShip(firstPlayerField);
		checkDeadShip(secondPlayerField);
		
		score = rawLog[15 * z + 14].split(" ");
		
		currentLog.push({'firstPlayerField' : firstPlayerField, 'secondPlayerField' : secondPlayerField, 'score' : score});
	}
}

function start()
{
	clearInterval(timer);
	it = 0;

	timer = setInterval(function()
	{
		draw();
			
		if (it == steps - 1)
		{
			clearInterval(timer);
			document.getElementById('hiddenScore').style.display = 'block';
		}
		else
		{
			++it;
		}
	}, 1000);
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
	{
		document.getElementById('hiddenScore').style.display = 'block';
	}
}