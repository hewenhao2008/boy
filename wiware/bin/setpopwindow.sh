#!/bin/sh
#setpopwindow.sh on|off

case "$1" in
on)
	#sed -i 's/^[ \t]*error_page[ \t]*404.*$/\terror_page 404 @fallback;/' /etc/nginx/conf.d/main.conf
	sed -ir 's/^[ \t]*error_page[ \t]*404.*$/\terror_page 404 = \/404.pl;/' /etc/nginx/conf.d/main.conf
	uci set wipark.conf.popwindow=1
;;
off)
	sed -ir 's/^[ \t]*error_page[ \t]*404.*$/\terror_page 404 \/404.pl;/' /etc/nginx/conf.d/main.conf
	uci set wipark.conf.popwindow=0
;;
*)
	sed -ir 's/^[ \t]*error_page[ \t]*404.*$/\terror_page 404 = \/404.pl;/' /etc/nginx/conf.d/main.conf
	uci set wipark.conf.popwindow=1
;;
esac
uci commit
nginx -s reload

