__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2
import re

def getpaperlevels(shost):
    html = urllib2.urlopen("http://" + shost + "/hp/device/this.LCDispatcher?dispatch=html&cat=0&pos=1")
    soup = BeautifulSoup(html.read())
    supplies = []
    for supply in soup.findAll("td", {'headers': 'Status'}):
        print str(supply) + "\r\n"

    return 0
