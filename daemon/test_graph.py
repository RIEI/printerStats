__author__ = 'pferland'
from PrintersConfig import PrintersConfig
from graphing import *
from PrinterStatsSQL import *

pcfg = PrintersConfig()
config = pcfg.ConfigMap("Daemon")

pSQL = PrinterStatsSQL(config)

for host_id in range(1, 23):

    host_name = pSQL.gethostname(host_id)
    print "Graphing " + host_name

    ####################
    print " Feature: " + "Counts"
    data = pSQL.getcounts(host_id)
    if not data[0]:
        print "No data, skipping."
    else:
        grapher(data, host_name, "Counts")

    ####################
    print " Feature: " + "Tray 1"
    data = pSQL.gettray1(host_id)
    if not data[0]:
        print "No data, skipping."
    else:
        grapher(data, host_name, "Tray 1")

    ####################
    print " Feature: " + "Tray 2"
    data = pSQL.gettray2(host_id)
    if not data[0]:
        print "No data, skipping."
    else:
        grapher(data, host_name, "Tray 2")

    ####################
    print " Feature: " + "Tray 3"
    data = pSQL.gettray3(host_id)
    if not data[0]:
        print "No data, skipping."
    else:
        grapher(data, host_name, "Tray 3")

    ####################
    print " Feature: " + "Toner"
    data = pSQL.gettoner(host_id)
    if not data[0]:
        print "No data, skipping."
    else:
        grapher(data, host_name, "Toner")

    ####################
    print " Feature: " + "Maint Kit A"
    data = pSQL.getkita(host_id)
    if not data[0]:
        print "No data, skipping."
    else:
        grapher(data, host_name, "Maint Kit A")

    ####################
    print " Feature: " + "Maint Kit B"
    data = pSQL.getkitb(host_id)
    if not data[0]:
        print "No data, skipping."
    else:
        grapher(data, host_name, "Maint Kit B")