<?php 

	require_once('config.php');

	/* restituisce true se si è loggati come amministratori */
	function loggato()
	{
		if( isset($_SESSION['user']) && ($_SESSION['user'] == USER) && isset($_SESSION['userip']) && ($_SESSION['userip'] == $_SERVER['REMOTE_ADDR']) ) return true;
		else return false;
	}
	
	function esci()
	{
		if( loggato() ) $_SESSION = array();
	}

	/* se il bot esiste restituisce il nome del file, altrimenti NULL */
	function botToFile($nomeBot)
	{
		if( $natSort = filesNatSort(XDCCDIR) )
			foreach( $natSort as $file )
				if( getNickName($file) == $nomeBot )
					return $file;
		
		return NULL;
	}

	/* restituisce un array con la lista dei bots presenti nella dir XDCC */
	function botsToArray()
	{
		$bots = NULL;
		
		if( $natSort = filesNatSort(XDCCDIR) )
			foreach( $natSort as $file )
				$bots[] = $file;
				
		return $bots ? $bots : NULL;
	}

	/* restituisce un array contenente i file presenti nella dir e nelle sottodir della dir passata come argomento, ordinati per ordine naturale */
	function filesNatSort($folder)
	{
		$output = array();
		foreach ( scandir($folder) as $item )
		{
			if ( $item == '.' || $item == '..' )
				continue;
			$full_path = "$folder/$item";
			if ( is_dir($full_path) )
			{
				$temp = filesNatSort($full_path);
				$output = array_merge($output, $temp);
			}
			else
			{
				$output[] = $full_path;
			}
		}
		return $output;
	}
	
	/* se esiste elimina il bot passato */
	function eliminaBot($nickBot)
	{
		foreach ( scandir(XDCCDIR) as $item )
		{
			if ( $item == '.' || $item == '..' )
				continue;
			$full_path = XDCCDIR."/$item";
			if ( is_dir($full_path) )
			{
				eliminaBot($full_path, $nickBot);
			}
			else if( ($nickname = getNickName($full_path)) )
			{
				if( $nickname == $nickBot )
				{
					unlink($full_path);
				}
			}
		}
	}
	
	/* dal file passato ne ricava il nick del bot */
	function getNickName($path)
	{
		if( ($fp = fopen($path, "r")) )
		{
			if( preg_match("/msg (.*) xdcc/mi", fread($fp, filesize($path)), $match) )  //ricavo il nick del bot
			{
				if( isset($match[1]) )
				{
					fclose($fp);
					return $match[1];
				}
			}
		}
		return 0;
	}
	
	function engtoita($mese)
	{
		if($mese == "Jan") return "GEN";
		elseif($mese == "Feb") return "FEB";
		elseif($mese == "Mar") return "MAR";
		elseif($mese == "Apr") return "APR";
		elseif($mese == "May") return "MAG";
		elseif($mese == "Jun") return "GIU";
		elseif($mese == "Jul") return "LUG";
		elseif($mese == "Aug") return "AGO";
		elseif($mese == "Sep") return "SET";
		elseif($mese == "Oct") return "OTT";
		elseif($mese == "Nov") return "NOV";
		elseif($mese == "Dec") return "DEC";
		else return null;
	}
	
?>
