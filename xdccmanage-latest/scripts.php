<?php 

	require_once('config.php');
	require_once('function.php');

	function print_xdcc_files($dir)
	{
		foreach ( scandir($dir) as $item )
		{
			if ( $item == '.' || $item == '..' )
				continue;
				
			$full_path = "$dir/$item";
			
			if ( is_file($full_path) )
			{
				if( ($fp = fopen($full_path, "r")) )
				{
					if( preg_match_all("/^#(.)+/mi", fread($fp, filesize($full_path)), $match) )
					{
						fclose($fp);
						if( $nickname = getNickName($full_path) )
						{
							for($i=0; isset($match[0][$i]); $i++)
							{
								if( preg_match_all("/(.+) (.+) \[(.+)\] ([0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}) ([0-9]{2}:[0-9]{2}) (.+)/mi", $match[0][$i], $match1) )
								{
									echo "\r\n".$match1[1][0]." ".$nickname." ".$match1[3][0]." ".$match1[6][0];
								}
							}
						}
					}
				}
			}
			else
				print_xdcc_files($full_path);
		}
	}
	
	function search_into_xdcc_files($dir, $str)
	{
		foreach ( scandir($dir) as $item )
		{
			if ( $item == '.' || $item == '..' )
				continue;
				
			$full_path = "$dir/$item";
			
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
							if( preg_match_all("/^#(.*)](.*)".$ricercaTemp."(.*)/mi", fread($fp, filesize($full_path)), $match) )  //tutte le righe che contengono la prima parola di $_POST['cerca']
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
										if( preg_match_all("/(.+) (.+) \[(.+)\] ([0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}) ([0-9]{2}:[0-9]{2}) (.+)/mi", $match[0][$i], $match1) )
										{
											echo "\r\n".$match1[1][0]." ".$nickname." ".$match1[3][0]." ".$match1[6][0];
										}
									}
									$ricercaTemp = $str;
									$ricercaTemp = strtok($ricercaTemp, " ");
								}
							}
						}
						fclose($fp);
					}
				}
			}
			else
				search_into_xdcc_files($full_path, $str);
		}
	}
	
	if( isset($_GET['q']) )
	{
		if( (stripos($_SERVER['HTTP_USER_AGENT'], "PoWeR-Script") !== false) && ($_GET['q'] == ".") )
			print_xdcc_files(XDCCDIR);
		else
		{
			if( $ricerca = preg_replace('/[^a-zA-Z0-9 {2,}]/', ' ', $_GET['q']) )
				if( strlen(trim($ricerca)) >= 2) //se la stringa non contiene solo spazi
					search_into_xdcc_files(XDCCDIR, $ricerca);
		}
	}
	else
		print_xdcc_files(XDCCDIR);

?>
