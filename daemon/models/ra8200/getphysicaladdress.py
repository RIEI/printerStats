__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2


def getphysicaladdress(shost):
    html = urllib2.urlopen("http://" + shost + "/web/guest/en/websys/netw/getInterface.cgi")
    soup = BeautifulSoup(html.read())
    i = 0
    for supply in soup.findAll("tr", {'class': 'editProp'}):
        for found in supply.findAll("td"):
            if i == 3:
                return found.contents[0]
            i += 1

