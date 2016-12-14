# Copyright (C) 2014 wipark.cn
# common use functions 

source /etc/profile >/dev/null
ERRLOG='/tmp/log/err.log'

#Usage: wp_log [LEVEL] [MESSAGE]
#LEVEL: debug info notice err|error warnig crit alert emerg, default:info

wipark_log() {
        local level="$1"
        [ -n "$2" ] && shift || level=info
        [ "$level" != error ] || {
		sz=$(ls -l $ERRLOG |awk '{print $5}')
        	if [ $sz -gt 100000 ];then
                	rm $ERRLOG
        	fi

		echo "Error: $@" >&2
		echo "$(date +'%F %T') Error: $@" >> $ERRLOG
	}
        logger -t wipark -p user.$level "$@"
}

wiencode(){
        if [ "x$1" != "x" ]; then
                base64out=$(echo -n $1|base64|sed -e ':a;N;$ s/\n//g;ba')
                echo "$base64out"| tr '3658024971' '!@#$%^&*()'
        fi
}

widecode(){
        if [ "x$1" != "x" ]; then
                base64out=$(echo "$1"| tr '!@#$%^&*()' '3658024971')
                echo "$base64out"|base64 -d
        fi
}

