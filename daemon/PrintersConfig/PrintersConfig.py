__author__ = 'pferland'
from ConfigParser import ConfigParser
import os


class PrintersConfig:
    def __init__(self):
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
