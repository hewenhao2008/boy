#!/bin/sh
#--------------
# initwipark.sh 
#--------------

source /etc/profile
. /wiware/bin/wicore.sh

/bin/sh /wiware/bin/wifilistener.sh &

#----------------
# format extdisk if extdisk is available and TYPE != ext4
#----------------
[ -e /dev/mmcblk0p1 ] && {
        extfstype=$(blkid /dev/mmcblk0p1|awk -F" TYPE=" '{print $2}'|awk -F'"' '{print $2}')
        if [ "$extfstype" != "ext4" ]; then
                led all off && led red on && led blue on
                device='mmcblk0p1'
                umount /dev/$device
                #mkfs.ext2 /dev/$device && {
                mkfs.ext4 -O ^has_journal,extent /dev/$device && {
                        ( mkdir -p /mnt/$device && mount /dev/$device /mnt/$device ) 2>&1 | tee /proc/self/fd/2 | logger -t 'fstab'
                        [ -d /tmp/extdownload ] || {
                                mkdir -p /mnt/$device/extdownload
                                cd /tmp && ln -s /mnt/$device/extdownload extdownload
                        }
                        [ -d /mnt/$device/www2 ] || {
                                mkdir -p /mnt/$device/www2
                                cp -r /wiware/www1/* /mnt/$device/www2/
                                sync
                        }

                        rm -f /wiware/www
                        rm -f /wiware/rs
                        cd /wiware
                        ln -s /mnt/$device/www2 www
                }
        fi
}

[ -f /lastreboottime ] && {
        #date -s "2007-08-03 14:15:00"
        lastreboottime=$(cat /lastreboottime  2>/dev/null)
        #ptime=$(date -d "1970-01-01 UTC $lastreboottime seconds" +"%Y%m%d %T")
        date -s "$lastreboottime"
}

/bin/sh /wiware/bin/wigetserver.sh
/bin/sh /wiware/bin/wiservice.sh 1>/dev/null 2>>$ERRLOG &

nginx -s reload

#system test
if [ ! -e /root/sys.ok ]; then
        #first boot do system test.
        sh /wiware/bin/systemtest.sh &
fi

hasprinter=$(uci get wipark.conf.hasprinter 2>/dev/null || echo '0')
if [ $hasprinter -ge 1 ]; then
   /wiware/bin/printservice.sh &
fi

sh /wiware/bin/startqos.sh &
sh /wiware/bin/checknet.sh &

