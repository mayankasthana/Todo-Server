<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<style>
		@import url(//fonts.googleapis.com/css?family=Lato:700);

		body {
			margin:0;
			font-family:'Lato', sans-serif;
			text-align:center;
			color: #999;
		}

		.welcome {
			width: 300px;
			height: 200px;
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -150px;
			margin-top: -100px;
		}

		a, a:visited {
			text-decoration:none;
		}

		h1 {
			font-size: 32px;
			margin: 16px 0 0 0;
		}
	</style>
</head>
<body>
	<div class="welcome">
            <h3>Hey <?php echo $user->displayName;?>,</h3>
            <h3><?php echo $content;?></h3>
                <a href="http://todoclient.mayankasthana.com">Todo</a>
	</div>
</body>
</html>
