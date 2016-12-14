#!/bin/sh
# set network to dhcp

. /wiware/bin/command/common.sh

sh /wiware/bin/setdhcp.sh $msgbody
report $S_SUCCESS

sleep 3 && /etc/init.d/network restart

