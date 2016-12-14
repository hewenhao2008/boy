#!/bin/sh
. /wiware/bin/wicore.sh

killservice(){
    kill -9 $(ps -w|grep boxselect|grep -v grep|awk {'print $1'}|tr -s "\n" " ") 2>/dev/null
    kill -9 $(ps -w|grep wicmd|grep -v vi|grep -v grep|awk {'print $1'}|tr -s "\n" " ") 2>/dev/null
}

addserverip(){
	serverip=$1
	if [ "x$serverip" != "x" ]; then
		if [ "x$(uci get wipark.server 2>/dev/null)" = "x" ]; then
                        uci set wipark.server=wipark
                fi
		existed=$(uci get wipark.server.ip|grep $serverip|wc -l)
		if [ $existed -eq 0 ]; then
			uci add_list wipark.server.ip="$serverip"
			iptables -t nat -I PREROUTING -d $serverip -j ACCEPT
		else
			wipark_log "wigetserver serverip $serverip already existed."
		fi
	fi
}

serverurl=$(cat /tmp/serverurl 2>/dev/null)
if [ "x$serverurl" != "x" ]; then
	exit 0
fi

mac=$(wiget idmac)
key=$(wiget key)
nameserver=$(wiget nameserver)
token=$(echo -n "$mac$key"|md5sum|awk '{print substr($1,1,4)}')

msg=$(wiencode "$mac|$token")
encodemsg=$(curl -s --connect-timeout 10 -m 10 "$nameserver" --data-urlencode msg=$msg 2>/dev/null)
configarr=$(widecode $encodemsg)

IFS="#"
for config in $configarr
do
	key=$(echo $config|awk -F'=' '{print $1}')
	value=$(echo $config|awk -F'=' '{print $2}')
	if [ "x$key" = "x" ] || [ "x$value" = "x" ]; then 
		continue
	fi
	if [ "$key" = "wipark.server.ip" ]; then
		addserverip $value
		continue
	fi

	if [ "$key" = "wipark.conf.nodeserver" ]; then 
		serverurl=$value;
	elif [ "$key" = "wipark.conf.authserver" ]; then 
		authserver=$value; 
	else
		/sbin/uci set $config
		wipark_log info "$0 set config: $config"
	fi
done
/sbin/uci commit

if [ "x$serverurl" != "x" ]; then
	echo "$serverurl" > /tmp/serverurl
	newserverdomain=$(echo $serverurl|awk -F'/' '{print $3}')
	curnodeserverdomain=$(uci get wipark.conf.nodeserver|awk -F'/' '{print $3}')
	if [ "$curnodeserverdomain" != "$newserverdomain" ]; then
		# change box domain configs
		domaintail=$(echo $newserverdomain |awk -F'.' 'sub($1".","")')
		if [ "x$domaintail" = "x" ]; then
			wipark_log "get error nodeserver value [$serverurl]"
			exit 0
		fi
		domain_alies=$(/sbin/uci get wipark.conf.domain_alies 2>/dev/null||echo "u.$domaintail")
		domain="m.$domaintail"
		/sbin/uci set wipark.conf.domain="$domain"
		subw="w.$domaintail"
		if [ "$domaintail" != "wipark.cn" ]; then
			domain="m.wipark.cn $domain"
			subw="w.wipark.cn $subw"
		fi
		/sbin/uci set wipark.conf.nodeserver="$serverurl"
		/sbin/uci commit
		sed -ir "s/server_name.*$/server_name $domain $domain_alies;/" /etc/nginx/conf.d/main.conf
		sed -ir "s/server_name.*$/server_name $subw;/" /etc/nginx/conf.d/subw.conf
		/bin/sh /wiware/bin/setuphosts.sh
		nginx -s reload
	else 
		wipark_log "boxdomain is the same as that get from wigetserver.sh"
	fi

	wipark_log "get serverurl from Server: $serverurl" 
	killservice
else 
	serverurl=$(/sbin/uci get wipark.conf.nodeserver 2>/dev/null)
	if [ "x$serverurl" != "x" ]; then
		echo "$serverurl" >/tmp/serverurl
	fi
	wipark_log "get serverurl from Config:$serverurl"
fi

if [ "x$authserver" != "x" ]; then
	/sbin/uci set wipark.conf.authserver="$authserver"                                                 
	/sbin/uci commit  
	wipark_log "get authserver from server: $authserver"
fi

