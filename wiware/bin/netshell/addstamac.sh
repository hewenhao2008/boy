#!/bin/sh

if [ `grep "$1" /wiware/etc/boxlist |wc -l` -gt 0 ];
then
   mac=`ebget ipmac $2`
   if [ `/wiware/bin/netshell/fw_list.sh |grep $mac |wc -l` -eq 0 ];
   then
     iptables -t nat -I PREROUTING -m mac --mac-source $mac -j ACCEPT 
   fi

   if [ `grep "$mac" /wiware/etc/white_maclist |wc -l` -eq 0 ];
   then
      echo $mac >> /wiware/etc/white_maclist
   fi
   
   if [ -f /tmp/staroute ];
   then
      cat /tmp/staroute
   else
      /wiware/bin/ebget nettype
   fi

else
   echo "invalid STA BOX"
fi

