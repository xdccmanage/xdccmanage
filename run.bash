#!/bin/bash

check_curl ()
{
curl -h > /dev/null
if [ $? -ne 0 ] ; then
	echo "Installa curl : sudo apt-get install curl"
	exit 1
fi
}
check_wget ()
{
wget -h > /dev/null
if [ $? -ne 0 ] ; then
	echo "Installa wget : sudo apt-get install wget"
	exit 1
fi
}

update ()
{
	VERSION_URL="https://raw.githubusercontent.com/xdccmanage/iroffer-dinoex-xdccmanage/master/version"
	GITHUB_VERSION=$(curl "$VERSION_URL" 2> /dev/null)
	IROFFER_STR=$(./"$iroffer" -v)
	grep_result=$( echo "$IROFFER_STR" | grep "$GITHUB_VERSION" -)
	if [ -z "$grep_result" ] ; then
		echo "  STO AGGIORNANDO IROFFER  "
		UPDATE_URL="$(curl https://raw.githubusercontent.com/xdccmanage/iroffer-dinoex-xdccmanage/master/update_url 2> /dev/null)"/"$iroffer"
		curl -Lo "$iroffer" "$UPDATE_URL" 2&>1 /dev/null
		echo "  >>" $iroffer "... Aggiornato alla v" $GITHUB_VERSION
	fi
}


clear

echo " ######################################################"
echo " ##                                                  ##"
echo " ##  XDCCMANAGE                                      ##"
echo " ##                                                  ##"
echo " ##  Lancia  tanti  iroffer  quanti  sono  i         ##"
echo " ##  config  nella  directory  corrente.             ##"
echo " ##  Sito: http://xdccmanage.altervista.org/         ##"
echo " ##  https://github.com/xdccmanage		     ##"
echo " ##                                                  ##"
echo " ##  21 Novembre 2011                                ##"
echo " ##  Autore: puccio                                  ##"
echo " ##  modificato 19 Giugno 2020			     ##"
echo " ##                                                  ##"
echo " ######################################################"

echo ""

check_wget
check_curl

arch=`uname -m`

if [ $arch == "x86_64" ] ; then
	iroffer="iroffer-it64"
else
	iroffer="iroffer-it32"
fi

if [ -f $iroffer ] ; then
	chmod +x $iroffer
	update
	
else
	echo -n "Errore:" $iroffer "non esiste, VUOI CARICARLO? [si/no]: "
	read risp
	while [[ ! -n $risp ]] ; do
		echo -n "Errore:" $iroffer "non esiste, VUOI CARICARLO? [si/no]: "
		read risp
	done
	if [ $risp == "si" ] ; then
		if [ $arch == "x86_64" ] ; then
			if [ ! -f iroffer-it64 ] ; then
				echo -n " > Sto scaricando iroffer-it64 ... "
				wget https://github.com/xdccmanage/iroffer-dinoex-xdccmanage/releases/download/v3.33/iroffer-it64 > /dev/null 2>&1 && chmod 700 iroffer-it64 
				echo "OK"
			fi
		else
			if [ ! -f iroffer-it32 ] ; then
				echo -n " > Sto scaricando iroffer-it32 ... "
				wget https://github.com/xdccmanage/iroffer-dinoex-xdccmanage/releases/download/v3.33/iroffer-it32 > /dev/null 2>&1 && chmod 700 iroffer-it32
				echo "OK"
			fi
		fi
	else
		echo ""
		exit 0
	fi
fi

echo ""

echo ""
echo "  STO INIZIANDO  "
for file in *.config; do
	./$iroffer -b $file > /dev/null 2>&1
	echo "  >>" $file "... OK"
done

echo ""
