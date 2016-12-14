#!/bin/sh

/bin/sh /wiware/bin/setuphosts.sh norestart
/bin/sh /wiware/bin/setupssid.sh
/bin/sh /wiware/bin/dnsblock.sh setclear

