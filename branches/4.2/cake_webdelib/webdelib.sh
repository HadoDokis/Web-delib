#!/bin/bash

ME="$0"
APP_DIR="`dirname "$ME"`"
WORK_DIR="$PWD"

# ------------------------------------------------------------------------------

function __clearDir() {
	dir="$1"

	if [ -d "$dir" ]
	then
		(
			cd "$dir"
			find . -type f -not -path '*/.svn/*' -not -name "empty" | while read -r ; do rm "$REPLY"; done
		)
	fi
}

# ------------------------------------------------------------------------------

function __clear() {
	dir="$1"
	__clearDir "$dir/tmp/cache/"
	__clearDir "$dir/tmp/logs/"
	__clearDir "$dir/tmp/sessions/"

    sudo rm app/tmp/logs/*
	sudo svn revert app/tmp/logs/{empty,error.log,debug.log} > /dev/null 2>&1

	sudo rm -f app/tmp/sessions/*
	sudo svn revert app/tmp/sessions/empty > /dev/null 2>&1

    sudo svn revert app/tmp/tests/test

	sudo rm -rf app/webroot/files/generee/*
}

# ------------------------------------------------------------------------------

function __svnbackup() {
	APP_DIR="`readlink -f "$1"`"

	xml="`svn info --xml app | sed ':a;N;$!ba;s/\n/ /g'`"
	revision="`echo $xml| sed 's/^.*<entry[^>]* revision=\"\([0-9]\+\)\".*$/\1/g'`"
	project="`echo $xml| sed 's/^.*<root>.*\/\([^\/]\+\)<\/root>.*$/\1/g'`"
	subfolder="`echo $xml| sed 's/^.*<url>.*\/\([^\/]\+\)\/cake_webdelib<\/url>.*$/\1/g'`"

	NOW=`date +"%Y%m%d-%H%M%S"` # FIXME: M sur 2 chars
	PATCH_DIR="$APP_DIR/../svnbackup-${project}_${subfolder}-r${revision}-${NOW}"
	PATCH_DIR="`readlink -f "$PATCH_DIR"`"

	mkdir -p "$PATCH_DIR"
	if [[ $? -ne 0 ]] ; then
		echo "Impossible de créer le répertoire ${PATCH_DIR}"
		exit 1
	fi

	(
		cd "$APP_DIR"
		local SAVEIFS=$IFS
		IFS=$(echo -en "\n\b")

		status="`svn status . | grep -v "^\(\!\|D\)" | sed 's/^\(.\{8\}\)\(.*\)$/\2/'`";
		for file in `echo "$status"`; do
			dir="`dirname "$file" | sed "s@^\./@$PWD@"`"
			if [ "$dir" != '.' ] ; then
				mkdir -p "$PATCH_DIR/app/$dir"
			fi
			cp -R "$file" "$PATCH_DIR/app/$dir"
		done
		IFS=$SAVEIFS
	)

	(
		cd "$PATCH_DIR"
		SVNBACKUP_SUBDIR="`basename "$PATCH_DIR"`"

		zip -o -r -m "../$SVNBACKUP_SUBDIR.zip" app >> "/dev/null" 2>&1
		if [[ $? -ne 0 ]] ; then
			echo "Impossible de créer le fichier $SVNBACKUP_SUBDIR.zip"
		else
			echo "Fichier $SVNBACKUP_SUBDIR.zip créé"
			cd ..
			rmdir "$PATCH_DIR"
		fi
	)
}

# ------------------------------------------------------------------------------

case $1 in
	clear)
		__clear "$APP_DIR"
		exit 0
	;;
	svnbackup)
		__clear "$APP_DIR"
		__svnbackup "$APP_DIR"
		exit 0
	;;
	*)
		echo "Usage: $ME {clear|svnbackup}"
		exit 1
	;;
esac