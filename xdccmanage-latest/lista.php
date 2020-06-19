<?php 
	require_once('config.php');
	require_once('function.php');

	function print_xdcc_files(&$start)
	{
		$maxbotpervoltatemp = MAXBOTPERVOLTA ? MAXBOTPERVOLTA : PHP_INT_MAX ;
		$countbot = 0;
		
		foreach ( scandir(XDCCDIR) as $item )
		{
			if ( $countbot++ >= $start )
			{
				if( $maxbotpervoltatemp )
				{
					if ( $item == '.' || $item == '..' )
						continue;
					
					$full_path = XDCCDIR."/$item";
					
					if ( is_file($full_path) )
					{
						if( ($fp = fopen($full_path, "r")) )
						{
							if( preg_match_all("/^#(.)+/mi", fread($fp, filesize($full_path)), $match) )
							{
								fclose($fp);
								$maxbotpervoltatemp--;
								
								if( $nickname = getNickName($full_path) )
								{
									echo "<table class=\"lista\">";
										echo "<tr><th colspan=\"4\">".htmlentities($nickname)."</th></tr>";
										echo "<tr class=\"header\">";
											echo "<td>Pack</td>";
											echo "<td>Dim.</td>";
											echo "<td colspan=\"2\" style=\"width: 100%;\">Nome File</td>";
										echo "</tr>";
										
										$concat = "";
										
										for($i=0; isset($match[0][$i]); $i++)
										{
											if( preg_match_all("/(.+) .+ \[(.+)\] [0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2} [0-9]{2}:[0-9]{2} (.+)/mi", $match[0][$i], $match1) )
											{
												echo "<tr>";
													echo "<td>".htmlentities($match1[1][0])."</td>";
													echo "<td>".htmlentities($match1[2][0])."</td>";
													echo "<td><a class=\"dl\" href=\"#\">".htmlentities($match1[3][0])."</a></td>";
													echo "<td><button>Download</button></td>";
												echo "</tr>";
											}
										}
									echo "</table>";
								}
							}
							else
							{
								fclose($fp);
								unlink($full_path);
							}
						}
					}
				}
				else
				{
					$countbot--;
					break;
				}
			}
		}
		$start = $countbot;
		return $maxbotpervoltatemp;
	}

	function search_into_xdcc_files($str)
	{
		$rows = 0;
		
		if( ($str = preg_replace('/[^a-zA-Z0-9 {2,}]/', ' ', $str)) )
		{
			if( strlen(trim($str)) > 2) //se la stringa non contiene solo spazi
			{
				echo "<table class=\"lista\">";
					echo "<tr class=\"header\"><td>Bot</td><td style=\"display: none;\">Pack</td><td>Dim.</td><td style=\"display: none;\">Aggiunto il</td><td colspan=\"2\" style=\"width: 100%;\">Nome File</td></tr>";

					foreach ( scandir(XDCCDIR) as $item )
					{
						if ( $item == '.' || $item == '..' )
							continue;
							
						$full_path = XDCCDIR."/$item";
						
						if ( is_file($full_path) )
						{
							if( ($fp = fopen($full_path, "r")) )
							{
								if( $nickname = getNickName($full_path) )
								{
									$ricercaTemp = $str;
									$ricercaTemp = strtok($ricercaTemp, " ");
									
									if( $ricercaTemp !== false )
									{
										if( preg_match_all("/^#(.*)](.*)".$ricercaTemp."(.*)/mi", fread($fp, filesize($full_path)), $match) )
										{
											for($i=0; isset($match[0][$i]); $i++)
											{
												$contatore1 = $contatore2 = 0;
												
												while( ($ricercaTemp = strtok(" ")) !== false )
												{
													$contatore1++;
													if( stripos($match[0][$i], $ricercaTemp) !== false )
														$contatore2++;
												}

												if( $contatore1 == $contatore2 )
												{
													if( preg_match_all("/(.+) .+ \[(.+)\] ([0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}) [0-9]{2}:[0-9]{2} (.+)/mi", $match[0][$i], $match1) )
													{
														if( date('Y-m-d', strtotime($match1[4][0])) == date('Y-m-d') )
															echo "<tr><td>".htmlentities($nickname)."</td><td style=\"display: none;\">".htmlentities($match1[1][0])."</td><td>".htmlentities($match1[2][0])."</td><td class=\"dataOggi\" style=\"display: none;\">".htmlentities(date('d-m-Y', strtotime($match1[3][0])))."</td><td><a class=\"dl\" href=\"#\">".htmlentities($match1[4][0])."</a></td><td><button>Download</button></td></tr>";
														else
															echo "<tr><td>".htmlentities($nickname)."</td><td style=\"display: none;\">".htmlentities($match1[1][0])."</td><td>".htmlentities($match1[2][0])."</td><td class=\"data\" style=\"display: none;\">".htmlentities(date('d-m-Y', strtotime($match1[3][0])))."</td><td><a class=\"dl\" href=\"#\">".htmlentities($match1[4][0])."</a></td><td><button>Download</button></td></tr>";
														$rows++;
													}
												}
												$ricercaTemp = $str;
												$ricercaTemp = strtok($ricercaTemp, " ");
											}
										}
										fclose($fp);
									}
								}
							}
						}
					}
				echo "</table>";
			}
		}
		return $rows;
	}
	
	function ultimiaggiunti()
	{
		foreach ( scandir(XDCCDIR) as $item )
		{
			if ( $item == '.' || $item == '..' )
				continue;
				
			$full_path = XDCCDIR."/$item";
			
			if ( is_file($full_path) )
			{
				if( ($fp = fopen($full_path, "r")) )
				{
					$oggi = date("Y-m-d");
					$ieri = date("Y-m-d", mktime(0,0,0,date("m"),date("d")-1,date("Y")));
					$altroieri = date("Y-m-d", mktime(0,0,0,date("m"),date("d")-2,date("Y")));
					
					if( preg_match_all("/.+ .+ \[(.+)\] ($oggi|$ieri|$altroieri) [0-9]{2}:[0-9]{2} (.+)/mi", fread($fp, filesize($full_path)), $match) )
					{
						if( isset($match[0][0]) )
						{
							for($i=0; isset($match[0][$i]); $i++)
							{
								if( $match[2][$i] == $oggi )
								{
									$files['oggi'][] = $match[1][$i];
									$files['oggi'][] = $match[3][$i];
								}
								elseif( $match[2][$i] == $ieri )
								{
									$files['ieri'][] = $match[1][$i];
									$files['ieri'][] = $match[3][$i];
								}
								elseif( $match[2][$i] == $altroieri )
								{
									$files['altroieri'][] = $match[1][$i];
									$files['altroieri'][] = $match[3][$i];
								}
							}
						}
					}
					fclose($fp);
				}
			}
		}
		
		if( isset($files['oggi']) )
		{
			echo "<table class=\"lista\">";
				echo "<tr><th colspan=\"2\"><center><u>O G G I</u></center></th></tr><tr class=\"header\"><td>Dimensione</td><td style=\"width: 100%;\">Nome del file</td></tr>";
				$i = 0;
				foreach( $files['oggi'] as $file )
				{
					if( ($i++%2) == 0 )
						echo "<tr><td>".$file."</td>";
					else
						echo "<td style=\"width: 100%;\"><a class=\"ricerca\" href=\"#\">".htmlentities($file)."</a></td></tr>";
				}
			echo "</table>";
		}
		
		if( isset($files['ieri']) )
		{
			echo "<table class=\"lista\">";
				echo "<tr><th colspan=\"2\"><center><u>I E R I</u></center></th></tr><tr class=\"header\"><td>Dimensione</td><td style=\"width: 100%;\">Nome del file</td></tr>";
				$i = 0;
				foreach( $files['ieri'] as $file )
				{
					if( ($i++%2) == 0 )
						echo "<tr><td>".$file."</td>";
					else
						echo "<td style=\"width: 100%;\"><a class=\"ricerca\" href=\"#\">".htmlentities($file)."</a></td></tr>";
				}
			echo "</table>";
		}
		
		if( isset($files['altroieri']) )
		{
			echo "<table class=\"lista\">";
				echo "<tr><th colspan=\"2\"><center><u>L ' A L T R O &nbsp; I E R I</u></center></th></tr><tr class=\"header\"><td>Dimensione</td><td style=\"width: 100%;\">Nome del file</td></tr>";
				$i = 0;
				foreach( $files['altroieri'] as $file )
				{
					if( ($i++%2) == 0 )
						echo "<tr><td>".$file."</td>";
					else
						echo "<td style=\"width: 100%;\"><a class=\"ricerca\" href=\"#\">".htmlentities($file)."</a></td></tr>";
				}
			echo "</table>";
		}
	}
	
	function piuscaricati()
	{
		$j = 0;		
		foreach ( scandir(XDCCDIR) as $item )
		{
			if ( $item == '.' || $item == '..' )
				continue;
				
			$full_path = XDCCDIR."/$item";
			
			if ( is_file($full_path) )
			{
				if( ($fp = fopen($full_path, "r")) )
				{
					if( preg_match_all("/ ([0-9]+)x .+ [0-9]{2}:[0-9]{2} (.+)/mi", fread($fp, filesize($full_path)), $match) )
					{
						
						if( isset($match[0][0]) )
						{
							for($i=0; isset($match[0][$i]); $i++)
							{
								$dl[$j] = $match[1][$i];
								$files[$j++] = $match[2][$i];
							}
						}
					}
					fclose($fp);
				}
			}
		}
		
		if( isset($dl) )
		{
			array_multisort($dl, SORT_DESC, $files);
			
			echo "<table class=\"lista\">";
				echo "<tr class=\"header\"><td>Posizione</td><td>Download</td><td style=\"width: 100%;\">Nome del file</td></tr>";
				for($i = 0; ($i < 50) && isset($dl[$i]); $i++)
				{
					echo "<tr>";
						if( $i < 9 ) echo "<td><center><span style=\"padding-left: 7px; padding-right: 7px;\">".($i+1)."</span></center></td>";
						else echo "<td><center><span>".($i+1)."</span></center></td>";
						echo "<td>".$dl[$i]."</td>";
						echo "<td style=\"width: 100%;\"><a class=\"ricerca\" href=\"#\">".htmlentities($files[$i])."</a></td>";
					echo "</tr>";
				}
			echo "</table>";
		}
	}
	
	if( isset($_GET['func']) )
	{
		switch($_GET['func'])
		{
			case '1':
				if( isset($_GET['q']) ) 
					search_into_xdcc_files($_GET['q']);
				break;
				
			case '2':
				if( isset($_GET['q']) )
				{
					$start = $_GET['q'];
					if( print_xdcc_files($start) == 0 )
						echo "<form id=\"altririsultati\" action=\"?start=$start\"><input type=\"submit\" value=\"Gli altri file\" style=\"width: 300px; height: 40px; \"></input></form>";
				}
				break;
			
			case '3':
				ultimiaggiunti();
				break;
			
			case '4':
				piuscaricati();
				break;
		}
	}
	else
	{
		$start = 0;
		if( print_xdcc_files($start) == 0 )
			echo "<form id=\"altririsultati\" action=\"?start=$start\"><input type=\"submit\" value=\"Gli altri file\"></input></form>";
	}
	
?>
