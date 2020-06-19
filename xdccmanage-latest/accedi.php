<?php 
	session_start();
	require_once('config.php');
	require_once('function.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
		<link rel="stylesheet" type="text/css" href="css/styleadmin.css" />
		<title> <?php  echo NOMECANALE." - Amministrazione"; ?> </title>
	</head>

	<body> 
		<div id="page-admin-wrap">
			<?php 
				if( loggato() == false )
				{
					if( isset($_POST['user']) && isset($_POST['pass']) )
					{
						if( ($_POST['user'] == USER) && (md5($_POST['pass']) == PASS) )
						{
							$_SESSION['user'] = USER;
							$_SESSION['userip'] = $_SERVER['REMOTE_ADDR'];
						}
						echo "<meta http-equiv=\"Refresh\" content=\"0; url=accedi.php\">";
					}
					else
					{?>
						<fieldset id="login">
							<legend>Login</legend>
							<table>
								<form action="accedi.php" method="POST">
									<tr>
										<td>Utente </td>
										<td><input type="text" name="user"></input></td>
									</tr>
									<tr>
										<td>Password </td>
										<td><input type="password" name="pass"></input>
									</tr>
									<tr>
										<td></td>
										<td><input id="loginbutton" type="submit" name="accedi" value="Accedi"></input><td>
									</tr>
								</form>
							</table>
						</fieldset>
					<?php }
				}
				else
				{?>
					<div id="menu">
						<ul>
							<li><a href="?act=impostazioni">GENERALE</a> | </li>
							<li><a href="?act=vetrina">VETRINE</a> | </li>
							<li><a href="?act=xdccfile">BOTS</a> | </li>
							<li><a href="?act=tags">TAGS</a> | </li>
							<li><a href="?act=esci">LOGOUT</a></li>
						</ul>
					</div>
					<div style="clear: both;"></div>
					<div>
						<?php 	
							if( isset($_GET['act']) )
							{
								if( ($_GET['act'] == "esci") )
								{
									esci();
									echo "<meta http-equiv=\"Refresh\" content=\"0; url=.\">";
								}
								else if( ($_GET['act'] == "impostazioni") )
								{
									if( isset($_POST['impostazioni']) )
									{
										if( ($fp = fopen("config.php.temp", "w+")) )
										{
											fwrite($fp, "<?php \r\n");
											fwrite($fp, "define(\"XDCCDIR\", \"".XDCCDIR."\");\r\n");
											fwrite($fp, "define(\"VETRINACONF\", \"".VETRINACONF."\");\r\n");
											fwrite($fp, "define(\"STATOVETRINA\", \"".STATOVETRINA."\");\r\n");
											
											if( isset($_POST['maxElemVetr']) && is_numeric($_POST['maxElemVetr']) )
											{	
												if( $_POST['maxElemVetr'] != MAXELEMPERVETRINA )
												{
													fwrite($fp, "define(\"MAXELEMPERVETRINA\", \"".$_POST['maxElemVetr']."\");\r\n");
													require_once('vetrina.php');
													$Vetrine = InsiemeVetrine::creaInsiemeVetrine();
													$Vetrine->rifaiVetrine();
												}
												else
													fwrite($fp, "define(\"MAXELEMPERVETRINA\", \"".MAXELEMPERVETRINA."\");\r\n");
											}
											
											if( isset($_POST['maxBotPerVolta']) && is_numeric($_POST['maxBotPerVolta']) && ($_POST['maxBotPerVolta'] > 0))
												fwrite($fp, "define(\"MAXBOTPERVOLTA\", \"".$_POST['maxBotPerVolta']."\");\r\n");
											else
												fwrite($fp, "define(\"MAXBOTPERVOLTA\", \"\");\r\n");
												
											if( isset($_POST['canale']) && !empty($_POST['canale']) )
												fwrite($fp, "define(\"NOMECANALE\", \"".$_POST['canale']."\");\r\n");
											else
												fwrite($fp, "define(\"NOMECANALE\", \"".NOMECANALE."\");\r\n");
												
											if( isset($_POST['user']) && !empty($_POST['user']) )
												fwrite($fp, "define(\"USER\", \"".$_POST['user']."\");\r\n");
											else
												fwrite($fp, "define(\"USER\", \"".USER."\");\r\n");

											if( isset($_POST['passVecchia']) && (md5($_POST['passVecchia']) == PASS) && isset($_POST['passNuova']) && isset($_POST['passConferma']) && !empty($_POST['passNuova']) && !empty($_POST['passConferma']) && ($_POST['passNuova'] == $_POST['passConferma']) )
												fwrite($fp, "define(\"PASS\", \"".md5($_POST['passNuova'])."\");\r\n");
											else
												fwrite($fp, "define(\"PASS\", \"".PASS."\");\r\n");
											
											if( isset($_POST['numVetPerRiga']) )
											{
												if ( $_POST['numVetPerRiga'] == "quattro" )
													fwrite($fp, "define(\"NUMVETRINEPERRIGA\", \"4\");\r\n");
												else if ( $_POST['numVetPerRiga'] == "cinque" )
													fwrite($fp, "define(\"NUMVETRINEPERRIGA\", \"5\");\r\n");
												else
													fwrite($fp, "define(\"NUMVETRINEPERRIGA\", \"3\");\r\n");
											}
											
											if( isset($_POST['formatoTitoli']) )
											{
												if ( $_POST['formatoTitoli'] == "notag" )
													fwrite($fp, "define(\"FORMATOTITOLI\", \"notag\");\r\n");
												else
													fwrite($fp, "define(\"FORMATOTITOLI\", \"nopunti\");\r\n");
												
												require_once('vetrina.php');
												$Vetrine = InsiemeVetrine::creaInsiemeVetrine();
												$Vetrine->rifaiVetrine();
											}
											
											if( isset($_POST['info']) )
											{
												if( $_POST['info'] == "si" )
													fwrite($fp, "define(\"INFO\", \"si\");\r\n");
												else
													fwrite($fp, "define(\"INFO\", \"no\");\r\n");
											}
											
											if( isset($_POST['avviso']) )
												fwrite($fp, "define(\"AVVISOHOMEPAGE\", \"".$_POST['avviso']."\");\r\n");
											else
												fwrite($fp, "define(\"AVVISOHOMEPAGE\", \"\");\r\n");
											
											fwrite($fp, "?>\r\n");
											fclose($fp);
											unlink("config.php");
											rename("config.php.temp", "config.php");
											
											if( isset($_POST['maxElemVetr']) && is_numeric($_POST['maxElemVetr']) )
												echo "<meta http-equiv=\"Refresh\" content=\"0; url=accedi.php?act=impostazioni&mod=sistema\">";
											else
												echo "<meta http-equiv=\"Refresh\" content=\"0; url=accedi.php?act=impostazioni\">";
										}
									}
									echo "<div class=\"settings\">";
										echo "<form action=\"accedi.php?act=impostazioni\" method=\"POST\">";
											echo "<fieldset>";
											echo "<legend>IMPOSTAZIONI PERSONALI</legend>";
												echo "<table class=\"tabella\">";
													echo "<tr>";
														echo "<td><b>Nome admin</b></td>";
														echo "<td><input type=\"text\" name=\"user\" value=\"".USER."\"></input></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td colspan=\"2\"><hr></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td><b>Password corrente</b></td>";
														echo "<td><input type=\"password\" name=\"passVecchia\"></input></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td><b>Nuova password</b></td>";
														echo "<td><input type=\"password\" name=\"passNuova\"></input></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td><b>Ripeti nuova password</b></td>";
														echo "<td><input type=\"password\" name=\"passConferma\"></input></td>";
													echo "</tr>";
												echo "</table>";
											echo "</fieldset>";
											
											echo "<fieldset>";
											echo "<legend>IMPOSTAZIONI LISTA</legend>";
												echo "<table class=\"tabella\">";
													echo "<tr>";
														echo "<td><b>Nome del canale</b></td>";
														echo "<td><input type=\"text\" name=\"canale\" value=\"".NOMECANALE."\"></input></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td></td>";
														echo "<td class=\"desc\">Imposta il titolo della pagina<br><br></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td><b>Elementi per vetrina</b></td>";
														echo "<td><input type=\"text\" name=\"maxElemVetr\" value=\"".MAXELEMPERVETRINA."\"></input></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td></td>";
														echo "<td class=\"desc\">Se impostato a 0 non verrà visualizzata la vetrina<br><br></td>";
													echo "</tr>";
													
													echo "<tr>";
														echo "<td><b>Bot per pagina</b></td>";
														echo "<td><input type=\"text\" name=\"maxBotPerVolta\" value=\"".MAXBOTPERVOLTA."\"></input></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td></td>";
														echo "<td class=\"desc\">Se lasciato vuoto verrà visualizzata l'intera lista</td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td></td>";
														echo "<td class=\"desc\">Per valori positivi i bot verranno caricati man mano<br><br></td>";
													echo "</tr>";
													
													echo "<tr>";
														echo "<td colspan=\"2\"><hr></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td colspan=\"2\"><b>Numero di vetrine per riga</b></td>";
														echo "<td></td>";
													echo "</tr>";
													if( NUMVETRINEPERRIGA == "3" )
													{
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"numVetPerRiga\" value=\"tre\" checked> tre </input></td>";
															echo "<td></td>";
														echo "</tr>";
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"numVetPerRiga\" value=\"quattro\"> quattro </input></td>";
															echo "<td></td>";
														echo "</tr>";
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"numVetPerRiga\" value=\"cinque\"> cinque </input></td>";
															echo "<td></td>";
														echo "</tr>";
													}
													else if( NUMVETRINEPERRIGA == "4" )
													{
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"numVetPerRiga\" value=\"tre\"> tre </input></td>";
															echo "<td></td>";
														echo "</tr>";
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"numVetPerRiga\" value=\"quattro\" checked> quattro </input></td>";
															echo "<td></td>";
														echo "</tr>";
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"numVetPerRiga\" value=\"cinque\"> cinque </input></td>";
															echo "<td></td>";
														echo "</tr>";
													}
													else if( NUMVETRINEPERRIGA == "5" )
													{
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"numVetPerRiga\" value=\"tre\"> tre </input></td>";
															echo "<td></td>";
														echo "</tr>";
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"numVetPerRiga\" value=\"quattro\"> quattro </input></td>";
															echo "<td></td>";
														echo "</tr>";
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"numVetPerRiga\" value=\"cinque\"  checked> cinque </input></td>";
															echo "<td></td>";
														echo "</tr>";
													}
													
													echo "<tr>";
														echo "<td colspan=\"2\"><hr></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td colspan=\"2\"><b>Scegli il formato dei singoli titoli in vetrina</b></td>";
														echo "<td></td>";
													echo "</tr>";
													if( FORMATOTITOLI == "nopunti" )
													{
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"formatoTitoli\" value=\"nopunti\" checked> Prova 2012 iTALiAN DVDRip avi </input></td>";
															echo "<td></td>";
														echo "</tr>";
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"formatoTitoli\" value=\"notag\"> Prova 2012 </input></td>";
															echo "<td></td>";
														echo "</tr>";
													}
													else if( FORMATOTITOLI == "notag" )
													{
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"formatoTitoli\" value=\"nopunti\"> Prova 2012 iTALiAN DVDRip avi </input></td>";
															echo "<td></td>";
														echo "</tr>";
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"formatoTitoli\" value=\"notag\" checked> Prova 2012 </input></td>";
															echo "<td></td>";
														echo "</tr>";
													}
													
													echo "<tr>";
														echo "<td colspan=\"2\"><hr></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td colspan=\"2\"><b>Visualizza i trailer sulla prima vetrina</b></td>";
													echo "</tr>";
													if( INFO == "si" )
													{
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"info\" value=\"si\" checked> Si </input> </td>";
															echo "<td></td>";
														echo "</tr>";
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"info\" value=\"no\"> No </input></td>";
															echo "<td></td>";
														echo "</tr>";
													}
													else
													{
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"info\" value=\"si\"> Si </input> </td>";
															echo "<td></td>";
														echo "</tr>";
														echo "<tr>";
															echo "<td><input type=\"radio\" name=\"info\" value=\"no\" checked> No </input></td>";
															echo "<td></td>";
														echo "</tr>";
													}
													
													echo "<tr>";
														echo "<td colspan=\"2\"><hr></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td colspan=\"2\"><b>Avviso</b></td>";
														echo "<td></td>";
													echo "</tr>";
													echo "<tr>";
														echo "<td colspan=\"2\"><textarea type=\"textarea\" name=\"avviso\" rows=\"10\" cols=\"60\" >".htmlentities(AVVISOHOMEPAGE)."</textarea></td>";
													echo "</tr>";

												echo "</table>";
											echo "</fieldset>";
											echo "<div id=\"altririsultati\"><input type=\"submit\" name=\"impostazioni\" value=\"Salva\"></input></div>";
										echo "</form>";
									echo "</div>";
								}
								else if( ($_GET['act'] == "vetrina") )
								{
									require_once('vetrina.php');
									$Vetrina = InsiemeVetrine::creaInsiemeVetrine();
									$Vetrina->impostazioniVetrina();
								}
								else if( ($_GET['act'] == "xdccfile") )
								{
									if( isset($_GET['opt']) && isset($_POST['nomeBot']) )
									{
										foreach( $_POST['nomeBot'] as $bot )
											eliminaBot($bot);
										
										echo "<meta http-equiv=\"Refresh\" content=\"0; url=accedi.php?act=xdccfile\">";
									}
									else
									{
										if( ($bots = botsToArray()) )
										{
											echo "<div class=\"settings\">";
												echo "<form method=\"POST\" action=\"accedi.php?act=xdccfile&opt=elimina\">";
													echo "<fieldset>";
														echo "<legend>Seleziona i bots da eliminare</legend>"; 
														echo "<table class=\"tabella\" style=\"margin:0 auto;\">";
															echo "<tr>";
																echo "<td>";
																	echo "<select class=\"CONFIG\" multiple=\"multiple\" name=\"nomeBot[]\" size=\"20\">";
																		foreach( $bots as $bot )
																			echo "<option>".getNickName($bot)."</option>";
																	echo "</select>";
																echo "</td>";
															echo "</tr>";
														echo "</table>";
													echo "</fieldset>";
													echo "<div id=\"altririsultati\"><input type=\"submit\" name=\"submit\" value=\"Elimina\"></input></div>";
												echo "</form>";
											echo "</div>";
										}
										else
											echo "<p>Nessun bot è stato trovato.</p>";
									}
								}
								else if( ($_GET['act'] == "tags") )
								{
									require_once('tags.php');
									$Vetrina = InsiemeTags::creaInsiemeTags();
									$Vetrina->impostazioniTags();
								}
							}
							else
								echo "<meta http-equiv=\"Refresh\" content=\"0; url=accedi.php?act=impostazioni\">";
						?>
					</div>
				<?php }
			?>
		</div>
	</body>
</html>
