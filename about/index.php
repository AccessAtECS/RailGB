<!DOCTYPE html>
<html lang="en">
	<head>
		<?
			$path = $_SERVER['DOCUMENT_ROOT'];
			include_once($path.'/includes/header.php');
		?>
		
		<title>RailGB - Accessible Rail Network Map</title>
	</head>

	<body>
	
		<? include_once($path.'/includes/menu.php'); ?>
	
		<div class="container" id="container">
			<div class="page-header">
				<h1><img src="/public/img/pins/rail.png" alt="national rail logo" /> RailGB <small>About the Project</small></h1>
			</div>
			
			<div class="row-fluid">
				<div class="span4">
					<img src="/public/img/theme/class-377-southampton.jpeg" alt="class 377 southern service train" />
				</div>
				<div class="span7 offset1">
					<p>RailGB is a project for the annual WAIS Fest 2012. The team involved are all members of the Web & Internet Sciences (WAIS) research group at the University of Southampton. The aim of the project was to identify accessible public transport systems, primarily the UK rail services and London Underground Network.</p>
					<p>RailGB uses linked data provided by <a href="http://oad.rkbexplorer.com/">RKB Explorer</a>.</p>
					<p>Special thanks to Hugh Glaser for providing the triple store.</p>
					<p>The project was built by Yunjia Li, Russell Newman & Magnus White.  Data on many sites related to accessibility were collected and added to the WAIS fest wiki by Lisha Chen-Wilson, Rumi Hirabayashi, Kewalin Angkananon,  Alaa Mashat  and E.A. Draffan. Unfortunately none of these were in a format that could be used as linked data illustrating the issues arising for those who wish to access  information across the country from a single service. </p>
				</div>
			</div>
		</div>

		<? include_once($path.'/includes/footer.php'); ?>
		
	</body>
</html>