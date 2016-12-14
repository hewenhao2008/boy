#!/bin/bash
# Copyright (C) 2014 wipark.cn

. /wiware/bin/wicore.sh

report(){
	orginmsg="$1"
        wipark_log info "report: [$orginmsg]" 
        #rptmsg=$(echo -n $orginmsg|base64|sed -e ':a;N;$ s/\n//g;ba')
        rptmsg=$(wiencode $orginmsg)
        wipark_log info "report encode msg: [$rptmsg]" 
	CURL=$(which curl)
        if [ -f $CURL ];then
        	$CURL --connect-timeout 10 -s "$server/report" --data-urlencode "msg=$rptmsg"
        else
	        rpturl="$server/report?msg=$rptmsg"
        	wget -T20 -q --spider $rpturl
        fi
}

# remote shellcmd. $1-cmd to exec, $2-paras needed by report
execcmd(){
   shellcmd="$1"
   rptpara="$2"
   
   count=10
   let s=$(date +%s)+2
   data=""
   status="0000"
   
   output="/tmp/cmd.output"
   echo "$shellcmd" |/bin/sh 1>$output 2>&1
   if [ $? -ne 0 ];then
   	status="1000"
   fi
   
   while read -t 1 line
   do
	if [ "x$line" != "x" ]; then
	   if [ "x$data" != "x" ]; then
		data=$(echo "$data\n$line")
	   else
		data=$(echo "$line")
	   fi
	fi
	
	if [ $count -le 0 ]; then 
	   if [ "$data" != "" ]; then
	        base64out=$(echo -e "$data"|base64|sed -e ':a;N;$ s/\n//g;ba')
	   	omsg="$rptpara|0001#$base64out"
	   	report "$omsg"
	   fi
	   count=10
	   data=""
	fi
	e=$(date +%s)
	
	if [ $e -gt $s ] && [ "$data" != "" ]; then
	   base64out=$(echo -e "$data"|base64|sed -e ':a;N;$ s/\n//g;ba')
	   omsg="$rptpara|0001#$base64out"
	   report "$omsg"
   	   let s=$(date +%s)+2
	   count=10
	fi
	let count--
	line=""
   done < $output
   
   if [ "x$data" != "x" ]; then
	base64out=$(echo -e "$data"|base64|sed -e ':a;N;$ s/\n//g;ba')
	omsg="$rptpara|$status#$base64out"
	report "$omsg"
   else
	base64out=$(echo "success"|base64|sed -e ':a;N;$ s/\n//g;ba')
	omsg="$rptpara|$status#$base64out"
	report "$omsg"
   fi
}

#----------
# main part 
#----------

count=0
maxheartbeat=250

while true
do
  line=""
  read -t 10 line
  if [ "x$line" != "x" ]; then
     wipark_log "wicmd get a line[$line]"
     line=$(echo $line|tr -d "\r")
     if [ $(expr "$line" : 'data') -gt 0  ]; then
	encodemsg=$(echo $line |awk -F ':' '{print $2}')
	wipark_log "wicmd get a msg[$encodemsg]"
	cmd=$(widecode $encodemsg)
	wipark_log "wicmd get a task cmd=[$cmd]"

	server=$(/wiware/bin/wiget server)
	mac=$(/wiware/bin/wiget idmac)
	
	timestamp=$(date "+%s")
	token=$(echo $cmd |awk -F '|' '{print $1}')
	msgid=$(echo $cmd |awk -F '|' '{print $2}')
	cmdid=$(echo $cmd |awk -F '|' '{print $3}')
	msgbody=$(echo $cmd |awk -F '|' '{print $4}')

	case "$cmdid" in
	5000)
	     wipark_log "heartbeat: $msgbody"
	;;
	9999)
             wipark_log "wicmd get a shell cmd[$msgbody]"
             ret=$(execcmd "$msgbody" "$mac|$token|$msgid|$timestamp|$cmdid")
	;;
	*)
	     cmdstatus=$(ps|grep "cmd_$cmdid"|grep -v grep|grep -v "vi cmd_$cmdid"|wc -l)
             wipark_log "ack $mac|$token|$msgid|$cmdid|$cmdstatus"
             msg=$(wiencode "$mac|$token|$msgid|$cmdid|$cmdstatus")

             ackr=$(curl -s --connect-timeout 10 -m 10 "$server/ack" --data-urlencode "msg=$msg" |awk '{print $1}')
             wipark_log "wicmd ackr=$ackr"

             if [ "$ackr" = "ok" ];then
                if [ "$cmdid" = "6000" ];then
                	disk=$(df|grep mmcblk0p1|awk '{print $3/1024"#"$2/1024}')
                	if [ "$disk" = "" ];then
                		 disk="0#0"
                	fi
                	report "$mac|$token|$msgid|$timestamp|$cmdid|$disk"
                else
             		/bin/sh /wiware/bin/command/cmd_$cmdid.sh "$cmd" 1>/dev/null 2>&1 
             	fi
             else
             	wipark_log "wicmd ackr is not ok, won't to exec cmd $cmdid"
             fi
          ;;
        esac

        let count=0
     else
	wipark_log "line not contains (data:)"
        sleep 1
        let count=count+1
     fi
  else
     sleep 1
     let count=count+11
  fi

  if [ $count -gt $maxheartbeat ];
  then
     wipark_log "wicmd heart beat timeout: $count"
     rm /tmp/serverurl 2>/dev/null
     kill -9 $(ps -w|grep wget|grep boxselect|grep -v grep|awk {'print $1'}|tr -s "\n" " ") 2>/dev/null
     exit
  fi

done

