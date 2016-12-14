#!/bin/sh

cur_time=$(cat /proc/uptime| awk -F'.' '{printf $1}')
ipaddr=$(/sbin/uci get network.wlan.ipaddr 2>/dev/null || echo '10.10.10.1')
for n in `ls /tmp/-.*`;do
  file_time=`echo $n|awk -F'.' '{print $3}'`
  mac=`basename $n|awk -F'.' '{print $2}'`
  
  if [ $cur_time -ge $file_time ];
  then
      echo [`date +"%Y-%m-%d %H:%M:%S"`]"[uptime:$cur_time] del $mac" >> /tmp/usermac.log
      iptables -t nat -D PREROUTING -m mac --mac-source $mac -j ACCEPT
      iptables -t nat -D PREROUTING  -p tcp --dport 80 ! -d $ipaddr -m mac --mac-source $mac -j DNAT --to $ipaddr:8118

#      iptables -t nat -D PREROUTING -p tcp -m multiport --dports 0:79,81:65535  -m mac --mac-source $mac -j ACCEPT
#      iptables -t nat -D PREROUTING -p udp --dport 0:65535  -m mac --mac-source $mac -j ACCEPT
      rm -f $n
  fi
  
done
