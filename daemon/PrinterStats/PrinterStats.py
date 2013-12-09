__author__ = 'pferland'
import sys
from collections import defaultdict
from types import ModuleType
import hostcheck


def getprinterid(cur, host):
    cur.execute("SELECT * FROM `printers`.`printers` WHERE `name` = %s", host)
    row = cur.fetchone()
    if row:
        return row[0]
    else:
        return 0


def create_models_functions(models):
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


def check_printers_table(model_functions, conn, cur, all_hosts):
    for printer in all_hosts:
        #print "---------------------"
        shost = all_hosts[printer][0]
        model = all_hosts[printer][1]
        pid = all_hosts[printer][2]
        row = getprinterid(cur, shost)
        if not row:
            print shost
            check = hostcheck.hostcheck(shost)
            print shost + " is " + check
            if check == "down":
                continue

            supplies = []
            print "Gathering Mac Address."
            mac = model_functions[model].getphysicaladdress(shost)
            if mac == -1:
                print "Host is not returning correct pages, check host."
                return -1
            supplies.append(mac)

            print "Gathering Serial."
            model_functions[model].getserialnumber
            serial = model_functions[model].getserialnumber(shost)
            if serial == -1:
                print "Host is not returning correct pages, check host."
                return -1
            supplies.append(serial)

            if supplies == -1:
                continue
            cur.execute("INSERT INTO `printers`.`printers` ( `id`, `name`, `mac`, `serial`, `model`, `campus_id` ) VALUES ( NULL, %s, %s, %s, %s, %s )", (shost, supplies[0], supplies[1], model, pid))
            conn.commit()
        #else:
            #print row
    return 1


def daemon_get_host_stats(model_functions, shost, model):
    supplies = []
    check = hostcheck.hostcheck(shost)
    print shost + " is " + check
    if check == "down":
        return -1
    else:
        print "Gathering Status."
        status = model_functions[model].getstatus(shost)
        if status == -1:
            print "Host is not returning correct pages, check host."
            return -1
        supplies.append(status)

        print "Gathering Counter."
        counter = model_functions[model].getcounter(shost)
        if counter == -1:
            print "Host is not returning correct pages, check host."
            return -1
        supplies.append(counter)

        print "Gathering Paper Levels."
        paper = model_functions[model].getpaperlevels(shost)
        if paper == -1:
            print "Host is not returning correct pages, check host."
            return -1
        supplies.append(paper)

        print "Gathering Supply Levels."
        supply = model_functions[model].getsupplies(shost)
        if supply == -1:
            print "Host is not returning correct pages, check host."
            return -1
        supplies.append(supply)
        return supplies