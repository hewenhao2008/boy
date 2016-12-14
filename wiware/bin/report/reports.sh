#!/bin/sh

for i in /wiware/bin/report/reports/R*.sh; do
	[ -x $i ] && sh $i >/dev/null 2>&1
done

