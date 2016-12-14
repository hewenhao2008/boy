#!/bin/sh

if [ $# -lt 2 ]; then
   echo "usage: $0 ip seconds"
   exit 1
fi

ip=$1
seconds=$2

mac=$(/wiware/bin/wiget ipmac $ip)
sh /wiware/bin/netshell/mac_add.sh $mac $seconds
return $?

