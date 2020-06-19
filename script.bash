#!/bin/bash

if test -f $1; then
	config=$1
	host=`grep -e ^ftphost $config | sed "s/\r$//g" | cut -f 2 -d' '`
	if [ ! -n "$host" ]; then
		echo "Setta ftphost in $CONFIG";
		exit 1;
	fi
	port=`grep -e ^ftpport $config | sed "s/\r$//g" | cut -f 2 -d' '`
	if [ ! -n "$port" ]; then
		echo "Setta ftpport in $CONFIG";
		exit 2;
	fi
	usr=`grep -e ^ftpuser $config | sed "s/\r$//g" | cut -f 2 -d' '`
	if [ ! -n "$usr" ]; then
		echo "Setta ftpuser in $CONFIG";
		exit 3;
	fi
	passwd=`grep -e ^ftppswd $config | sed "s/\r$//g" | cut -f 2 -d' '`
	if [ ! -n "$passwd" ]; then
		echo "Setta ftppswd in $CONFIG";
		exit 4;
	fi
	filesrc=`grep -e ^xdcclistfile $config | sed "s/\r$//g" | cut -f 2 -d' '`
	if [ ! -n "$filesrc" ]; then
		echo "Setta xdcclistfile in $CONFIG";
		exit 5;
	fi
	filedest=`grep -e ^ftpxdccdir $config | sed "s/\r$//g" | cut -f 2 -d' '`
	if [ ! -n "$filedest" ]; then
		echo "Setta ftpxdccdir in $CONFIG";
		exit 6;
	fi
	
	ftp -n $host $port<< CONFIG
   quote USER $usr
   quote PASS $passwd
   put $filesrc $filedest$filesrc
   quit
CONFIG
else
	echo "Error: fileconfig";
fi

exit 0
