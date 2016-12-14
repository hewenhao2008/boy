#!/bin/sh


case "$1" in
on)
	uci set wipark.conf.hasprinter=1
	uci commit
	ps|grep printservice.sh|grep -v grep|awk '{print $1}'|xargs kill -9
	sh /wiware/bin/printservice.sh >/dev/null &
;;
off)
	ps|grep printservice.sh|grep -v grep|awk '{print $1}'|xargs kill -9
	uci set wipark.conf.hasprinter=0
	uci commit
;;
*)
	echo "Usage:$0 <on|off>"
;;
esac

