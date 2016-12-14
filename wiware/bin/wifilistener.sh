#!/bin/sh
. /wiware/bin/wicore.sh
ps|grep "apguy -l"|grep -v grep|awk '{print $1}'|xargs kill -9

apguy -l| while read line;
do
	op=$(echo $line|cut -b1)
        if [ "$op" = "A" ]; then
                umac=$(echo $line|awk -F'[][]' '{print $2}'|tr [a-z] [A-Z])
		wipark_log "=== $umac associated"
		sh /wiware/bin/associate.sh $umac &
        elif [ "$op" = "D" ]; then
                umac=$(echo $line|awk -F'[][]' '{print $2}'|tr [a-z] [A-Z])
		wipark_log "=== $umac disassociated"
		sh /wiware/bin/disassociate.sh $umac &
        fi

#        assocmac=$(echo $line|grep ASSOC|awk -F'[][]' '{print $2}'|tr [a-z] [A-Z])
#	if [ "x$assocmac" != "x" ]; then
#		wipark_log "=== $assocmac associated"
#		sh /wiware/bin/associate.sh $assocmac &
#	fi
#        disassocmac=$(echo $line|grep DISASSOC|awk -F'[][]' '{print $2}'|tr [a-z] [A-Z])
#	if [ "x$disassocmac" != "x" ]; then
#		wipark_log "=== $disassocmac disassociated"
#		sh /wiware/bin/disassociate.sh $disassocmac &
#	fi
done
