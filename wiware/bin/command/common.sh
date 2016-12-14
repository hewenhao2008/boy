#!/bin/sh
#-------
# WIWARE common.sh
#-------

S_DOWNLOADING="0001"                                        
S_DOWNSUCCESS="0002"                                        
S_DOWNFAILED="1002"                                         
S_CHECKSUCCESS="0003"                                       
S_CHECKFAILED="1003"                                        
S_UNTARSUCCESS="0004"                                       
S_UNTARFAILED="1004"             
S_SHELLSUCCESS="0005"    
S_SHELLFAILED="1005"     
S_SUCCESS="0000"     
S_FAILED="1000"

. /wiware/bin/wicore.sh

report(){
	timestamp=$(date "+%s")
	mac=$(wiget idmac)
	msg="$mac|$token|$msgid|$timestamp|$cmdid|$1"
	wipark_log "report: [$msg]"
	rptmsg=$(wiencode "$msg")
	wipark_log "report encoded: [$rptmsg]"
	curl -s --connect-timeout 10 -m 10 "$server/report" --data-urlencode "msg=$rptmsg" 2>/dev/null
}

# make sure there is only one $1 shell running 
shell=$(basename $0)

wipark_log "begin to run $0"

server=$(wiget server)
if [ "x$server" = "x" ]; then
	wipark_log "$0 no server found. 'wiget server' got null, but go on process."
fi

token=$(wiget token)
rtoken=$(echo $1 |awk -F '|' '{print $1}')
if [ "$token" != "$rtoken" ]; then
	wipark_log error "$0 Token check failed, (box:$token, server:$rtoken)"
	exit 1
fi

msgid=$(echo $1 |awk -F '|' '{print $2}')
cmdid=$(echo $1 |awk -F '|' '{print $3}')
msgbody=$(echo $1 |awk -F '|' '{print $4}')

