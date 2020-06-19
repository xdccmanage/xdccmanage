#!/bin/bash

clear

echo " ######################################################"
echo " ##                                                  ##"
echo " ##  XDCCMANAGE                                      ##"
echo " ##                                                  ##"
echo " ##  Creazione guidata config per iroffer            ##"
echo " ##  Sito: http://xdccmanage.altervista.org/         ##"
echo " ##  Irc: //server -m irc.chlame.net -j #xdccManage  ##"
echo " ##                                                  ##"
echo " ##  Ultima modifica: 03 Dicembre 2011               ##"
echo " ##  Autore: puccio                                  ##"
echo " ##                                                  ##"
echo " ######################################################"

function is_integer() {
    [ "$1" -eq "$1" ] > /dev/null 2>&1
    return $?
}

arch=`uname -m`
saved="no"

if [ $arch == "x86_64" ] ; then
	if [ ! -f iroffer-it64 ] ; then
		echo ""
		echo -n " > Sto scaricando iroffer-it64 ... "
		wget http://xdccmanage.altervista.org/tool/iroffer-it64 > /dev/null 2>&1 && chmod 700 iroffer-it64 
		echo "OK"
	fi
else
	if [ ! -f iroffer-it32 ] ; then
		echo -n " > Sto scaricando iroffer-it32 ... "
		wget http://xdccmanage.altervista.org/tool/iroffer-it32 > /dev/null 2>&1 && chmod 700 iroffer-it32
		echo "OK"
	fi
fi

if [ ! -f script.bash ] ; then
	echo -n " > Sto scaricando script.bash ... "
	wget http://xdccmanage.altervista.org/tool/script.bash > /dev/null 2>&1 && chmod 700 script.bash
	echo "OK"
fi

echo ""
echo " Questo script ti guiderà nella creazione dei config. Devi sapere che:"
echo " 1) le voci che iniziano per ** sono obbligatorie"
echo " 2) per interrompere lo script in qualsiasi punto devi premere CTRL+C"

