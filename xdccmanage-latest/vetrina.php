<?php 

	require_once('config.php');
	require_once('function.php');

	class InsiemeVetrine
	{
		private $vetrine;
		private static $istanza;
		
		public static function creaInsiemeVetrine()
		{
			if( isset($istanza) )
			{
				return InsiemeVetrine::$istanza;
			}
			else if( is_file(STATOVETRINA) )
			{
				if( ($fp = fopen(STATOVETRINA, "r")) )
				{
					if( (InsiemeVetrine::$istanza = unserialize(fread($fp, filesize(STATOVETRINA)))) === false )
						InsiemeVetrine::$istanza = new InsiemeVetrine();

					fclose($fp);
					return InsiemeVetrine::$istanza;
				}
			}
			else
			{
				InsiemeVetrine::$istanza = new InsiemeVetrine();
				return InsiemeVetrine::$istanza;
			}
		}

		public function eliminaVetrine()
		{
			if( (is_file(STATOVETRINA) === true) && (unlink(STATOVETRINA) === true) )
				if( (is_file(VETRINACONF) === true) && (unlink(VETRINACONF) === true) )
					InsiemeVetrine::$istanza = NULL;
		}
		
		private function __construct()
		{
			$vetrine = array();
			@unlink(VETRINACONF);
		}
		
		// aggiunge una vetrina
		public function aggiungiVetrina($nomeVetrina)
		{
			if( !isset($this->vetrine[$nomeVetrina]) )
			{
				$this->vetrine[$nomeVetrina] = array();
				$this->salvaStatoInsiemeVetrine();
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
				$this->salvaStatoInsiemeVetrine();
				
				if( (is_file(VETRINACONF) === true) && unlink(VETRINACONF) === true ) 
					$this->salvaVetrineSuFile();
				
				return true;
			}
			return false;
		}
		
		// svuota dagli elementi la vetrina passata
		public function svuotaVetrina($nomeVetrina)
		{
			if( isset($this->vetrine[$nomeVetrina]) )
			{
				unset($this->vetrine[$nomeVetrina]['listaElem']);
				$this->salvaStatoInsiemeVetrine();
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
						$this->salvaStatoInsiemeVetrine();
						return true;
					}
					else if( stripos($this->vetrine[$nomeVetrina]['listaBot'], ":".$filename.":") === false )		//se è definita listaBot controllo che nomeBot non esista, e allora posso concatenarlo alla listaBot esistente
					{
						$this->vetrine[$nomeVetrina]['listaBot'] .= $filename.":";
						$this->salvaStatoInsiemeVetrine();
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
						$this->caricaElementiVetrina($nomeVetrina);
					}
					$this->salvaStatoInsiemeVetrine();
					$this->salvaVetrineSuFile();
					return true;
				}
			}
			return false;
		}
		
		// aggiunge un elemento alla vetrina
		public function aggiungiElementoAllaVetrina($nomeVetrina, $nomeElemento)
		{
			if( isset($this->vetrine[$nomeVetrina]) )
			{
				$data = preg_replace('/:/', '-', substr($nomeElemento, 0, 16));
				$filename = substr($nomeElemento, 17);
				
				if( FORMATOTITOLI == "nopunti" )
				{
					$filename = preg_replace('/[^a-zA-Z0-9]/', ' ', $filename);
					$filename = preg_replace('/ {2,}/', ' ', $filename);
					$filename = preg_replace('/^ /', '', $filename);
				}
				elseif( FORMATOTITOLI == "notag" )
				{
					$filename = preg_replace('/[^a-zA-Z0-9]/', ' ', $filename);
					$filename = preg_replace('/ {2,}/', ' ', $filename);
					$filename = preg_replace('/^ /', '', $filename);
					if( preg_match('/(.*[12][0-9]{3}).*/', $filename, $matches) || preg_match('/(.*)italian.*/i', $filename, $matches) || preg_match('/(.*)ita.*/i', $filename, $matches) )
						$filename = $matches[1];
				}
					
				$nomeElemento = $data." # ".$filename;
				
				if( !isset($this->vetrine[$nomeVetrina]['listaElem']) )
				{
					$this->vetrine[$nomeVetrina]['listaElem'] = ":".$nomeElemento.":";
					$this->salvaStatoInsiemeVetrine();
					return true;
				}
				//nel caso in cui esistono già elementi , controllo che l'elemento passato non esista, e che il numero di elementi sia <= al numero di elementi consentito, per aggiungere l'elemento passato
				else if( (stripos($this->vetrine[$nomeVetrina]['listaElem'], "# ".$filename.":") === false) && (substr_count($this->vetrine[$nomeVetrina]['listaElem'], ":") <= MAXELEMPERVETRINA) )
				{
					$this->vetrine[$nomeVetrina]['listaElem'] .= $nomeElemento.":";
					$this->salvaStatoInsiemeVetrine();
					return true;
				}
			}
			return false;
		}

		// ridimensiona dal numero di elementi la vetrina
		public function rifaiVetrine()
		{
			if( ($listaVetrine = array_keys($this->vetrine)) )
				foreach ($listaVetrine as $nomeVetrina)
					unset($this->vetrine[$nomeVetrina]['lastMod']);
			
			$this->salvaStatoInsiemeVetrine();
		}

		public function caricaElementiVetrine()
		{
			if( ($listaVetrine = array_keys($this->vetrine)) )
				foreach ($listaVetrine as $nomeVetrina)
					$this->caricaElementiVetrina($nomeVetrina);
			
			$this->salvaStatoInsiemeVetrine();
			$this->salvaVetrineSuFile();
		}
		
		public function caricaElementiVetrina($nomeVetrina)
		{
			if( isset($this->vetrine[$nomeVetrina]['listaBot']) )
			{
				if( empty($this->vetrine[$nomeVetrina]['lastMod']) )
					$this->vetrine[$nomeVetrina]['lastMod'] = "";
				
				$listaBot = $this->vetrine[$nomeVetrina]['listaBot'];
				$fileNameBot = strtok($listaBot, ":");
				while( $fileNameBot !== false )
				{
					$lastMod .= filemtime($fileNameBot);
					$fileNameBot = strtok(":");
				}
				
				if( $this->vetrine[$nomeVetrina]['lastMod'] != $lastMod )
				{
					$this->vetrine[$nomeVetrina]['lastMod'] = $lastMod;
					
					if( isset($this->vetrine[$nomeVetrina]['listaElem']) ) 
						unset($this->vetrine[$nomeVetrina]['listaElem']);
			
					$listaBot = $this->vetrine[$nomeVetrina]['listaBot'];
					$fileNameBot = strtok($listaBot, ":");
					
					while( $fileNameBot !== false )
					{
						if( file_exists($fileNameBot) )
						{
							if( ($fp = fopen($fileNameBot, "r")) )
							{
								if( preg_match_all("/] ([0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}) ([0-9]{2}:[0-9]{2}) (.+)/mi", fread($fp, filesize($fileNameBot)), $match) )
								{
									for($i=(count($match[1])-1); $i>=0; $i--)
									{
										$files[] = $match[1][$i]." ".$match[2][$i]." ".$match[3][$i];
									}
								}
							}
						}
						$fileNameBot = strtok(":");
					}
					
					if( !is_array($files) )
						return;
					
					sort($files);
					
					$numeroElementi = MAXELEMPERVETRINA;
										
					for($i=(count($files)-1); ($numeroElementi>0) && ($i>=0); $i--)
					{
						$poscor = 0;
						$posdef = 255;
						$filei = substr($files[$i], 17);
						
						for($j=(count($files)-1); $j>=0; $j--)
						{
							$filej = substr($files[$j], 17);
							if( $filei != $filej )
							{
								if( ($poscor = $this->similStringhe($filei, $filej)) )
								{
									if( $poscor<$posdef )
									{
										$posdef = $poscor;
									}
								}
							}
						}
						
						if( $this->aggiungiElementoAllaVetrina($nomeVetrina, substr($files[$i], 0, 17+$posdef)) == true )
							$numeroElementi--;
					}
				}
			}
		}
		
		/* restituisce 0 se le due stringhe sono diverse, > 0 se sono simili */
		public function similStringhe($str1, $str2)
		{
			if( strlen($str1) == strlen($str2) )
			{
				$strlen = strlen($str1);
				$temp1 = $temp2 = 0;
			
				for($i=0; $i<$strlen; $i++)
				{
					if( $str1[$i] == $str2[$i] )
						$temp1++;
					else
						break;
				}
				
				for($i=($strlen-1); $i>$temp1; $i--)
				{
					if( $str1[$i] == $str2[$i] )
						$temp2++;
					else
						break;
				}
				
				if( abs($strlen-($temp1+$temp2)) <= 2 )
					if( is_numeric(substr($str1, $temp1, abs($strlen-($temp1+$temp2)))) )
						return $temp1;
				
			}
			return 0;
		}
		
		/* fa una copia su file dell'oggetto */
		public function salvaStatoInsiemeVetrine()
		{
			if( $fp = fopen(STATOVETRINA, "w") )
				fwrite($fp, serialize(InsiemeVetrine::$istanza));
				
			fclose($fp);
		}
		
		/* salva la configurazione delle vetrine su un file di testo */
		public function salvaVetrineSuFile()
		{
			if( is_file(VETRINACONF) !== false )
				unlink(VETRINACONF);

			if( $fp = fopen(VETRINACONF, "w+") )
			{
				if( ($listaVetrine = array_keys($this->vetrine)) )
				{
					foreach ($listaVetrine as $nomeVetrina)
					{
						if( isset($this->vetrine[$nomeVetrina]['listaElem']) )
						{	
							$files = $nomeVetrina."|";
							
							$file = strtok($this->vetrine[$nomeVetrina]['listaElem'], ":");
							while( $file !== false )
							{
								$files .= ":".$file;
								$file = strtok(":");
							}
							
							$files .= ":\r\n";
							fwrite($fp, $files);
						}
					}
					fclose($fp);
				}
			}
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
		
		public function warning($message)
		{
			echo "<p class=\"warning\">".$message."</p>";
		}
		
		public function mostraVetrina()
		{	
			//non considero le vetrine senza elementi
			if( isset($this->vetrine) )
			{
				if( ($listaVetrineTemp = array_keys($this->vetrine)) )
				{
					$this->caricaElementiVetrine();
					
					foreach( $listaVetrineTemp as $vetrina )
						if( !empty($this->vetrine[$vetrina]['listaElem']) )
							$listaVetrine[] = $vetrina;
					
					if( !isset($listaVetrine) ) return;
					
					$count = 0;
					$righe = (int)(count($listaVetrine)/NUMVETRINEPERRIGA);
					$resto = count($listaVetrine)%NUMVETRINEPERRIGA;
					
					//creo matrice , dove la riga n identifica la vetrina n-esima, la colonna m il file m-esimo, e all'incrocio il file m-esimo della vetrina n-esima
					for($i = 0; $i < count($listaVetrine); $i++)
					{
						$j=0;
						$tmp[$i][] = strtok($this->vetrine[$listaVetrine[$i]]['listaElem'], ":");
						while( $tmp[$i][$j] !== false )
						{
							$tmp[$i][] = strtok(":");
							$j++;
						}
					}
					
					while( $righe > 0 )
					{
						echo "<table class=\"vetrina\" cellspacing=\"0\" cellpadding=\"0\">";
							echo "<tr>";
								for($i = 0; $i < NUMVETRINEPERRIGA; $i++)
								{
									if( NUMVETRINEPERRIGA == 3 )
										echo "<td width=\"33%\" class=\"titolovetrina\" colspan=\"2\">".$listaVetrine[(NUMVETRINEPERRIGA*$count)+$i]."</td>";
									else if( NUMVETRINEPERRIGA == 4 )
										echo "<td width=\"25%\" class=\"titolovetrina\" colspan=\"2\">".$listaVetrine[(NUMVETRINEPERRIGA*$count)+$i]."</td>";
									else if( NUMVETRINEPERRIGA == 5 )
										echo "<td width=\"20%\" class=\"titolovetrina\" colspan=\"2\">".$listaVetrine[(NUMVETRINEPERRIGA*$count)+$i]."</td>";
								}
							echo "</tr>";
							
							for($i = 0; $i < MAXELEMPERVETRINA; $i++)
							{
								echo "<tr>";
									for($j = 0; $j < NUMVETRINEPERRIGA; $j++)
									{
										if( !empty($tmp[(NUMVETRINEPERRIGA*$count)+$j][$i]) )
										{
											$nomeElemento = $tmp[(NUMVETRINEPERRIGA*$count)+$j][$i];
											
											if( ($data = substr($nomeElemento, 0, 10)) !== false )
												echo "<td class=\"datavetrina\"><span class=\"day\">".htmlentities(date("d", strtotime($data)))."</span><br><span class=\"month\">".htmlentities(engtoita(date("M", strtotime($data))))."</span></td>";
											else
												echo "<td class=\"datavetrina\"></td>";
											
											if( NUMVETRINEPERRIGA == 3 )
												echo "<td width=\"33%\"><a class=\"titolo ricerca pos".$i."\" href=\"#\">".htmlentities(substr($nomeElemento, 19))."</a>";
											else if( NUMVETRINEPERRIGA == 4 )
												echo "<td width=\"25%\"><a class=\"titolo ricerca pos".$i."\" href=\"#\">".htmlentities(substr($nomeElemento, 19))."</a>";
											else if( NUMVETRINEPERRIGA == 5 )
												echo "<td width=\"20%\"><a class=\"titolo ricerca pos".$i."\" href=\"#\">".htmlentities(substr($nomeElemento, 19))."</a>";
											
											if( (INFO == "si") && (($count == 0) && ($j == 0)) )
												echo "<div><a class=\"trailer\" href=\"#\"><b>T</b>&nbsp;R&nbsp;A&nbsp;I&nbsp;L&nbsp;E&nbsp;R</a></div>";
											
											echo "</td>";
										}
										else
										{
											echo "<td></td><td></td>";
										}
									}
								echo "</tr>";
							}
						echo "</table>";
						
						$righe--;
						$count++;
					}
				
					if( $resto > 0 )
					{
						echo "<table class=\"vetrina\" cellspacing=\"0\" cellpadding=\"0\">";
							echo "<tr>";
								
								for($i = 0; $i < $resto; $i++)
								{
									if( NUMVETRINEPERRIGA == 3 )
										echo "<td width=\"33%\" class=\"titolovetrina\" colspan=\"2\">".$listaVetrine[(NUMVETRINEPERRIGA*$count)+$i]."</td>";
									else if( NUMVETRINEPERRIGA == 4 )
										echo "<td width=\"25%\" class=\"titolovetrina\" colspan=\"2\">".$listaVetrine[(NUMVETRINEPERRIGA*$count)+$i]."</td>";
									else if( NUMVETRINEPERRIGA == 5 )
										echo "<td width=\"20%\" class=\"titolovetrina\" colspan=\"2\">".$listaVetrine[(NUMVETRINEPERRIGA*$count)+$i]."</td>";	
								}
								for($resto1 = $resto; $resto1 < NUMVETRINEPERRIGA; $resto1++)
								{
									if( NUMVETRINEPERRIGA == 3 )
										echo "<td width=\"33%\" class=\"padding\"></td>";
									else if( NUMVETRINEPERRIGA == 4 )
										echo "<td width=\"25%\" class=\"padding\"></td>";
									else if( NUMVETRINEPERRIGA == 5 )
										echo "<td width=\"20%\" class=\"padding\"></td>";
								}
								
							echo "</tr>";
							
							for($i = 0; $i < MAXELEMPERVETRINA; $i++)
							{
								echo "<tr>";
									
									for($j = 0; $j < $resto; $j++)
									{
										if( !empty($tmp[(NUMVETRINEPERRIGA*$count)+$j][$i]) )
										{
											$nomeElemento = $tmp[(NUMVETRINEPERRIGA*$count)+$j][$i];
											
											if( ($data = substr($nomeElemento, 0, 10)) !== false )
												echo "<td class=\"datavetrina\"><span class=\"day\">".htmlentities(date("d", strtotime($data)))."</span><br><span class=\"month\">".htmlentities(engtoita(date("M", strtotime($data))))."</span></td>";
											else
												echo "<td class=\"datavetrina\"></td>";
											
											if( NUMVETRINEPERRIGA == 3 )
												echo "<td width=\"33%\"><a class=\"titolo ricerca pos".$i."\" href=\"#\">".htmlentities(substr($nomeElemento, 19))."</a>";
											else if( NUMVETRINEPERRIGA == 4 )
												echo "<td width=\"25%\"><a class=\"titolo ricerca pos".$i."\" href=\"#\">".htmlentities(substr($nomeElemento, 19))."</a>";
											else if( NUMVETRINEPERRIGA == 5 )
												echo "<td width=\"20%\"><a class=\"titolo ricerca pos".$i."\" href=\"#\">".htmlentities(substr($nomeElemento, 19))."</a>";
											
											if( (INFO == "si") && (($count == 0) && ($j == 0)) )
												echo "<div><a class=\"trailer\" href=\"#\"><b>T</b>&nbsp;R&nbsp;A&nbsp;I&nbsp;L&nbsp;E&nbsp;R</a></div>";
											
											echo "</td>";
										}
										else
											echo "<td></td><td></td>";
										
									}
									for($resto1 = $resto; $resto1 < NUMVETRINEPERRIGA; $resto1++)
									{
										if( NUMVETRINEPERRIGA == 3 )
											echo "<td width=\"33%\" class=\"padding\" align=\"center\" width=\"20%\"></td>";
										else if( NUMVETRINEPERRIGA == 4 )
											echo "<td width=\"25%\" class=\"padding\" align=\"center\" width=\"20%\"></td>";
										else if( NUMVETRINEPERRIGA == 5 )
											echo "<td width=\"20%\" class=\"padding\" align=\"center\" width=\"20%\"></td>";
									}
								echo "</tr>";
							}
						echo "</table>";
					}
				}
			}
		}
		
		public function impostazioniVetrina()
		{
			if( loggato() )
			{
				echo "<center>";
					echo "<table>";
						echo "<tr>";
							echo "<form method=\"POST\" action=\"accedi.php?act=vetrina\">";
								echo "<td><b>AGGIUNGI VETRINA</b></td>";
								echo "<td><input type=\"text\" name=\"nomeVetrina\"></input></td>";
								echo "<td><input type=\"submit\" name=\"do\" value=\"Aggiungi\"></input></td>";
							echo "</form>";
						echo "</tr>";
					echo "</table>";				
				echo "</center>";
				
				if( isset($_POST['nomeVetrina']) && isset($_POST['do']) )
					if( $_POST['do'] == "Aggiungi" )
						if( $this->aggiungiVetrina($_POST['nomeVetrina']) == false )
							$this->warning("Vetrina <b>".$_POST['nomeVetrina']."</b> già esistente");

				if( isset($this->vetrine) )
				{
					if( isset($_GET['do']) )
					{
						if( isset($_GET['nomeVetrina']) )
						{
							if( $_GET['do'] == "elimina" )
							{
								if( $this->eliminaVetrina($_GET['nomeVetrina']) == false )
								{
									$this->warning("Vetrina <b>".$_GET['nomeVetrina']."</b> inesistente");
								}
							}
							else if( $_GET['do'] == "configura" )
							{
								if( isset($_POST['nomeBot']) && isset($_POST['submit']) )
								{
									if( $_POST['submit'] == "Associa" )
									{
										foreach( $_POST['nomeBot'] as $bot )
										{
											if( !$this->aggiungiBotAllaVetrina($_GET['nomeVetrina'], $bot) )
											{
												$this->warning("Vetrina <b>".$_GET['nomeVetrina']."</b> o Bot <b>".$_POST['nomeBot']."</b> inesistente");
											}
										}
										$this->caricaElementiVetrina($_GET['nomeVetrina']);
									}
									else if( $_POST['submit'] == "Elimina" )
									{
										foreach( $_POST['nomeBot'] as $bot )
										{
											if( !$this->togliBotAllaVetrina($_GET['nomeVetrina'], botToFile($bot)) == true )
											{
												$this->warning("Vetrina <b>".$_GET['nomeVetrina']."</b> o Bot <b>".$bot."</b> inesistente.");
											}
										}
										$this->caricaElementiVetrina($_GET['nomeVetrina']);
									}
								}
							}
							else if( $_GET['do'] == "salva" )
							{
								if( isset($_POST[0]) )
								{
									if( $this->svuotaVetrina($_GET['nomeVetrina']) == true )
									{
										for($i=0; $i<MAXELEMPERVETRINA; $i++)
										{
											if( isset($_POST["$i"]) && !empty($_POST["$i"]) )
											{
												if( !preg_match("/([0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}) ([0-9]{2}\-[0-9]{2}) (.+)/mi", $_POST["$i"]) )
													$this->aggiungiElementoAllaVetrina($_GET['nomeVetrina'], date("Y-m-d H:i") . " " . $_POST["$i"]);
												else
													$this->aggiungiElementoAllaVetrina($_GET['nomeVetrina'], substr($_POST["$i"], 0, 16)."".substr($_POST["$i"], 18) );
											}
										}
										
										if( isset($_POST['nuovoNomeVetrina']) && ($_GET['nomeVetrina'] != $_POST['nuovoNomeVetrina']) )
										{
											if( !isset($this->vetrine[$_POST['nuovoNomeVetrina']]) )
											{
												$tempVetrine = $this->vetrine;
												$this->vetrine = array();
												
												if( ($listaVetrine = array_keys($tempVetrine)) )
												{
													foreach ($listaVetrine as $nomeVetrina)
													{
														if( $nomeVetrina == $_GET['nomeVetrina'] )
														{
															$this->vetrine[$_POST['nuovoNomeVetrina']] = array();
															if( isset($tempVetrine[$nomeVetrina]['listaElem']) )
																$this->vetrine[$_POST['nuovoNomeVetrina']]['listaElem'] = $tempVetrine[$nomeVetrina]['listaElem'];
															if( isset($tempVetrine[$nomeVetrina]['listaBot']) )
																$this->vetrine[$_POST['nuovoNomeVetrina']]['listaBot'] = $tempVetrine[$nomeVetrina]['listaBot'];
														}
														else
														{
															$this->vetrine[$nomeVetrina] = array();
															if( isset($tempVetrine[$nomeVetrina]['listaElem']) )
																$this->vetrine[$nomeVetrina]['listaElem'] = $tempVetrine[$nomeVetrina]['listaElem'];
															if( isset($tempVetrine[$nomeVetrina]['listaBot']) )
																$this->vetrine[$nomeVetrina]['listaBot'] = $tempVetrine[$nomeVetrina]['listaBot'];
														}
													}
													$this->salvaStatoInsiemeVetrine();
												}
											}
											else
												$this->warning("Linea ".__linee__.": vetrina <u>".$_POST['nuovoNomeVetrina']."</u> esistente");
										}
										$this->salvaVetrineSuFile();
									}
									else
										$this->warning("Linea ".__line__.": vetrina <u>".$_GET['nomeVetrina']."</u> inesistente");
								}
							}
						}
						else if( $_GET['do'] == "aggiornamento" )
						{
							$this->caricaElementiVetrine();
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
								echo "<tr><td>";
									echo "<table>";
										echo "<form method=\"POST\" action=\"accedi.php?act=vetrina&nomeVetrina=".$nomeVetrina."&do=salva\">";
											echo "<tr><th>Nome: <input type=\"text\" name=\"nuovoNomeVetrina\" value=\"".$nomeVetrina."\"></input></th></tr>";
												
											$count = 0;
											if( isset($this->vetrine[$nomeVetrina]['listaElem']) )
											{
												$temp = $this->vetrine[$nomeVetrina]['listaElem'];
												$token = strtok($temp, ":");
												
												while( $token !== false )
												{
													if( isset($this->vetrine[$nomeVetrina]['listaBot']) )
														echo "<tr><td>Elem ".$count.": <input type=\"text\" value=\"".$token."\" name=\"".$count++."\" readonly></input></td></tr>";
													else
														echo "<tr><td>Elem ".$count.": <input type=\"text\" value=\"".$token."\" name=\"".$count++."\"></input></td></tr>";
													$token = strtok(":");
												}
											}
											
											while($count<MAXELEMPERVETRINA)
											{
												if( isset($this->vetrine[$nomeVetrina]['listaBot']) )
													echo "<tr><td>Elem ".$count.": <input type=\"text\" name=\"".$count++."\" readonly></input></td></tr>";
												else
													echo "<tr><td>Elem ".$count.": <input type=\"text\" name=\"".$count++."\"></input></td></tr>";
											}
											
											if( !isset($this->vetrine[$nomeVetrina]['listaBot']) )
												echo "<tr><td><input align=\"right\" type=\"submit\" value=\"Salva\" name=\"".$nomeVetrina."\"></input></td></tr>";
										
										echo "</form>";
									echo "</table>";
								echo "</td>";
								echo "<td>";
									if( (($botsTotali = botsToArray()) != NULL) )
									{
										echo "<form method=\"POST\" action=\"accedi.php?act=vetrina&nomeVetrina=".$nomeVetrina."&do=configura\">";
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
																		$this->caricaElementiVetrina($nomeVetrina);
																		echo "<meta http-equiv=\"Refresh\" content=\"0; url=accedi.php?act=vetrina\">";
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
								
							echo "<center><a href=\"accedi.php?act=vetrina&nomeVetrina=".$nomeVetrina."&do=elimina\">EliminaVetrina</a></center>";
							echo "</fieldset>";
							echo "</div>";
						}
					}
				}
			}
		}
	}
?>
