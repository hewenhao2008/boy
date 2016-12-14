#!/bin/sh

. /wiware/bin/command/common.sh 

# tmp download dir in /tmp
TMPDIR_FLASH="/tmp/download"
tmpoutput="/tmp/0002.out"

#function progress, $1 resid, $2 progress percent
curlprogress(){
   rptprefix="$1"
   g_stime=0
   line=""
   null_times=0
   while read -t 3 line
   do
        wipark_log "curlprogress get a line[$line]" 
        if [ "x$line" = "x" ];then
		let null_times++
                if [ $null_times -gt 10 ]; then
			wipark_log "null over 10 times, progress return."
                        return
                fi
                continue;
        fi
	let null_times=0

        let l_stime=$g_stime+3
	l_ntime=$(cat /proc/uptime |awk -F'.' '{print $1}')
        if [ $l_ntime -lt $l_stime ];then
                continue
        fi
        let g_stime=$l_ntime

	tmpresult=$(echo "$line"|awk '{print $1"|"$2"|"$4"|"$9"|"$10"|"$11"|"$12}') 
	wipark_log "$tmpresult" 
	base64out=$(echo $tmpresult|base64|sed -e ':a;N;$ s/\n//g;ba')
        report "$rptprefix:$base64out"

	percent=$(echo "$line"|awk '{print $1}')
        if [ $percent -ge 100 ]; then
                wipark_log "0002 download 100 percent,progress return."
                return
        fi
   done
}

#--------
# function download $1 resid
#--------
download(){
   wipark_log "download rsid=$1"
   resid="$1"

   tmpdir="$TMPDIR_FLASH/$resid"
   wipark_log "download dir: $tmpdir"

   [ -d $tmpdir ] && wipark_log "$tmpdir already existed, resume the previous download"
   mkdir -p $tmpdir
   
   olddir=$(pwd)
   downurl="$server/boxdl?id=$resid"
   wipark_log "download url: $downurl"
   downfile="$tmpdir/$resid.tar"

   let diskavailablesize=$(df /tmp|grep -v Available|awk '{print $4}')*1024  

    httpcode=$(curl -s -m 10 -o /dev/null -s -w %{http_code} $downurl)
    [ $httpcode -ne 200 ] && {
		errinfo="Download $resid failed: httpcode $httpcode" 
		wipark_log error "cmd_0002 $errinfo"
		base64out=$(mbase64 "$errinfo")
                report "$resid:$S_DOWNFAILED:$base64out"
                return
    }
    downfilesize=$(curl -I -s -m 10 $downurl 2>/dev/null |grep Content-Length |awk -F':' '{print $2}')
    if [ $downfilesize -gt $diskavailablesize ]; then
		errinfo="No enough space left: disk available($diskavailablesize), downfile($downfilesize)" 
		wipark_log error $errinfo
		base64out=$(mbase64 "$errinfo")
	        report "$resid:$S_DOWNFAILED:$base64out"
		return
    fi
    wipark_log "Downloading. disk available($diskavailablesize), downfile($downfilesize)"
    (curl --connect-timeout 10 -C - --limit-rate 2M -o $downfile $downurl -L 2>&1;echo $?>/tmp/rtcode)|tr -s '\r' '\n'|awk '/:.*:/ {print}'|curlprogress "$resid:$S_DOWNLOADING"
   
   curlstatus=$(cat /tmp/rtcode)
   wipark_log "curl status=$curlstatus"
   if [ "$curlstatus" = "33" ]; then
        rm -rf $tmpdir
   elif [ "$curlstatus" != "0" ]; then
        #rm -rf $tmpdir
        errinfo="download $resid failed. curl status code ($curlstatus)." 
	wipark_log "cmd_0002 $errinfo"
	base64out=$(mbase64 "$errinfo")
   	report "$resid:$S_DOWNFAILED:$base64out"
        return
   fi
   
   report "$resid:$S_DOWNSUCCESS"
   wipark_log "curl $resid done."
   
   calmd5=$(md5sum $downfile|awk '{print $1}')
   if [ "$calmd5" != "$resid" ]; then
        rm -rf $tmpdir
        errinfo="check md5 error, resid[$resid], calmd5[$calmd5]." 
	wipark_log error "cmd_0002 $errinfo"
	base64out=$(mbase64 "$errinfo")
   	report "$resid:$S_CHECKFAILED:$base64out"
   	return
   fi
   
   wipark_log "check md5 success."
   report "$resid:$S_CHECKSUCCESS"
   # 0002: md5 ok, next is to extract and exec install.sh
   
   cd $tmpdir
   tar -xf "$downfile" 1>"$tmpoutput" 2>&1
   
   tarstatus=$?
   if [ $tarstatus -ne 0 ]; then
   	errinfo="extract tar $downfile failed." 
   	wipark_log error "cmd_0002 $errinfo"
	base64out=$(mbase64 "$errinfo")
   	report "$resid:$S_UNTARFAILED:$base64out"
	cd $olddir
	rm $tmpdir
        return
   fi

   wipark_log "extract tar $downfile done."
   report "$resid:$S_UNTARSUCCESS"

#delete tarbao after untar
   [ "x$downfile" != "x" ] &&  rm $downfile
   
   sh "$tmpdir/install.sh" 1>"$tmpoutput" 2>&1
   shellstatus=$?
   cd $olddir
   rm -r $tmpdir
   
   if [ $shellstatus -ne 0 ];then
   	errinfo="shell exec failed.($tmpdir/install.sh)" 
   	wipark_log error "cmd_0002 $errinfo"
	base64out=$(mbase64 "$errinfo")
   	report "$resid:$S_SHELLFAILED:$base64out"
        return
   fi
   
   wipark_log "shell exec done."
   report "$resid:$S_SHELLSUCCESS"
   
   wipark_log "Download task rsid=$resid done."
   report "$resid:$S_SUCCESS"
}


#--------
# main part
#--------

packageid=$(echo $msgbody|awk -F '#' '{print $1}')
version=$(echo $msgbody|awk -F '#' '{print $2}')
nowversion=$(wiget version)

wipark_log "Version now: $nowversion, this cmd want to update to version: $version"
needupdate=$(expr $version \> $nowversion)
if [ $needupdate -eq 0 ]; then
	wipark_log "No need to do system update."
	report "$packageid:$S_SUCCESS"
	exit 1
fi

download "$packageid"

