var context;
var countField;
var timer;
var rawLog;
var dirs = [-Math.PI / 2, 0, Math.PI / 2, -Math.PI];
var steps;
var it;
var block;
var tank1;
var tank2;
var energy;
var bullet;
//var state = [true, true, true, true, true];

function startup()
{
	context = document.getElementById("cv").getContext("2d");
	countField = document.getElementById("cnt");	
	it = 0;

	block = new Image();
	block.src = "images/wall.png";
	//block.onload = function() { state[0] = false; };

	tank1 = new Image();
	tank1.src = "images/tank1.png";
	//tank1.onload = function() { state[1] = false; };

	tank2 = new Image();
	tank2.src = "images/tank2.png";
	//tank2.onload = function() { state[2] = false; };

	energy = new Image();
	energy.src = "images/energy.png";
	//energy.onload = function() { state[3] = false; };

	bullet = new Image();
	bullet.src = "images/bullet.png";
	//bullet.onload = function() { state[4] = false; };

	setLog("\
###########\n\
#....#..4.#\n\
#1........#\n\
#v........#\n\
#.........#\n\
##...#...##\n\
#.........#\n\
#....e....#\n\
#........2#\n\
#.3>.#....#\n\
###########\n\
94 4 5\n\
99 2 0\n\
94 3 5\n\
99 1 0\n\
###########\n\
#....#..4.#\n\
#1........#\n\
#v...e....#\n\
#.........#\n\
##...#...##\n\
#.........#\n\
#....e....#\n\
#........2#\n\
#.3>.#....#\n\
###########\n\
94 4 5\n\
99 2 0\n\
94 3 5\n\
99 1 0");
	draw();
}

function draw()
{
	context.fillStyle = "rgb(0, 0, 0)";
	context.clearRect(0, 0, 352, 352);
	context.fillRect(0, 0, 352, 352);
	context.fillStyle = "rgb(0, 255, 0)";

	for (var i = 0; i < 11; ++i)
	{
		for (var j = 0; j < 11; ++j)
		{
			switch (rawLog[15 * it + i][j])
			{
				case "#":
					context.drawImage(block, 32 * j, 32 * i, 32, 32);
					break;

				case "1":
					context.save();
					context.translate(32 * j + 16, 32 * i + 16);
					context.rotate(dirs[parseInt(rawLog[15 * it + 11].split(" ")[1], 10) - 1]);
					context.drawImage(tank1, -16, -16, 32, 32);
					context.restore();
					context.fillRect(32 * j, 32 * i, 32 * 100 / parseInt(rawLog[15 * it + 11].split(" ")[0], 10), 3);
					break;

				case "2":
					context.save();
					context.translate(32 * j + 16, 32 * i + 16);
					context.rotate(dirs[parseInt(rawLog[15 * it + 12].split(" ")[1], 10) - 1]);
					context.drawImage(tank1, -16, -16, 32, 32);
					context.restore();
					context.fillRect(32 * j, 32 * i, 32 * 100 / parseInt(rawLog[15 * it + 12].split(" ")[0], 10), 3);
					break;

				case "3":
					context.save();
					context.translate(32 * j + 16, 32 * i + 16);
					context.rotate(dirs[parseInt(rawLog[15 * it + 13].split(" ")[1], 10) - 1]);
					context.drawImage(tank2, -16, -16, 32, 32);
					context.restore();
					context.fillRect(32 * j, 32 * i, 32 * 100 / parseInt(rawLog[15 * it + 13].split(" ")[0], 10), 3);
					break;

				case "4":
					context.save();
					context.translate(32 * j + 16, 32 * i + 16);
					context.rotate(dirs[parseInt(rawLog[15 * it + 14].split(" ")[1], 10) - 1]);
					context.drawImage(tank2, -16, -16, 32, 32);
					context.restore();
					context.fillRect(32 * j, 32 * i, 32 * 100 / parseInt(rawLog[15 * it + 14].split(" ")[0], 10), 3);
					break;

				case "e":
					context.drawImage(energy, 32 * j, 32 * i, 32, 32);
					break;
					
				case "<":
					context.save();
					context.translate(32 * j + 16, 32 * i + 16);
					context.rotate(dirs[0]);
					context.drawImage(bullet, -16, -16, 32, 32);
					context.restore();
					break;
					
				case ">":
					context.save();
					context.translate(32 * j + 16, 32 * i + 16);
					context.rotate(dirs[2]);
					context.drawImage(bullet, -16, -16, 32, 32);
					context.restore();
					break;
					
				case "^":
					context.drawImage(bullet, 32 * j, 32 * i, 32, 32);
					break;
					
				case "v":
					context.save();
					context.translate(32 * j + 16, 32 * i + 16);
					context.rotate(dirs[3]);
					context.drawImage(bullet, -16, -16, 32, 32);
					context.restore();
					break;
			}
		}
	}

	countField.innerText = it + " / " + (steps - 1);
}

function setLog(logStrings)
{
	logStrings = logStrings.replace(/\n\n/g, "\n");
	rawLog = logStrings.split("\n");
	steps = rawLog.length / 15;
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
