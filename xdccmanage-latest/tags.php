<?php 
	
	require_once('config.php');
	require_once('function.php');

	class InsiemeTags
	{
		private $vetrine;
		private static $istanza;
		
		public static function creaInsiemeTags()
		{
			if( isset($istanza) )
			{
				return InsiemeTags::$istanza;
			}
			else if( is_file("tags.stat") )
			{
				if( ($fp = fopen("tags.stat", "r")) )
				{
					if( (InsiemeTags::$istanza = unserialize(fread($fp, filesize("tags.stat")))) === false )
						InsiemeTags::$istanza = new InsiemeTags();

					fclose($fp);
					return InsiemeTags::$istanza;
				}
			}
			else
			{
				InsiemeTags::$istanza = new InsiemeTags();
				return InsiemeTags::$istanza;
			}
		}

		public function eliminaVetrine()
		{
			if( (is_file("tags.stat") === true) && (unlink("tags.stat") === true) )
				InsiemeTags::$istanza = NULL;
		}
		
		private function __construct()
		{
			$vetrine = array();
		}
		
		// aggiunge una vetrina
		public function aggiungiVetrina($nomeVetrina)
		{
			if( !isset($this->vetrine[$nomeVetrina]) )
			{
				$this->vetrine[$nomeVetrina] = array();
				$this->salvaStatoInsiemeTags();
				return true;
			}
			return false;
		}
		
		// elimina completamente la vetrina passata
		public function eliminaVetrina($nomeVetrina)
		{
			if( isset($this->vetrine[$nomeVetrina]) )
			{
				unset($this->vetrine[$nomeVetrina]);
				$this->salvaStatoInsiemeTags();
				return true;
			}
			return false;
		}
		
		public function aggiungiBotAllaVetrina($nomeVetrina, $nomeBot)
		{
			if( isset($this->vetrine[$nomeVetrina]) )
			{
				if( ($filename = botToFile($nomeBot)) )
				{
					if( !isset($this->vetrine[$nomeVetrina]['listaBot']) )
					{
						$this->vetrine[$nomeVetrina]['listaBot'] = ":".$filename.":";
						$this->salvaStatoInsiemeTags();
						return true;
					}
					else if( stripos($this->vetrine[$nomeVetrina]['listaBot'], ":".$filename.":") === false )		//se è definita listaBot controllo che nomeBot non esista, e allora posso concatenarlo alla listaBot esistente
					{
						$this->vetrine[$nomeVetrina]['listaBot'] .= $filename.":";
						$this->salvaStatoInsiemeTags();
						return true;
					}
				}
			}
			return false;
		}
		
		// elimina l'associazione bot<-->vetrina
		public function togliBotAllaVetrina($nomeVetrina, $fileNameBot)
		{
			if( isset($this->vetrine[$nomeVetrina]) && isset($this->vetrine[$nomeVetrina]['listaBot']) )		//se è definita la vetrina passata e questa contiene almeno un bot
			{
				if( ($pos = stripos($this->vetrine[$nomeVetrina]['listaBot'], ":".$fileNameBot.":")) !== false )		//se esiste il bot passato
				{
					if( $this->vetrine[$nomeVetrina]['listaBot'] == ":".$fileNameBot.":" )
					{
						unset($this->vetrine[$nomeVetrina]['listaBot']);
						unset($this->vetrine[$nomeVetrina]['listaElem']);
						unset($this->vetrine[$nomeVetrina]['lastMod']);
					}
					else
					{
						$this->vetrine[$nomeVetrina]['listaBot'] = substr($this->vetrine[$nomeVetrina]['listaBot'], 0, $pos)."".substr($this->vetrine[$nomeVetrina]['listaBot'], ($pos+strlen($fileNameBot)+1));
					}
					$this->salvaStatoInsiemeTags();
					return true;
				}
			}
			return false;
		}
		
		/* fa una copia su file dell'oggetto */
		public function salvaStatoInsiemeTags()
		{
			if( $fp = fopen("tags.stat", "w") )
				fwrite($fp, serialize(InsiemeTags::$istanza));
				
			fclose($fp);
		}
		
		/* restituisce i bots addati alla singola vetrina */
		public function botsDellaVetrina($nomeVetrina)
		{
			if( isset($this->vetrine[$nomeVetrina]['listaBot']) )
			{
				$listaBot = $this->vetrine[$nomeVetrina]['listaBot'];
				$bots = NULL;
				
				$bot = strtok($listaBot, ":");
				while( $bot !== false )
				{
					$bots[] = $bot;
					$bot = strtok(":");
				}
				
				return $bots ? $bots : NULL;
			}
		}
		
		public function mostraTags()
		{	
			if( isset($this->vetrine) )
			{
				if( ($listaTags = array_keys($this->vetrine)) )
				{
					echo "<b>Tags:</b> ";
					$count = 0;
					foreach ($listaTags as $nomeTag)
					{
						if( isset($this->vetrine[$nomeTag]['listaBot']) )
						{
							if( $count > 0 )
								echo " , ";
							echo "<a href=\"#\">" . $nomeTag . "</a>";
							$count++;
						}
					}
				}
			}
		}
		
		function printTags($tags)
		{
			if( ($listaBot = $this->botsDellaVetrina($tags)) )
			{
				echo "<table class=\"lista\">";
					echo "<tr class=\"header\"><td>Bot</td><td style=\"display: none;\">Pack</td><td>Dim.</td><td style=\"display: none;\">Aggiunto il</td><td style=\"width: 100%;\">Nome File</td><td></td></tr>";
					foreach( $listaBot as $bot )
					{
						if( ($fp = fopen($bot, "r")) )
						{
							if( preg_match_all("/^#(.)+/mi", fread($fp, filesize($bot)), $match) )
							{
								fclose($fp);
								if( $nickname = getNickName($bot) )
								{
									for($i=0; isset($match[0][$i]); $i++)
									{
										if( preg_match_all("/(.+) .+ \[(.+)\] ([0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}) [0-9]{2}:[0-9]{2} (.+)/mi", $match[0][$i], $match1) )
										{
											echo "<tr><td>".htmlentities($nickname)."</td><td style=\"display: none;\">".htmlentities($match1[1][0])."</td><td>".htmlentities($match1[2][0])."</td><td class=\"data\" style=\"display: none;\">".htmlentities(date('d-m-Y', strtotime($match1[3][0])))."</td><td><a class=\"dl\" href=\"#\">".htmlentities($match1[4][0])."</a></td><td><button>Download</button></td></tr>";
										}
									}
								}
							}
						}
					}
				echo "</table>";
			}
		}
		
		public function impostazioniTags()
		{
			if( loggato() )
			{
				echo "<center>";
					echo "<table>";
						echo "<tr>";
							echo "<form method=\"POST\" action=\"accedi.php?act=tags\">";
								echo "<td><b>AGGIUNGI TAG</b></td>";
								echo "<td><input type=\"text\" name=\"nomeVetrina\"></input></td>";
								echo "<td><input type=\"submit\" name=\"do\" value=\"Aggiungi\"></input></td>";
							echo "</form>";
						echo "</tr>";
					echo "</table>";				
				echo "</center>";
				
				if( isset($_POST['nomeVetrina']) && isset($_POST['do']) )
					if( $_POST['do'] == "Aggiungi" )
						$this->aggiungiVetrina($_POST['nomeVetrina']);
				
				if( isset($this->vetrine) )
				{
					if( isset($_GET['do']) )
					{
						if( isset($_GET['nomeVetrina']) )
						{
							if( $_GET['do'] == "elimina" )
							{
								$this->eliminaVetrina($_GET['nomeVetrina']);
							}
							else if( $_GET['do'] == "configura" )
							{
								if( isset($_POST['nomeBot']) && isset($_POST['submit']) )
								{
									if( $_POST['submit'] == "Associa" )
									{
										foreach( $_POST['nomeBot'] as $bot )
										{
											$this->aggiungiBotAllaVetrina($_GET['nomeVetrina'], $bot);
										}
									}
									else if( $_POST['submit'] == "Elimina" )
									{
										foreach( $_POST['nomeBot'] as $bot )
										{
											$this->togliBotAllaVetrina($_GET['nomeVetrina'], botToFile($bot));
										}
									}
								}
							}
						}	
					}	
				}			
								
				if( ($listaVetrine = array_keys($this->vetrine)) )
				{
					//riempio il form
					foreach ($listaVetrine as $nomeVetrina)
					{
						echo "<div class=\"settings\">";
						echo "<fieldset>";
						echo "<legend><b>".$nomeVetrina."</b></legend>";
						
						echo "<table>";
							echo "<td>";
								if( (($botsTotali = botsToArray()) != NULL) )
								{
									echo "<form method=\"POST\" action=\"accedi.php?act=tags&nomeVetrina=".$nomeVetrina."&do=configura\">";
										echo "<table>";
											echo "<tr><th>Lista Bot Disponibili</th></tr>";
											echo "<tr>";
												echo "<td>";
													echo "<select class=\"CONFIG\" multiple=\"multiple\" name=\"nomeBot[]\" size=\"10\">";
														foreach( $botsTotali as $fileNameBot )
															if( !isset($this->vetrine[$nomeVetrina]['listaBot']) || (stripos($this->vetrine[$nomeVetrina]['listaBot'], ":".$fileNameBot.":") === false) )
																echo "<option>".getNickName($fileNameBot)."</option>";
													echo "</select>";
												echo "</td>";
											echo "</tr>";
											echo "<tr>";
												echo "<td>";
												echo "<input type=\"submit\" name=\"submit\" value=\"Associa\">";
												echo "<td>";
											echo "</tr>";
										echo "</table>";
										
										echo "</td><td>";
										
										echo "<table>";
										
											if( ($botsVetrina = $this->botsDellaVetrina($nomeVetrina)) != NULL )
											{
												echo "<tr><th>Lista Bot Associati</th></tr>";
												echo "<tr>";
													echo "<td>";
														echo "<select class=\"CONFIG\" multiple=\"multiple\" name=\"nomeBot[]\" size=\"10\">";
															foreach( $botsVetrina as $fileNameBot )
															{
																if( file_exists($fileNameBot) )
																{
																	echo "<option>".getNickName($fileNameBot)."</option>";
																}
																else
																{
																	$this->togliBotAllaVetrina($nomeVetrina, $fileNameBot);
																	echo "<meta http-equiv=\"Refresh\" content=\"0; url=accedi.php?act=tags\">";
																}
															}
															
														echo "</select>";
													echo "</td>";
												echo "</tr>";
												echo "<tr>";
													echo "<td>";
														echo "<input type=\"submit\" name=\"submit\" value=\"Elimina\">";
													echo "</td>";
												echo "</tr>";
											}
										echo "</table>";
									echo "</form>";
								}
							echo "</td></tr>";
						echo "</table>";
							
						echo "<center><a href=\"accedi.php?act=tags&nomeVetrina=".$nomeVetrina."&do=elimina\">EliminaTags</a></center>";
						echo "</fieldset>";
						echo "</div>";
					}
				}
			}
		}
	}
	
	if( isset($_GET['func']) )
	{
		switch($_GET['func'])
		{
			case '1':
				if( isset($_GET['q']) )
				{
					$Tags = InsiemeTags::creaInsiemeTags();
					$Tags->printTags($_GET['q']);
				}
				break;
		}
	}
	
?>
