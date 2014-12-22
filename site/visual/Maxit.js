var canvas;
var context;
var timer;
var currentLog;
var steps;
var it;
var heightStep;
var widthStep;

var cell = [1, 3, 5, 7, 9, 11];
var activeCellColor = "rgb(255, 255, 0)";
var rowAndColumnColor = "rgb(0, 255, 255)";

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
	canvas.width = canvas.height = height;
	widthStep = canvas.width / 6.0;
	heightStep = canvas.height / 6.0;
	context.font = Math.round(map(canvas.height, 300, 800, 40, 70)) + "px Times New Roman";
	
	draw();
}

function draw()
{
	context.clearRect(0, 0, canvas.width, canvas.height);
	
	context.beginPath();
	
	for (var i = 0; i < 7; ++i)
	{
		context.moveTo(heightStep * i, 0);
		context.lineTo(heightStep * i, canvas.height);
		context.moveTo(0, widthStep * i);
		context.lineTo(canvas.width, widthStep * i);
	}
	
	context.stroke();
	
	// Рисуем вспомогательную подсветку + текущий ход
	drawHelpingCells(currentLog[it].playerInfo);
	if (it != 0)
		drawCurrentCell(currentLog[it].playerInfo);
	
	//fillCell(1 + widthStep * j, 1 + heightStep * i, rowAndColumnColor);
	
	for (var i = 0; i < 6; ++i)
		for (var j = 0; j < 6; ++j)
			if (currentLog[it].field[i][j] != 0)
				context.fillText(Math.abs(currentLog[it].field[i][j]), cell[j] * widthStep / 2 - 15, cell[i] * heightStep / 2 + 15);
			
	changeScore(currentLog[it].score);
}

function changeScore(score)
{
	$('#scoreDiv').html(score[0] + ":" + score[1]);
}

function fillCell(x, y, color)
{
	context.fillStyle = color;
	context.fillRect(x, y, widthStep - 2, heightStep - 2);
	context.fillStyle = "rgb(0, 0, 0)";
}

function drawHelpingCells(playerInfo)
{
	if (playerInfo[0] == 1)
		for (var i = 0; i < 6; ++i)
			fillCell(1 + widthStep * i, 1 + heightStep * (playerInfo[1] - 1), rowAndColumnColor);
	else
		for (var i = 0; i < 6; ++i)
			fillCell(1 + widthStep * (playerInfo[2] - 1), 1 + heightStep * i, rowAndColumnColor);
}

function drawCurrentCell(playerInfo)
{
	fillCell(1 + widthStep * (playerInfo[2] - 1), 1 + heightStep * (playerInfo[1] - 1), activeCellColor);
}

function setLog(logStrings)
{
	logStrings = logStrings.replace(/\n\n/g, "\n");
	var rawLog = logStrings.split("\n");
	if (rawLog.length / 8 != 0 && rawLog[rawLog.length - 1] == "")
		rawLog.pop();
		
	steps = rawLog.length / 8;
	currentLog		= new Array;
	
	for (var z = 0; z < rawLog.length / 8; ++z)
	{
		var field 		= new Object;
		var playerInfo 	= new Object;
		var score 		= new Object;
		// Читаем поле
		for (var i = 0; i < 6; ++i) 
			field[i] = rawLog[8 * z + i].split(" ");
		
		// Читаем кто куда походил
		playerInfo = rawLog[8 * z + 6].split(" ");
		score = rawLog[8 * z + 7].split(" ");
				
		currentLog.push({'field' : field, 'playerInfo' : playerInfo, 'score' : score});
	}
	
	//console.log(currentLog);
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
		document.getElementById('hiddenScore').style.display = 'block';
	
}