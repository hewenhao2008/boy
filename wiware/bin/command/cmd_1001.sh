#!/bin/sh
# reboot

. /wiware/bin/command/common.sh

wipark_log "reboot execed" 
report $S_SUCCESS

sleep 3 && reboot
