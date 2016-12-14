#!/bin/sh

for i in /wiware/bin/report/statistics/S*.sh; do
	[ -x $i ] && sh $i >/dev/null 2>&1 
done

