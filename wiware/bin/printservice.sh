#!/bin/sh

count=$(ps |grep printservice|grep -v grep|wc -l)

if [ $count -gt 2 ]; then
   echo "printservice is running..."
   exit
fi

PRINTQUEUE=$(uci get wipark.conf.printqueue 2>/dev/null || echo '/mnt/mmcblk0p1/_printqueue')
#'/mnt/mmcblk0p1/_printqueue'
XPRINT='_xprint';

print_queue () {
	queue=$(ls -rt $PRINTQUEUE)
	for i in $queue;
	do
		$XPRINT $PRINTQUEUE/$i
		printresult=$?
		echo "Print result <$printresult>. be printed -- $i"
		if [ $printresult -eq 0 ]; then
			rm "$PRINTQUEUE/$i"
			echo "Print success. removed -- $i"
		fi
	done
}

if [ ! -d $PRINTQUEUE ]; then
	mkdir -p $PRINTQUEUE
fi

while true; do
	print_queue;
	sleep 3;
done

