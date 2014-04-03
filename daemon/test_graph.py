__author__ = 'pferland'
from PrintersConfig import PrintersConfig
from Graphing import *
from PrinterStatsSQL import *

pcfg = PrintersConfig()
config = pcfg.ConfigMap("Daemon")

pSQL = PrinterStatsSQL(config)
graph = Graphing(pSQL, config['wwwroot'])

host_id = 3

host_name = pSQL.gethostname(host_id)

campus_name = pSQL.getprinterscampusname(host_id)

graph.graph(host_id, host_name, campus_name)