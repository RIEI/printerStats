__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2
import re

def getcounter(shost):
    html = urllib2.urlopen("http://" + shost + "/web/guest/en/websys/status/getUnificationCounter.cgi")
    soup = BeautifulSoup(html.read())
    i = 0
    for supply in soup.findAll("tr", {'class': 'staticProp'}):
        i += 1
        for row in supply.findAll(text=re.compile('\d')):
            count = int(row)
            break
        if i == 2:
            break
    return count