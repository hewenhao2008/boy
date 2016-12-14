#!/bin/sh
# reset www

. /wiware/bin/command/common.sh

# reset www
cd /wiware/ && {
	rm -rf www/*
	cp -rf www1/* www/
}
# reset end

wipark_log "reset www executed"

report $S_SUCCESS

