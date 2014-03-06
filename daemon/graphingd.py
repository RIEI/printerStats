__author__ = 'pferland'
__email__ = "pferland@randomintervals.com"
__lastedit__ = "2014-01-24"
print "PrinterStats Daemon v2.0 GPL V2.0 ("+ __lastedit__ +") \n\tAuthor: " + __author__ + "\n\tEmail: " + __email__
import sys, re, os, time
from PrintersConfig import *
from PrinterStatsSQL import *
from PrinterStats import Graphing

folder = os.path.dirname(os.path.realpath(__file__))
print os.path.join(folder+"/config", "printers.ini")
pid = os.getpid()
f = open("/var/run/printerstats/graphingd.pid", "w")
f.write(str(pid))      # str() converts to string
f.close()

# INI file init, config/config.ini and config/printers.ini
pcfg = PrintersConfig(folder)
config = pcfg.ConfigMap("Daemon")
campuses = pcfg.CampusMap("Campuses")['Campuses'].split(",")
printers = pcfg.ConfigMapPrinters("Printers").get("Printers")
rg = re.compile('(.*?),', re.IGNORECASE | re.DOTALL)


conn = PrinterStatsSQL(config)
graph = Graphing(conn, config['wwwroot'])
models, printer_campuses, all_hosts = pcfg.generate_hosts_list(conn, campuses, printers)

while 1:
    for printer in all_hosts:
        host = all_hosts[printer][0]
        model = all_hosts[printer][1]
        printer_id = conn.getprinterid(host)
        campus_name = conn.getprinterscampusname(printer_id)
        graph.graph(printer_id, host, campus_name)
    time.sleep(10)