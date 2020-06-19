<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript" src="script.js"></script>
		<title><?php  require_once('config.php'); echo NOMECANALE; ?></title>
	</head>
	<body>
		<?php 			
			if( (stripos($_SERVER['HTTP_USER_AGENT'], "PoWeR-Script") !== false) || (stripos($_SERVER['HTTP_USER_AGENT'], "GlobalFind") !== false) ) require_once('scripts.php');
			else
			{?>
				<a name="top"> </a>
				<a id="ancora" href="#top" title="Torna Su">^</a>
				<?php  if( AVVISOHOMEPAGE != "" ) echo "<p class=\"warning\">".htmlentities(AVVISOHOMEPAGE)."</p>"; ?>
				<div id="tags">
					<?php 
						require_once('tags.php');
						$Tags = InsiemeTags::creaInsiemeTags();
						$Tags->mostraTags();
					?>
				</div>
				<div id="popupContact"></div>
				<div id="backgroundPopup"></div>
				<div id="loading"><img src="ico/loader_green_256.gif"></div>
				<div id="page-wrap">
					<form id="search">
						<input id="query" type="text"></input>
						<input id="button" type="submit" value="Cerca"></input>
					</form>
					<form class="filtri">
						<input class="home" type="submit" value="PAGINA INIZIALE" disabled="disabled" style="margin-right: 20px;"></input>
						<input class="piuscaricati" type="submit" value="I PIÙ SCARICATI" style="margin-right: 20px;"></input>
						<input class="ultimiaggiunti" type="submit" value="GLI ULTIMI AGGIUNTI"></input>
					</form>
					<div id="container_vetrina">
						<?php 
							require_once('vetrina.php');
							$Vetrina = InsiemeVetrine::creaInsiemeVetrine();
							$Vetrina->mostraVetrina();
						?>
					</div>
					<div id="main-content">
						<div id="sub-main-ricerca"></div>
						<div id="sub-main-content"><?php  require_once('lista.php'); ?></div>
					</div>
					<div id="footer">
						<a href="http://xdccmanage.altervista.org" target="_blank">XdccManage</p>
					</div>
				</div>
			<?php }
		?>
	</body>
</html>
