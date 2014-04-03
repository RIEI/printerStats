__author__ = 'pferland'
import sys
from collections import defaultdict
from types import ModuleType
import urllib2


class PrinterStats:
    def __init__(self, conn):
        self.conn = conn

    def create_models_functions(self, models):
        model_funcs = defaultdict(dict)
        for module_name in models:
            module = __import__("models." + module_name)
            for model_name, model_obj in vars(module).items():
                if model_name.startswith('_'):
                    continue
                if isinstance(model_obj, ModuleType):
                    #print model_obj, module_name
                    #print "models." + model_name
                    model_funcs[model_name] = model_obj
        return model_funcs

    def check_printers_table(self, model_functions, conn, all_hosts):
        print "---------------------"

        for printer in all_hosts:
            shost = all_hosts[printer][0]
            model = all_hosts[printer][1]
            pid = all_hosts[printer][2]
            row = conn.getprinterid(shost)
            check = self.hostcheck(shost)
            print shost + " is " + check
            if check == "down":
                if not row:
                    self.conn.setprinter(shost, pid, model, '')
                    self.conn.setprinteroffline(pid, shost)
                else:
                    self.conn.setprinteroffline(pid, shost)
                print "---------------------"
                continue

            supplies = []
            print "Gathering Mac Address."
            mac = model_functions[model].getphysicaladdress(shost)
            if mac == -1:
                print "Host is not returning correct pages, check host."
                print "---------------------"
                return -1
            supplies.append(mac)

            print "Gathering Serial."
            model_functions[model].getserialnumber
            serial = model_functions[model].getserialnumber(shost)
            if serial == -1:
                print "Host is not returning correct pages, check host."
                print "---------------------"
                return -1
            supplies.append(serial)

            if supplies == -1:
                print "Empty Supplies list."
                print "---------------------"
                continue
            if not row:
                self.conn.setprinter(shost, pid, model, supplies)
            else:
                self.conn.updateprinter(shost, model, supplies)
            print "---------------------"
        return 1

    def daemon_get_host_stats(self, model_functions, shost, model):
        supplies = []
        check = self.hostcheck(shost)
        print shost + " is " + check
        if check == "down":
            return -1
        else:
            sys.stdout.write("Gathering Data: Status")
            sys.stdout.flush()
            status = model_functions[model].getstatus(shost)
            if status == -1:
                print "Host is not returning correct pages, check host."
                return -1
            supplies.append(status)

            sys.stdout.write(", Counter")
            sys.stdout.flush()
            counter = model_functions[model].getcounter(shost)
            if counter == -1:
                print "Host is not returning correct pages, check host."
                return -1
            supplies.append(counter)

            sys.stdout.write(", Paper Levels")
            sys.stdout.flush()
            paper = model_functions[model].getpaperlevels(shost)
            if paper == -1:
                print "Host is not returning correct pages, check host."
                return -1
            supplies.append(paper)

            sys.stdout.write(", Supply Levels")
            sys.stdout.flush()
            supply = model_functions[model].getsupplies(shost)
            if supply == -1:
                print "Host is not returning correct pages, check host."
                return -1
            supplies.append(supply)
            sys.stdout.write("\n")
            sys.stdout.flush()
            return supplies

    def hostcheck(self, shost):
        try:
            code = urllib2.urlopen("http://" + shost).getcode()
            #print code
            if code != 200:
                return "down"
            return "up"
        except urllib2.URLError:
            return "down"