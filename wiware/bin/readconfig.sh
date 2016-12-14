#!/bin/sh

arr=$(ls /etc/config/|sed -r 's/[ \t]/\n/g'|sed -r '/system|dhcp|button|fstab|firewall/d')
for config in $arr
do
	uci show $config 2>/dev/null
done

