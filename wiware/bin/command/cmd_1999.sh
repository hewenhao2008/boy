#!/bin/sh
# reset

. /wiware/bin/command/common.sh

wipark_log "reset executed"
rm -rf /overlay/*
# reset end
report $S_SUCCESS

sleep 3 && reboot