while true; do
	
	echo ""
	echo -n "--> ** Iniziale dei nick (senza la parte numerica), per es. \"XM|Cinema|\" : "
	read nick
	while [[ ! -n $nick ]] ; do
		echo -n "--> ** Iniziale dei nick (senza la parte numerica), per es. \"XM|Cinema|\" : "
		read nick
	done
	
	echo ""
	echo -n "--> ** Numero di cifre per la numerazione dei bot, per es. \""$nick"01\" ha 2 cifre numeriche, \""$nick"001\" ha 3 cifre numeriche: "
	read cifre
	while ! is_integer $cifre ; do
		echo -n "--> ** Numero di cifre per la numerazione dei bot, per es. \""$nick"01\" ha 2 cifre numeriche, \""$nick"001\" ha 3 cifre numeriche: "
		read cifre
	done

	while true; do
		
		nicktemp=$nick
		for k in $(seq 2 $cifre); do
			nicktemp="$nicktemp""0"
		done
		
		echo ""
		echo -n "--> ** Numero di partenza, per es. inserendo 1 la numerazione partità da \""$nicktemp"1\" : "
		read start
		while ! is_integer $start ; do
			echo -n "--> ** Numero di partenza, per es. inserendo 1 la numerazione partità da \""$nicktemp"1\" : "
			read start
		done
		
		nicktemp=$nick
		for k in $(seq 3 $cifre); do
			nicktemp="$nicktemp""0"
		done
		
		echo ""
		echo -n "--> ** Numero di fine, per es. inserendo 50 la numerazione finirà a \""$nicktemp"50\" : "
		read end
		while ! is_integer $end ; do
			echo -n "--> ** Numero di fine, per es. inserendo 50 la numerazione finirà a \""$nicktemp"50\" : "
			read end
		done
		
		echo ""
		
		if [ "$start" -le "$end" ] ; then
			break
		else
			echo "----> ATTENZIONE! Il punto di partenza deve essere minore del punto di arrivo"
		fi
		
	done

	while true; do
		echo -n "--> ** Directory del bot, per es, \"/home/xdccmanage/cinema\" : "
		read path
		if [[ -n $path ]] ; then
			if [ -d $path ] ; then
				break
			fi
		fi
	done

	if [ $saved == "no" ] ; then
		echo ""
		echo -n "--> ** Nome del canale, per es. \"#xdccmanage\": "
		read chan
		while [[ ! -n $chan ]] ; do
			echo -n "--> ** Nome del canale, per es. \"#xdccmanage\": "
			read chan
		done

		echo ""
		echo -n "--> ** Nome del server e numero di porta, per es. \"irc.chlame.net 6667\" : "
		read server
		while [[ ! -n $server ]] ; do
			echo -n "--> ** Nome del server e numero di porta, per es. \"irc.chlame.net 6667\" : "
			read server
		done

		echo ""
		echo -n "--> Password nickserv, [default: xdccmanage]: "
		read nickserv_pass
		if [[ ! -n $nickserv_pass ]] ; then
			nickserv_pass="xdccmanage"
		fi

		echo ""
		echo -n "--> ** Host ftp, per es. \"xdccmanage.altervista.org\" : "
		read ftphost
		while [[ ! -n $ftphost ]] ; do
			echo -n "--> ** Host ftp, per es. \"xdccmanage.altervista.org\" : "
			read ftphost
		done

		echo ""
		echo -n "--> ** User ftp, per es. \"xdccmanage\" : "
		read ftpuser
		while [[ ! -n $ftpuser ]] ; do
			echo -n "--> ** User ftp, per es. \"xdccmanage\" : "
			read ftpuser
		done
		
		echo ""
		echo -n "--> ** Password ftp: "
		read ftppswd
		while [[ ! -n $ftppswd ]] ; do
			echo -n "--> ** Password ftp: "
			read ftppswd
		done
		
		echo ""
		while true; do
			echo -n "--> Porta ftp, [default: 21]: "
			read ftpport
			if [[ -n $ftpport ]] ; then
				if is_integer $ftpport ; then
					break
				fi
			else
				ftpport="21"
				break
			fi
		done

		echo ""
		echo -n "--> Path alla directory xdcc/ su ftp, [default: xdcc/]: "
		read ftpxdccdir
		if [[ ! -n $ftpxdccdir ]] ; then
			ftpxdccdir="xdcc/"
		fi

		echo ""
		echo "--> ** AdminHost: "
		echo "----> 1) preferisci specificare il nick?"
		echo "----> 2) oppure il vhost?"
		while true; do
			echo -n "------> Scelta [1/2] : "
			read adminhostchoice
			if [[ -n $adminhostchoice ]] ; then
				if [ $adminhostchoice == "1" ] ; then
					break
				elif [ $adminhostchoice == "2" ] ; then
					break
				fi
			fi
		done
		
		if [ $adminhostchoice == "1" ] ; then
			echo -n "--------> ** Specifica il/i nick, per es. \"admin1 admin2 admin3\" : "
		elif [ $adminhostchoice == "2" ] ; then
			echo -n "--------> ** Specifica il/i vhost, per es. \"vhost1 vhost2 vhost3\" : "
		fi
		read adminhost
		while [[ ! -n $adminhost ]] ; do
			if [ $adminhostchoice == "1" ] ; then
				echo -n "--------> ** Specifica il/i nick, per es. \"admin1 admin2 admin3\" : "
			elif [ $adminhostchoice == "2" ] ; then
				echo -n "--------> ** Specifica il/i vhost, per es. \"vhost1 vhost2 vhost3\" : "
			fi
			read adminhost
		done

		echo ""
		echo "--> ** UploadHost: "
		echo "----> 1) preferisci specificare il nick?"
		echo "----> 2) oppure il vhost?"
		while true; do
			echo -n "------> Scelta [1/2] : "
			read uploadhostchoice
			if [[ -n $uploadhostchoice ]] ; then
				if [ $uploadhostchoice == "1" ] ; then
					break
				elif [ $uploadhostchoice == "2" ] ; then
					break
				fi
			fi
		done
		
		if [ $uploadhostchoice == "1" ] ; then
			echo -n "--------> ** Specifica il/i nick, per es. \"uploader1 uploader2 uploader3\" : "
		elif [ $uploadhostchoice == "2" ] ; then
			echo -n "--------> ** Specifica il/i vhost, per es. \"vhost1 vhost2 vhost3\" : "
		fi
		read uploadhost
		while [[ ! -n $uploadhost ]] ; do
			if [ $uploadhostchoice == "1" ] ; then
				echo -n "--------> ** Specifica il/i nick, per es. \"uploader1 uploader2 uploader3\" : "
			elif [ $uploadhostchoice == "2" ] ; then
				echo -n "--------> ** Specifica il/i vhost, per es. \"vhost1 vhost2 vhost3\" : "
			fi
			read uploadhost
		done

		echo ""

		while true; do
			echo "--> ** Password admin: digitare la pass 2 volte separata da invio: "
			
			if [ $arch == "x86_64" ] ; then
				./iroffer-it64 -c > passpasspass
			else
				./iroffer-it32 -c > passpasspass
			fi
			
			adminpass=`cat passpasspass | grep adminpass`
			
			if [[ -n $adminpass ]] ; then
				break
			fi
		done
		rm passpasspass
		
		echo ""
		
		while true; do
			echo -n "--> Velocità minima di download, [default: 20]: "
			read transferminspeed
			if [[ -n $transferminspeed ]] ; then
				if is_integer $transferminspeed ; then
					break
				fi
			else
				transferminspeed="20"
				break
			fi
		done

		echo ""

		while true; do
			echo -n "--> Velocità massima di download, [default: 300]: "
			read transfermaxspeed
			if [[ -n $transfermaxspeed ]] ; then
				if is_integer $transfermaxspeed ; then
					break
				fi
			else
				transfermaxspeed="300"
				break
			fi
		done

		echo ""

		while true; do
			echo -n "--> Numero di code, [default: 30]: "
			read queuesize
			if [[ -n $queuesize ]] ; then
				if is_integer $queuesize ; then
					break
				fi
			else
				queuesize="30"
				break
			fi
		done

		echo ""

		while true; do
			echo -n "--> Numero di send, [default: 5]: "
			read slotsmax
			if [[ -n $slotsmax ]] ; then
				if is_integer $slotsmax ; then
					break
				fi
			else
				slotsmax="5"
				break
			fi
		done

		echo ""

		while true; do
			echo -n "--> Numero di upload, [default: 10]: "
			read max_uploads
			if [[ -n $max_uploads ]] ; then
				if is_integer $max_uploads ; then
					break
				fi
			else
				max_uploads="10"
				break
			fi
		done
	
	fi
	
	echo ""
	echo -n "--> Virtual hosts, specifica indirizzo IP (IP failover, lasciare vuoto se non si sa che è): "
	read vhosts
	
	echo ""
	echo -n "----> ** VUOI STARTARE IROFFER CON I CONFIG APPENA CREATI? [si/no]: "
	read risp
	while [[ ! -n $risp ]] ; do
		echo -n "----> ** VUOI STARTARE IROFFER CON I CONFIG APPENA CREATI? [si/no]: "
		read risp
	done

	for i in $(seq $start $end); do

		ilen=${#i}
		diff=`expr $cifre - $ilen`
		
		nicktemp=$nick
		
		for j in $(seq 1 $diff); do
			nicktemp="$nicktemp""0"
		done
		
		if [ ! -f $nicktemp$i.config ] ; then
			echo pidfile $nicktemp$i.pid >> $nicktemp$i.config
			echo statefile $nicktemp$i.state >> $nicktemp$i.config
			echo xdcclistfile $nicktemp$i.xdcc >> $nicktemp$i.config
			echo user_nick $nicktemp$i >> $nicktemp$i.config
			echo channel $chan >> $nicktemp$i.config
			echo server $server >> $nicktemp$i.config
			echo ftphost $ftphost >> $nicktemp$i.config
			echo ftpuser $ftpuser >> $nicktemp$i.config
			echo ftpport $ftpport >> $nicktemp$i.config
			echo ftppswd $ftppswd >> $nicktemp$i.config
			echo ftpxdccdir $ftpxdccdir >> $nicktemp$i.config
			echo filedir $path >> $nicktemp$i.config
			echo uploaddir $path >> $nicktemp$i.config
			echo autoadd_dir $path >> $nicktemp$i.config
			
			for usr in $adminhost; do
				if [[ -n $adminhostchoice ]] ; then
					if [ $adminhostchoice == "1" ] ; then
						echo adminhost $usr!*@*.* >> $nicktemp$i.config
						echo autoignore_exclude $usr!*@*.* >> $nicktemp$i.config
						echo unlimitedhost $usr!*@*.* >> $nicktemp$i.config
					elif [ $adminhostchoice == "2" ] ; then
						echo adminhost *!*@$usr >> $nicktemp$i.config
						echo autoignore_exclude *!*@$usr >> $nicktemp$i.config
						echo unlimitedhost *!*@$usr >> $nicktemp$i.config
					fi
				fi
			done
			
			for usr in $uploadhost; do
				if [[ -n $uploadhostchoice ]] ; then
					if [ $uploadhostchoice == "1" ] ; then
						echo uploadhost $usr!*@*.* >> $nicktemp$i.config
						echo autoignore_exclude $usr!*@*.* >> $nicktemp$i.config
					elif [ $uploadhostchoice == "2" ] ; then
						echo uploadhost *!*@$usr >> $nicktemp$i.config
						echo autoignore_exclude *!*@$usr >> $nicktemp$i.config
					fi
				fi
			done
			
			echo downloadhost *!*@* >> $nicktemp$i.config
			
			if [[ -n $transferminspeed ]] ; then
				echo transferminspeed $transferminspeed >> $nicktemp$i.config
			fi
			
			if [[ -n $transfermaxspeed ]] ; then
				echo transfermaxspeed $transfermaxspeed >> $nicktemp$i.config
			fi
			
			if [[ -n $max_uploads ]] ; then
				echo max_uploads $max_uploads >> $nicktemp$i.config
			fi
			
			echo slotsmax $slotsmax >> $nicktemp$i.config
			echo queuesize $queuesize >> $nicktemp$i.config
			
			echo $adminpass >> $nicktemp$i.config
			
			echo autoadd_time 30 >> $nicktemp$i.config
			echo autoadd_delay 30 >> $nicktemp$i.config
			echo user_realname xdccmanage >> $nicktemp$i.config
			echo nickserv_pass $nickserv_pass >> $nicktemp$i.config
			echo reconnect_delay 60 >> $nicktemp$i.config
			echo user_modes +iB >> $nicktemp$i.config
			
			echo maxtransfersperperson 1 >> $nicktemp$i.config
			echo ignore_duplicate_ip 1 >> $nicktemp$i.config
			echo maxqueueditemsperperson 1 >> $nicktemp$i.config
			echo balanced_queue >> $nicktemp$i.config
			echo requeue_sends >> $nicktemp$i.config
			echo send_batch >> $nicktemp$i.config
			echo noduplicatefiles >> $nicktemp$i.config
			echo include_subdirs >> $nicktemp$i.config
			echo removelostfiles >> $nicktemp$i.config
			
			echo restrictlist >> $nicktemp$i.config
			echo restrictprivlist >> $nicktemp$i.config
			echo restrictprivlistmsg Scrivi !list nel canale >> $nicktemp$i.config
			echo restrictsend >> $nicktemp$i.config
			echo ignoreuploadbandwidth >> $nicktemp$i.config
			echo extend_status_line >> $nicktemp$i.config
			echo no_status_chat >> $nicktemp$i.config
			echo no_status_log >> $nicktemp$i.config
			echo show_date_added >> $nicktemp$i.config
			echo uploadminspace 1000 >> $nicktemp$i.config
			echo hideos >> $nicktemp$i.config
			echo nomd5sum >> $nicktemp$i.config
			echo nocrc32 >> $nicktemp$i.config
			
			if [[ -n $vhosts ]] ; then
				echo local_vhost $vhosts >> $nicktemp$i.config
			fi
			
			chmod 600 $nicktemp$i.config
			
			if [ $risp == "si" ] ; then
				if [ $arch == "x86_64" ] ; then
					./iroffer-it64 -b $nicktemp$i.config > /dev/null 2>&1
				else
					./iroffer-it32 -b $nicktemp$i.config > /dev/null 2>&1
				fi
				echo "------>" $nicktemp$i "... OK"
			fi
			
		else
			echo $nicktemp$i.config "esiste già"
		fi
	done
	
	echo ""
	echo -n "----> ** VUOI CREARE ALTRI CONFIG? [si/no]: "
	read risp
	while [[ ! -n $risp ]] ; do
		echo -n "----> ** VUOI CREARE ALTRI CONFIG? [si/no]: "
		read risp
	done
	
	if [ $risp != "si" ] ; then
		break
	fi
	
	echo ""
	echo -n "----> ** VUOI MANTENERE ALCUNI DEI DATI GIÀ INSERITI? [si/no]: "
	read saved
	while [[ ! -n $saved ]] ; do
		echo -n "----> ** VUOI MANTENERE ALCUNI DEI DATI GIÀ INSERITI? [si/no]: "
		read saved
	done
	
done
