__author__ = 'pferland'
__email__ = "pferland@randomintervals.com"
__lastedit__ = "2013-12-11"
print "PrinterStats Daemon v2.0 GPL V2.0 (2013-12-11) \n\tAuthor: " + __author__ + "\n\tEmail: " + __email__ + "\n\tLast Edit: " + __lastedit__
import sys, re, os
from PrintersConfig import *
from PrinterStatsSQL import *
from PrinterStats import PrinterStats

folder = os.path.dirname(os.path.realpath(__file__))

# INI file init, config/config.ini and config/printers.ini
pcfg = PrintersConfig(folder)
config = pcfg.ConfigMap("Daemon")
campuses = pcfg.CampusMap("Campuses")['Campuses'].split(",")
printers = pcfg.ConfigMapPrinters("Printers").get("Printers")

#SQL object init
conn = PrinterStatsSQL(config)
pStats = PrinterStats(conn)
rg = re.compile('(.*?),', re.IGNORECASE | re.DOTALL)
printer_campuses = []
all_hosts = {}
models = []
i = 0
#models, printer_campuses, all_hosts = pcfg.generate_hosts_list(conn, campuses, printers)

for campus in campuses:
    row = conn.getcampusid(campus)
    if row == -1:
        campus_id = conn.setcampusrow(campus)
    else:
        campus_id = row
    campus_printers = rg.findall(printers.get(campus.lower()))
    for printer in campus_printers:
        split = printer.split("|")
        all_hosts[i] = {0: split[0].replace("\n", ""), 1: split[1], 2: campus_id}
        printer_campuses.append(campus)
        i += 1
        if split[1] in models:
            continue
        models.append(split[1])


Model_functions = pStats.create_models_functions(models)

print "----------Running Test of Printer Functions----------"
host = "10.2.138.30"
model = "ra8300"
print "----------For Host: " + host + "----------"
print "----------Model: " + model + "----------"

print Model_functions[model].getpaperlevels(host)