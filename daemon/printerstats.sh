#!/bin/bash
### BEGIN INIT INFO
# Provides:          printerstats
# Required-Start:    
# Required-Stop:     
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: Start daemon at boot time
# Description:       Enable PrinterStats Daemon
### END INIT INFO

var="/opt/printers/daemon"

case "$1" in
  start)
    echo -n "Starting PrinterStats Daemon: "
    nohup python "$var"/getstatsd.py > "$var"/daemon_output.log &
    echo "Started"
    ;;
  stop)
    echo -n "Stopping PrinterStats Daemon: "
    line=$(head -n 1 /var/run/printerstats.pid)
    kill -9 $line
    echo "Stopped"
    ;;
  restart)
    $0 stop
    sleep 2
    $0 start
    ;;
  reload|force-reload)
    echo "Force Reload and Reload are not supported."
esac
