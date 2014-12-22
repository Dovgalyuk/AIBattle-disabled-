var context;
var timer;
var rawLog;
var steps;
var it;
var colors = [
	"rgb(165, 42, 42)",
	"rgb(0, 255, 0)",
	"rgb(255, 0, 0)",
	"rgb(255, 255, 0)",
	"rgb(255, 105, 180)",
	"rgb(64, 224, 208)",
	"rgb(0, 0, 255)",
	"rgb(255, 165, 0)"
];
var map = [
	[7, 6, 5, 4, 3, 2, 1, 0],
	[2, 7, 4, 1, 6, 3, 0, 5],
	[1, 4, 7, 2, 5, 0, 3, 6],
	[4, 5, 6, 7, 0, 1, 2, 3],
	[3, 2, 1, 0, 7, 6, 5, 4],
	[6, 3, 0, 5, 2, 7, 4, 1],
	[5, 0, 3, 6, 1, 4, 7, 2],
	[0, 1, 2, 3, 4, 5, 6, 7]
];
var p1 = [
	[0, 7],
	[0, 6],
	[0, 5],
	[0, 4],
	[0, 3],
	[0, 2],
	[0, 1],
	[0, 0]
];
var p2 = [
	[7, 0],
	[7, 1],
	[7, 2],
	[7, 3],
	[7, 4],
	[7, 5],
	[7, 6],
	[7, 7]
];

function startup(width, height)
{
	canvas = document.getElementById("cv");
	canvas.width = canvas.height = height;
	
	context = canvas.getContext("2d");
	context.lineWidth = 8;
	it = 0;
	
	draw();
<?php
	echo "setLog(\"" .$log."\");";
?>
};

function update()
{
	for (var i = 0; i < 8; ++i)
	{
		p1[i][0] = parseInt(rawLog[2 * it][2 * i], 10) - 1;
		p1[i][1] = parseInt(rawLog[2 * it][2 * i + 1], 10) - 1;

		p2[i][0] = parseInt(rawLog[2 * it + 1][2 * i], 10) - 1;
		p2[i][1] = parseInt(rawLog[2 * it + 1][2 * i + 1], 10) -1;

	}
}

function draw()
{
	context.clearRect(0, 0, canvas.width, canvas.height);

	for (var i = 0; i < 8; ++i)
	{
		for (var j = 0; j < 8; ++j)
		{
			context.fillStyle = colors[map[i][j]];
			context.fillRect(64 * j, 64 * i, 64, 64);
		}
	}

	for (var i = 0; i < 8; ++i)
	{
		context.beginPath();
		context.arc(64 * p1[7 - i][1] + 32, 64 * p1[7 - i][0] + 32, 20, 0, 2 * Math.PI);
		context.closePath();
		context.strokeStyle = "rgb(255, 255, 255)";
		context.stroke();
		context.fillStyle = colors[7 - i];
		context.fill();

		context.beginPath();
		context.arc(64 * p2[i][1] + 32, 64 * p2[i][0] + 32, 20, 0, 2 * Math.PI);
		context.closePath();
		context.strokeStyle = "rgb(0, 0, 0)";
		context.stroke();
		context.fillStyle = colors[i];
		context.fill();
	}
}

function setLog(logStrings)
{
	logStrings = logStrings.replace(/\n\n/g, "\n");
	var t = logStrings.split("\n");
	steps = t.length / 2;
	rawLog = new Array(2 * steps);

	for (var i = 0; i < 2 * steps; ++i)
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
		update();
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
		update();
		draw();
	}
}

function stepNext()
{
	clearInterval(timer);

	if (it < steps - 1)
	{
		++it;
		update();
		draw();
	}
}