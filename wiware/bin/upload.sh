#!/bin/sh
# upload access log

. /wiware/bin/wicore.sh

upload(){
   upfile="$1"
   if [ "$upfile" = "" ];then
       return
   fi
   if [ -f $upfile ];then
   	wipark_log "upload file : $upfile"
   else
      return
   fi
   
   mac=$(wiget idmac)
   token=$(wiget token)
   
   msg="$mac|$token"
#   rptmsg=$(mbase64 $msg|sed -e ':a;N;$ s/\n//g;ba')
   rptmsg=$(wiencode $msg)
   upurl="$server/uploadlog?msg=$rptmsg"
   curl -s -F "filename=@$upfile" $upurl
   if [ $? -eq 0 ];then
       wipark_log "upload file success: $upfile"
       rm $upfile
   else
	wipark_log "upload file failed: $upfile"
   fi
}

server=$(wiget server)
if [ "x$server" = "x" ];  then
        wipark_log "$0 no server found. 'wiget server' got null."
        exit 1
fi

if [ $# -ge 1 ]; then
	wipark_log "upload a file: $1"
	upload $1
	exit 0
fi

logpath="/tmp/log"
tmplogfile="/tmp/log/www.log.utmp"
mv "$logpath/www.log" $tmplogfile
md5out=$(md5sum $tmplogfile|awk '{print $1}')
if [ "x$md5out" != "x" ]; then               
	mv $tmplogfile "$logpath/$md5out.log"
fi

ls -F $logpath 2>/dev/null|sed '/\/$/d'|sed -r '/[0-9a-f]{32}/!d'|while read -t 2 line
do
    if [ "x$line" != "x" ]; then 
        fullname="$logpath/$line"
	upload "$fullname"
    fi
done

