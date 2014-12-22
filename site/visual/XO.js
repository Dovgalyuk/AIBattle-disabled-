var canvas;
var context;
var timer;
var rawLog;
var steps;
var it;

var heightStep;
var widthStep;

var players = ["rgb(255, 0, 0)", "rgb(0, 0, 255)"];
var cell 	= [1, 3, 5];

function startup(width, height)
{
	canvas = document.getElementById("cv");
	context = canvas.getContext("2d");
	context.lineWidth = 8;
	it = 0;
	
	<?php
		echo "setLog(\"" .$log."\");";
	?>
	
	resize(width, height);
}

function resize(width, height)
{
	canvas.width = canvas.height = height;
	widthStep = canvas.width / 3.0;
	heightStep = canvas.height / 3.0;
	
	draw();
}

function draw()
{
	context.clearRect(0, 0, canvas.width, canvas.height);
	
	// field
	context.beginPath();
	
	context.moveTo(heightStep, 0);
	context.lineTo(heightStep, canvas.height);
	context.moveTo(heightStep * 2, 0);
	context.lineTo(heightStep * 2, canvas.height);
	
	context.moveTo(0, widthStep);
	context.lineTo(canvas.width, widthStep);
	context.moveTo(0, widthStep * 2);
	context.lineTo(canvas.width, widthStep * 2);
	
	context.stroke();
		
	for (var i = 0; i < 3; ++i)
	{
		for (var j = 0; j < 3; ++j)
		{
			var index = 3 * it + i;
			if (rawLog[index][j] == 1)
				drawFigureX(cell[j] * widthStep / 2, cell[i] * heightStep / 2, rawLog[index][j] - 1);
			else if (rawLog[index][j] == 2)
				drawFigureO(cell[j] * widthStep / 2, cell[i] * heightStep / 2, rawLog[index][j] - 1);
		}
	}
}

function drawFigureX(x, y, color)
{
	context.beginPath();
	
	var dX = widthStep 	* 0.25;
	var dY = heightStep * 0.25;
	
	context.moveTo(x - dX, y - dY);
	context.lineTo(x + dX, y + dY);
	context.moveTo(x + dX, y - dY);
	context.lineTo(x - dX, y + dY);
	
	context.strokeStyle = players[color];
	context.stroke();
	context.strokeStyle = "rgb(0, 0, 0)";
}

function drawFigureO(x, y, color)
{
	context.beginPath();
	
	var radius = widthStep * 0.3;
	
	context.arc(x, y, radius, 0, 2 * Math.PI);
	
	context.strokeStyle = players[color];
	context.stroke();
	context.strokeStyle = "rgb(0, 0, 0)";
}

function setLog(logStrings)
{
	logStrings = logStrings.replace(/\n\n/g, "\n");
	var t = logStrings.split("\n");
	steps = Math.round(t.length / 3);
	rawLog = new Array(steps * 3);
		
	for (var i = 0; i < 3 * steps; ++i)
	{
		rawLog[i] = t[i].split(" ");
	}
}

function start()
{
	clearInterval(timer);
	it = 0;

	timer = setInterval(function()
	{
		if (rawLog[3 * it].length == 3)
			draw();
			
		if (it == steps - 1)
		{
			clearInterval(timer);
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
}