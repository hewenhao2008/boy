#!/bin/sh

. /wiware/bin/report/reports/r.sh

hardware=$(wiget hardware)
version=$(wiget version)
releasetime=$(wiget releasetime)

rptinfo="hardware=$hardware#version=$version#releasetime=$releasetime"
wipark_log "report version: $rptinfo"
config_report "$rptinfo"

