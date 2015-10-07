<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

<title><?php echo $title?></title>

<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<meta name="author" content="Ray Nowell - wod-minder.com" />
<meta name="description" content="<?php echo $site_description;?>" />
<meta name="keywords" content="CrossFit Journal" />
<meta name="robots" content="index, follow" />

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>css/SimpleBlog.css" />
    <?php if ($_SERVER['HTTP_HOST']	=== 'app.wod-minder.com'): ?>
        <script type="text/javascript">

          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-38416567-1']);
          _gaq.push(['_trackPageview']);

          (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

        </script>
    <?php endif; ?>
</head>

<body>
<!-- Wrap -->
<div id="wrap">
		
		<!-- Header -->
		<div id="header">		
			<h1 id="logo">WOD-Minder<span class="gray">Blog</span></h1>
			<h2 id="slogan">Lift it.  Save it.  Track it.</h2>			
		</div>
		
		<!-- menu -->
		<div id="menu">
			<ul>
				<li id="current"><a href="http://www.wod-minder.com"><span>Home</span></a></li>
				<li id="current"><a href="<?php echo base_url();?>index.php/blog"><span>Blog</span></a></li>		
				<li id="current"><a href="<?php echo base_url();?>index.php/"><span>App</span></a></li>
			</ul>
		</div>	
		
		
		<!--Content Wrap -->
		<div id="content-wrap">
			
			<div id="main">
                                <?php echo $main_content;?>
			</div>

            <div id="sidebar">

					<h1>Sidebar Menu</h1>
					<ul class="sidemenu">
						<li id="current"><a href="http://www.wod-minder.com"><span>Home</span></a></li>
						<li id="current"><a href="<?php echo base_url();?>index.php/blog"><span>Blog</span></a></li>		
						<li id="current"><a href="<?php echo base_url();?>index.php/"><span>App</span></a></li>
					</ul>

					<h1>Wise Words</h1>
					<p>&quot;Don't judge each day by the harvest you reap but by the seeds that you plant.&quot; </p>
					<p class="align-right">- Robert Louis Stevenson</p>
            </div>
		
		<!--End content-wrap-->
		</div>
		
		<!-- Footer -->
		<div id="footer">
		
			<p>   			
			&copy; 2013 <a href="http://www.o1solution.com">o1Solution.com</a> &nbsp;&nbsp;
			<a href="http://www.bluewebtemplates.com/" title="Website Templates">website templates</a> by <a href="http://www.styleshout.com/">styleshout</a>
			</p>
			
		</div>	
			
<!-- END Wrap -->
</div>

</body>
</html>
