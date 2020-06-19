#!/bin/bash

clear

echo " ######################################################"
echo " ##                                                  ##"
echo " ##  XDCCMANAGE                                      ##"
echo " ##                                                  ##"
echo " ##  Aggiorna tutti i bot.                           ##"
echo " ##  Sito: http://xdccmanage.altervista.org/         ##"
echo " ##  Irc: //server -m irc.chlame.net -j #xdccManage  ##"
echo " ##                                                  ##"
echo " ##  13 Febbraio 2012                                ##"
echo " ##  Autore: puccio                                  ##"
echo " ##                                                  ##"
echo " ######################################################"

echo ""

script="script.bash"

if [ -f $script ] ; then
	if [ ! -x $script ] ; then
		chmod +x $script
	fi
else
	echo -n "Errore:" $script "non esiste, VUOI CARICARLO? [si/no]: "
	read risp
	while [[ ! -n $risp ]] ; do
		echo -n "Errore:" $script "non esiste, VUOI CARICARLO? [si/no]: "
		read risp
	done
	if [ $risp == "si" ] ; then
		if [ ! -f $script ] ; then
			echo -n " > Sto scaricando script.bash ... "
			wget http://xdccmanage.altervista.org/tool/$script > /dev/null 2>&1 && chmod 700 $script
			echo "OK"
		fi
	else
		echo ""
		exit 0
	fi
fi

for file in *.config; do
	if [ -f $file ] ; then
		./script.bash $file
		echo " >" $file "... [ AGGIORNATO ]"
	fi
done
