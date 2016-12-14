# scan wifi 

#apcli | grep -v 'WPS DPID'|grep -v 'get_site_survey'| awk '{if(NF==9 && $5>20){print $5","$3","$2","$1","$4"|"}}'|sort -nr|sed -e ':a;N;$ s/\n//g;ba'

apcli | grep -v 'WPS DPID'|grep -v 'get_site_survey'| awk -F'|' '{
	if($5>20){
		sub(/[[:blank:]]*$/,"",$1)
		sub(/[[:blank:]]*$/,"",$2)
		sub(/[[:blank:]]*$/,"",$3)
		sub(/[[:blank:]]*$/,"",$4)
		sub(/[[:blank:]]*$/,"",$5)
		print $5","$3","$2","$1","$4"|"
	}
}' |sort -nr|sed -e ':a;N;$ s/\n//g;ba'
        
