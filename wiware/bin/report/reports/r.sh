#!/bin/sh

. /wiware/bin/wicore.sh
export PATH=$PATH:/wiware/bin

config_report() {
	server=$(wiget server)
	if [ "x$server" = "x" ];  then
		wipark_log "$0 no server found. 'wiget server' got null." 
		return 1
	fi
	mac=$(wiget idmac)
	token=$(wiget token)
	msgbody="$1"
	rptmsg=$(wiencode "$mac|$token|$msgbody")
	wipark_log "config report $rptmsg"
	curl -s --connect-timeout 10 -m 10 "$server/config" --data-urlencode "msg=$rptmsg"
}

