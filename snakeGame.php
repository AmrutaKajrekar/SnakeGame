<html>
    <head>
	<link rel="stylesheet" type = "text/css" href = "styleSheet.css">

	<body>
		<?php
        	$snakeSpeed = $_POST["speedInput"];
        	$foodCount = $_POST["foodCount"]; 

        	switch ($_POST["sizeOptions"]) {

			   case "S":
			   	$size = "S";
			    break;

			   case "M":
				$size = "M";
			    break;

			   case "L":
				$size = "L";
			    break;
				}

			switch ($_POST["wallsOptions"]) {

			   case "Yes":
			   	$isWallNeeded = "Yes";
			    break;

			   case "No":
				$isWallNeeded =  "No";
			    break;
			}
       ?>

    	<form id = "BackToMenu" action="startPage"  method = "POST">
		<input type="submit" value="Back to Menu" id="menu">
		<label id = "Score">Score: </label>
		<label id = "scoreValue"> </label></br>
		<table id= "gameTable" cellpadding=1 ></table>
		</form>
		
        
        <script type="text/javascript">

        
        snakeSpeed = "<?php echo $snakeSpeed ?>"; //500;
        if(snakeSpeed == null || snakeSpeed == 0 )
        {
        	snakeSpeed = 500; // slow speed
        }

        foodCount = "<?php echo $foodCount ?>";
        if(foodCount == null || foodCount == 0)
    	{
    		foodCount = 1; // minimum food count
    	}
        size = "<?php echo $size ?>";
        if (size == "S")
		{
			numOfRows = 26; 
			numOfCol = 30; 
		}
		else if (size == "L")
		{
			numOfRows = 56;
			numOfCol = 60;
		}
		else 
		{
			numOfRows = 36;
			numOfCol = 40;
		}

		isWallNeeded = "<?php echo $isWallNeeded ?>";
		if (isWallNeeded == "Yes")
		{
			isWalls = true;
		}
		else 
		{
			isWalls = false;
		}

		snakeChunkHeight = 10;
		snakeChunkWidth = 10;

		tableHeight = numOfRows * snakeChunkHeight; 
        tableWidth = numOfCol * snakeChunkWidth;

		backGroundColor = "#E0F8E0";//white color //"#CEF6CE"; //light green color for background
		wallColor = "#000000";
		snakeColor = "#088A08";
		foodColor = "#2B3856";

		var snakeBody = new Array(numOfRows * numOfCol);
		snakeLength = 1;
		var head, tail;
		var prevDir = -1, currDir = -1, nextDir = -1;
		var started = false;
		gameOver = false;
		foodCollision = false;
		score = 0;

        window.onload = function()
		{
			document.onkeydown = keyPressed;
			drawTableCells();
			if(isWalls)
			{
				drawWalls();
			}
			else
			{
				document.getElementById("gameTable").style.border = "3px solid #A4A4A4";
			}
		}

		function drawTableCells()
		{
			for(row = 0; row < numOfRows; row++)
			{
				tr = document.getElementById("gameTable").insertRow(row);
				for(col = 0; col < numOfCol; col++)
				{
					with((td = tr.insertCell(col)).style)
					{
						height = snakeChunkHeight;
						width = snakeChunkWidth;
					}
				}
			}
			document.getElementById("gameTable").style.height = numOfRows * snakeChunkHeight;
			document.getElementById("gameTable").style.width = numOfCol * snakeChunkWidth;

		}


		function drawWalls()
		{
			for(r = 0; r < numOfRows; r++)
			{
				// left
				setColor(0, r, wallColor);
				// right
				setColor(numOfCol - 1, r, wallColor);
			}
			
			for(c = 0; c < numOfCol; c++)
			{
				// top
				setColor(c, 0, wallColor);
				// bottom
				setColor(c, numOfRows - 1, wallColor);
			}
		}


		function startGame()
		{
			
			x = parseInt(numOfCol / 2);
			y = parseInt(numOfRows / 2);		
			tail = -snakeLength;
			head = 0;
			snakeBody[0] = [x, y];
			
			
			gameOver = false;
			foodCollision = false;

			for(var i=0; i<foodCount; i++)
			{
				displayFood();
			}
			moveSnake();
		}
		function displayFood()
		{
			while(1)
			{
				xFood = parseInt((Math.random() * numOfCol)-1);
				yFood = parseInt((Math.random() * numOfRows)-1);
					
				if(xFood > 0 && xFood < tableWidth && yFood > 0 && yFood <tableHeight)
				{
					setColor(xFood, yFood, foodColor);
					break;
				}
			}
			
		}
		function moveSnake()
		{
			if(currDir != -1)
			prevDir = currDir;
			if(nextDir != -1)
			{
				currDir = nextDir;
				nextDir = -1;
			}
			
			if(foodCollision)
			{ foodCollision = false;
			}
			else
			{
				document.getElementById("scoreValue").innerHTML = parseInt(score);
				tail = (tail +1) % snakeBody.length;
				if(tail >= 0)
				{
					x = snakeBody[tail][0];
					y = snakeBody[tail][1];
					setColor(x, y, backGroundColor);
				}
			}
			nextHead = (head+1) % snakeBody.length;

			detectCollisionType();

			if(!gameOver)
			setTimeout("moveSnake()", snakeSpeed);
		}

		function detectCollisionType()
		{
			var object = isCollision(prevDir);
			
			// if the collision is with wall or itself
			if(object == wallColor || object == snakeColor)
			{
				return isGameOver();
			}
			// if snake eats the food
			else if(object == foodColor)
			{
				setColor(x, y, snakeColor);
				displayFood();
				foodCollision = true;
				score = score +20;
				document.getElementById("scoreValue").innerHTML = parseInt(score);
				snakeLength +=1;
			}
			
			setColor(x, y, snakeColor);
			snakeBody[nextHead] = [x, y];
			head = nextHead;
		}

		function isGameOver()
		{
			gameOver = true;
			document.getElementById("gameTable").innerHTML = "Game Over!";
			document.getElementById("gameTable").style.backgroundColor = "#FFFFFF";
		}

		function keyPressed(keyStroke)
		{
			if(!started)
			{
				startGame();
				started = true;
			}
			if(keyStroke)
			{
				keyCode = keyStroke.keyCode;
			}
			else
			{
				keyCode = event.keyCode;
			}
			
			if(gameOver)
				return;
			
			// arrow keys
			if(keyCode >=37 && keyCode <= 40)
			{
				keyCode = keyCode % 4;
				if(prevDir == -1 || prevDir % 2 != keyCode % 2)
					currDir = keyCode;
				else if(currDir % 2 != keyCode % 2)
					nextDir = keyCode;
			}
		}

		function isCollision(dir)
		{	
			leftMargin = (dir - 2) * dir % 2;
			topMargin = (1 - dir) * (1 - dir % 2);
			x = (leftMargin + snakeBody[head][0] + numOfCol) % numOfCol;
			y = (topMargin + snakeBody[head][1] + numOfRows) % numOfRows;

			return document.getElementById("gameTable").rows[y].cells[x].block;
		}
	

		function setColor(row, col, color)
		{
			document.getElementById("gameTable").rows[col].cells[row].style.backgroundColor = color;
			document.getElementById("gameTable").rows[col].cells[row].block = color;
		}

        </script>
	</body>
</html>