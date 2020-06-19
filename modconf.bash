#!/bin/bash

clear

echo " ######################################################"
echo " ##                                                  ##"
echo " ##  XDCCMANAGE                                      ##"
echo " ##                                                  ##"
echo " ##  Inserisci la  riga  da  aggiungere ai config.   ##"
echo " ##  Ricorda  che  verranno  modificati  tutti       ##"
echo " ##  i  file  che  finisco  per  \".config\"         ##"
echo " ##  Sito: http://xdccmanage.altervista.org/         ##"
echo " ##  Irc: //server -m irc.chlame.net -j #xdccManage  ##"
echo " ##                                                  ##"
echo " ##  21 Novembre 2011                                ##"
echo " ##  Autore: TSoft                                   ##"
echo " ##                                                  ##"
echo " ######################################################"
echo ""

echo -n "Riga da aggiungere: "
read riga
while [[ ! -n $riga ]] ; do
	echo -n "Riga da aggiungere: "
	read riga
done

echo ""

for file in *.config; do
	echo "$riga" >> $file
	echo " " $file "... OK"
done

echo ""
