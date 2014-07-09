__author__ = 'pferland'
import pymysql as mysql
import time


class PrinterStatsSQL:
    def __init__(self, config):
        self.conn = mysql.Connect(host=config['host'], user=config['db_user'], passwd=config['db_pwd'], db=config['db'], charset=config['collate'])
        self.cur = self.conn.cursor()
        self.limit = int(config['sql_limit'])
        if self.limit != 0:
            self.limit = " LIMIT " + str(self.limit)

    def setprintervalues(self, supplies, host, pid):
        self.cur.execute("INSERT INTO `printers`.`history` ( `id`, `printer_id`, `timestamp`, `status`, `tray_1`, "
                    "`tray_2`, `tray_3`, `count`, `toner`, `kit_a`, `kit_b` )"
                    "VALUES ( NULL, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )", (pid, str(time.time()), str(supplies[0]), str(supplies[2][0]),
                     str(supplies[2][1]), str(supplies[2][2]), str(supplies[1]), str(supplies[3][0]), str(supplies[3][1]), str(supplies[3][2])))
        self.conn.commit()
        return 1

    def setprinter(self, host_name, pid, model, supplies):
        if supplies == '':
            supplies = ['00:00:00:00:00:00', 'SN00000000']
        self.cur.execute("INSERT INTO `printers`.`printers` ( `id`, `name`, `mac`, `serial`, `model`, `campus_id` ) VALUES ( NULL, %s, %s, %s, %s, %s )", (host_name, supplies[0], supplies[1], model, pid))
        self.conn.commit()
        return 1

    def setprinteroffline(self, pid, host):
        self.cur.execute("INSERT INTO `printers`.`history` ( `id`, `printer_id`, `timestamp`, `status`) VALUES ( NULL, %s, %s, 'Offline') ", (pid, str(time.time()) ) )

    def updateprinter(self, host_name, model, supplies):
        if supplies == '':
            self.cur.execute("UPDATE `printers`.`printers` SET `model` = %s WHERE name = %s ", ( model, host_name))
            self.conn.commit()
        else:
            self.cur.execute("UPDATE `printers`.`printers` SET `mac` = %s, `serial` = %s, `model` = %s WHERE name = %s ", (supplies[0], supplies[1], model, host_name))
            self.conn.commit()
        return 1

    def setcampusrow(self, campus_name):
        self.cur.execute("INSERT INTO `printers`.`campuses` (`id`, `campus_name`) VALUES (NULL, %s)", campus_name)
        self.conn.commit()
        return self.cur.lastrowid



    def getcampusid(self, campus_name):
        self.cur.execute("SELECT `id` FROM `printers`.`campuses` WHERE `campus_name` = %s", campus_name)
        row = self.cur.fetchone()
        if not row:
            return -1
        return row[0]

    def getprinterscampusname(self, printer_id):
        self.cur.execute("SELECT `campus_name` FROM `campuses`, `printers` WHERE `campuses`.`id` = `printers`.`campus_id` and `printers`.`id` = %s", printer_id)
        row = self.cur.fetchone()
        return row[0]

    def getprinterid(self, host):
        self.cur.execute("SELECT * FROM `printers`.`printers` WHERE `name` = %s", host)
        row = self.cur.fetchone()
        if row:
            return int(row[0])
        else:
            return 0

    def gethostname(self, host_id):
        self.cur.execute("SELECT `name` from `printers`.`printers` WHERE `id` = %s", host_id)
        row = self.cur.fetchone()
        if row:
            return str(row[0])
        else:
            return -1

    def logError(self, host, message):
        self.cur.execute("INSERT INTO `printers`.`error_log` (`id`, `message`, `host`, `timestamp`) VALUES (NULL, %s, %s, UNIX_TIMESTAMP(NOW()) )", (host, message))
        row = self.cur.fetchone()
        if row:
            return int(row[0])
        else:
            return 0

    ##########################################################################
    ##########################################################################
    ##########################################################################

    def getcounts(self, host_id):
        self.cur.execute("SELECT `count`, `timestamp` from `printers`.`history` WHERE `printer_id` = %s AND count > 0 ORDER BY id DESC" + self.limit, (host_id))
        data = [[], []]
        for row in self.cur.fetchall():
            data[0].append(int(row[0]))
            data[1].append(int(row[1]))

        data[0].reverse()
        data[1].reverse()
        return data

    def gettray1(self, host_id):
        self.cur.execute("SELECT `tray_1`, `timestamp` from `printers`.`history` WHERE `printer_id` = %s ORDER BY id DESC" + self.limit, host_id)
        data = [[], []]
        for row in self.cur.fetchall():
            data[0].append(int(row[0]))
            data[1].append(int(row[1]))
        data[0].reverse()
        data[1].reverse()
        return data

    def gettray2(self, host_id):
        self.cur.execute("SELECT `tray_2`, `timestamp` from `printers`.`history` WHERE `printer_id` = %s ORDER BY id DESC" + self.limit, host_id)
        data = [[], []]
        for row in self.cur.fetchall():
            data[0].append(int(row[0]))
            data[1].append(int(row[1]))
        data[0].sort(key=int)
        data[1].sort(key=int)
        return data

    def gettray3(self, host_id):
        self.cur.execute("SELECT `tray_3`, `timestamp` from `printers`.`history` WHERE `printer_id` = %s ORDER BY id DESC" + self.limit, host_id)
        data = [[], []]
        for row in self.cur.fetchall():
            data[0].append(int(row[0]))
            data[1].append(int(row[1]))
        data[0].sort(key=int)
        data[1].sort(key=int)
        return data

    def gettoner(self, host_id):
        self.cur.execute("SELECT `toner`, `timestamp` from `printers`.`history` WHERE `printer_id` = %s ORDER BY id DESC" + self.limit, host_id)
        data = [[], []]
        for row in self.cur.fetchall():
            data[0].append(int(row[0]))
            data[1].append(int(row[1]))
        data[0].reverse()
        data[1].reverse()
        return data

    def getkita(self, host_id):
        self.cur.execute("SELECT `kit_a`, `timestamp` from `printers`.`history` WHERE `printer_id` = %s ORDER BY id DESC" + self.limit, host_id)
        data = [[], []]
        for row in self.cur.fetchall():
            data[0].append(int(row[0]))
            data[1].append(int(row[1]))
        data[0].reverse()
        data[1].reverse()
        return data

    def getkitb(self, host_id):
        self.cur.execute("SELECT `kit_b`, `timestamp` from `printers`.`history` WHERE `printer_id` = %s ORDER BY id DESC" + self.limit, host_id)
        data = [[], []]
        for row in self.cur.fetchall():
            data[0].append(int(row[0]))
            data[1].append(int(row[1]))
        data[0].reverse()
        data[1].reverse()
        return data