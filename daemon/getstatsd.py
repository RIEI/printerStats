__author__ = 'pferland'
__email__ = "pferland@randomintervals.com"
__lastedit__ = "2013-12-09"
print "PrinterStats Daemon v2.0 GPL V2.0 (05/12/2013) " + __author__ + " Email: " + __email__ + "Last Edit: " + __lastedit__
import sys, pymysql, re, PrinterStats, time, graphing

config = PrinterStats.ConfigMap("Daemon")
campuses = PrinterStats.CampusMap("Campuses")['Campuses'].split(",")
printers = PrinterStats.ConfigMapPrinters("Printers").get("Printers")

conn = pymysql.Connect(host=config['host'], user=config['db_user'], passwd=config['db_pwd'], db=config['db'], charset=config['collate'])
cur = conn.cursor()

rg = re.compile('(.*?),', re.IGNORECASE | re.DOTALL)

printer_campuses = []
printer_campus_ids = []
all_hosts = {}
models = []
i = 0

for campus in campuses:
    cur.execute("SELECT * FROM `printers`.`campuses` WHERE `campus_name` = %s", campus)
    row = cur.fetchone()
    if row:
        campus_id = row[0]
    else:
        cur.execute("INSERT INTO `printers`.`campuses` (`id`, `campus_name`) VALUES (NULL, %s)", campus)
        conn.commit()
        campus_id = cur.lastrowid

    campus_printers = rg.findall(printers.get(campus.lower()))
    for printer in campus_printers:
        split = printer.split("|")
        all_hosts[i] = {0: split[0].replace("\n", ""), 1: split[1], 2: campus_id}
        printer_campuses.append(campus)
        i += 1
        if split[1] in models:
            continue
        models.append(split[1])

Model_functions = PrinterStats.create_models_functions(models)

print "Checking Printers table Population."
PrinterStats.check_printers_table(Model_functions, conn, cur, all_hosts)


print "Moving on to the main loop."

while 1:
    for printer in all_hosts:
        print "---------------------"
        host = all_hosts[printer][0]
        model = all_hosts[printer][1]
        supplies = PrinterStats.daemon_get_host_stats(Model_functions, host, model)
        if supplies == -1:
            continue
        cur.execute("INSERT INTO `printers`.`history` ( `id`, `printer_id`, `timestamp`, `status`, `desc`, `tray_1`, "
                    "`tray_2`, `tray_3`, `count`, `toner`, `kit_a`, `kit_b` )"
                    "VALUES ( NULL, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )", (PrinterStats.getprinterid(cur, host), str(time.time()),
                                                                                    str(supplies[0][0]), str(supplies[0][1]), str(supplies[2][0]), str(supplies[2][1]), str(supplies[2][2]), str(supplies[1]),
                                                                                    str(supplies[3][0]), str(supplies[3][1]), str(supplies[3][2])))
        conn.commit()
