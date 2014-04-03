__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2, sys


def getstatus(shost):
    html = urllib2.urlopen("http://" + shost + "/web/guest/en/websys/webArch/topPage.cgi")
    soup = BeautifulSoup(html.read())
    status = ""
    for supply in soup.findAll("tr", {'class': 'staticProp'}):
        #print str(supply) + "\r\n"
        for item in supply.findAll("font"):
            status = item.contents[0]
    return status