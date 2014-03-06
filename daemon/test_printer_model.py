__author__ = 'pferland'
__email__ = "pferland@randomintervals.com"
__lastedit__ = "2014-03-06"
print "PrinterStats Daemon v2.0 GPL V2.0 ("+ __lastedit__ +") \n\tAuthor: " + __author__ + "\n\tEmail: " + __email__
import sys, re, os
from PrintersConfig import *
from PrinterStatsSQL import *
from PrinterStats import PrinterStats
from PrinterStats import Graphing

folder = os.path.dirname(os.path.realpath(__file__))
print os.path.join("/etc/printerstats/config", "printers.ini")

# INI file init, config/config.ini and config/printers.ini
pcfg = PrintersConfig(folder)
config = pcfg.ConfigMap("Daemon")
campuses = pcfg.CampusMap("Campuses")['Campuses'].split(",")
printers = pcfg.ConfigMapPrinters("Printers").get("Printers")

#SQL object init
conn = PrinterStatsSQL(config)
pStats = PrinterStats(conn)

models, printer_campuses, all_hosts = pcfg.generate_hosts_list(conn, campuses, printers)
Model_functions = pStats.create_models_functions(models)


print "----------Running Test of Printer Functions----------"
host = "10.2.138.30"
model = "ra8300"
print "----------For Host: " + host + "----------"
print "----------Model: " + model + "----------"


supplies = pStats.check_all_model_functions(Model_functions, host, model)

print supplies