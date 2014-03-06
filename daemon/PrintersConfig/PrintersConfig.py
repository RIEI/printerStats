__author__ = 'pferland'
from ConfigParser import ConfigParser
import os, re
rg = re.compile('(.*?),', re.IGNORECASE | re.DOTALL)

class PrintersConfig:
    def __init__(self, folder):
        self.Config = ConfigParser()
        self.Config2 = ConfigParser()
        self.Config.read(os.path.join("/etc/printerstats/config", "printers.ini"))
        self.Config2.read(os.path.join("/etc/printerstats/config", "config.ini"))

    def ConfigMap(self, section):
        dict1 = {}
        options = self.Config2.options(section)
        for option in options:
            dict1[option] = self.Config2.get(section, option)
        return dict1


    def CampusMap(self, section):
        dict1 = {}
        options = self.Config.options(section)
        for option in options:
            dict1[section] = self.Config.get(section, option)
        return dict1


    def ConfigMapPrinters(self, section):
        dict1 = {section: {}}
        options = self.Config.options(section)
        for option in options:
            dict1[section][option] = self.Config.get(section, option)
        return dict1


    def generate_hosts_list(self, conn, campuses, printers):
        printer_campuses = []
        all_hosts = {}
        models = []
        i = 0
        for campus in campuses:
            row = conn.getcampusid(campus)
            if row:
                campus_id = row
            else:
                campus_id = conn.setcampusrow(campus)

            campus_printers = rg.findall(printers.get(campus.lower()))
            for printer in campus_printers:
                split = printer.split("|")
                all_hosts[i] = {0: split[0].replace("\n", ""), 1: split[1], 2: campus_id}
                printer_campuses.append(campus)
                i += 1
                if split[1] in models:
                    continue
                models.append(split[1])
        return models, printer_campuses, all_hosts