__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2


def getstatus(shost):
    html = urllib2.urlopen("http://" + shost + "/web/guest/en/websys/webArch/topPage.cgi")
    soup = BeautifulSoup(html.read())
    supplies = []
    for supply in soup.findAll("tr", {'class': 'staticProp'}):
        #print str(supply) + "\r\n"
        for found in supply.findAll("font"):
            supplies.append(found.contents[0])
        for item in supply.findAll("img", {"style" : "width:0px; height:0px;"}):
            supplies.append(item['alt'])
    return supplies