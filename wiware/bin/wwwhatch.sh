#!/bin/sh
. /wiware/bin/wicore.sh

hatch_www() {
	wwwroot='/wiware/www'
        for i in $(ls $wwwroot); do
                if [ -d "$wwwroot/$i" ] && [ -f "$wwwroot/$i/hatch.php" ]; then
			running=$(ps |grep hatch.php|grep $i|grep -v grep|wc -l)
			if [ $running -eq 0 ]; then
                		php-cgi "$wwwroot/$i/hatch.php"  1>/dev/null 2>>$ERRLOG &
                	else
                		wipark_log "$wwwroot/$i/hatch.php is running"
			fi
                fi
        done 
}

shellrunning=$(ps |grep hatch.php|grep -v grep|wc -l)
if [ $shellrunning -eq 0 ]; then
	timeout=$(awk 'BEGIN{srand();k=int(rand()*120);print k;}')
	[ $timeout -lt 1 ] && timeout=3
	sleep $timeout

	hatch_www
else
	wipark_log "hatch www is running"
fi

