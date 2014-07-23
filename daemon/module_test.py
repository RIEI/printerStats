#!/usr/bin/python
__author__ = 'pferland'
__email__ = "pferland@randomintervals.com"
__lastedit__ = "2014-jul-23"
print "PrinterStats Model Module Test Script \n\tAuthor: " + __author__ + "\n\tEmail: " + __email__ + "\n\tLast Edit: " + __lastedit__
import re, sys, time, socket, errno
from PrintersConfig import *
from PrinterStatsSQL import *
from PrinterStats import *

# INI file init, config/config.ini and config/printers.ini
pcfg = PrintersConfig()
config = pcfg.ConfigMap("Daemon")
campuses = pcfg.CampusMap("Campuses")['Campuses'].split(",")
printers = pcfg.ConfigMapPrinters("Printers").get("Printers")
rg = re.compile('(.*?),', re.IGNORECASE | re.DOTALL)
printer_campuses = []
printer_campus_ids = []
all_hosts = {}
models = []
i = 0

#SQL object init
conn = PrinterStatsSQL(config)
pStats = PrinterStats(conn)

print "Creating Model Functions"
Model_functions = pStats.create_models_functions(["ra8300"])

print "Testing Designated Module"
paper = Model_functions["ra8300"].getpaperlevels("wor-techctr-4")

print paper