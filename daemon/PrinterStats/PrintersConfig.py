__author__ = 'pferland'
from ConfigParser import ConfigParser
Config = ConfigParser()
Config2 = ConfigParser()
Config.read("config\printers.ini")
Config2.read("config\config.ini")


def ConfigMap(section):
    dict1 = {}
    options = Config2.options(section)
    for option in options:
        dict1[option] = Config2.get(section, option)
    return dict1


def CampusMap(section):
    dict1 = {}
    options = Config.options(section)
    for option in options:
        dict1[section] = Config.get(section, option)
    return dict1


def ConfigMapPrinters(section):
    dict1 = {section: {}}
    options = Config.options(section)
    for option in options:
        dict1[section][option] = Config.get(section, option)
    return dict1
