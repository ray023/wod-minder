<html>
	<head> 
		<title>Support page</title> 
		<link href="http://www.wod-minder.com/css/mobile-main.css" rel="stylesheet" type="text/css" />		
		<link href="http://www.wod-minder.com/css/all-shared.css" rel="stylesheet" type="text/css" />	
                <style>
                #js-warning p {
                        background:#ffcc00;
                        font-size:110%;
                        font-family:verdana;
                        text-align:center;
                        text-transform:uppercase;
                        font-weight:bold;
                        padding:0.8em;
                        color: red;
                }

                #style-warning
                {
                        background:#ffcc00;
                        font-size:110%;
                        font-family:verdana;
                        text-align:center;
                        text-transform:uppercase;
                        font-weight:bold;
                        padding:0.8em;
                        color: red;
                }
                </style>
	</head>
	<body>
		<noscript id="js-warning">
			<p>Problem:  Javascript is disabled; please enable.</p>
		</noscript>
		<p>
		<?php
		setcookie('wm_test', 1, time()+3600);
		if(!isset($_GET['cookies'])){
			header('Location:/support.php?cookies=true');
		}
		if(count($_COOKIE) > 0){
			echo "Cookies enabled; this is good.";
		} else {
			echo "<p id=\"style-warning\">Problem:  Cookies are not enabled for this browser. Please allow cookies for this site.</p>";
		}
		?>
		</p>
		<p>
			If there are errors listed above, then WoD-Minder is not quite ready for your device...yet!
		</p>
		<p>
			Please send us your problem to <a href="mailto:ray023@gmail.com?Subject=Help">ray023@gmail.com</a> and we will resolve it as quickly as possible.
		</p>
	</body>
</html>
