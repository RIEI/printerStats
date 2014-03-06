__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2


def getstatus(shost):
    html = urllib2.urlopen("http://" + shost + "/web/guest/en/websys/webArch/getStatus.cgi")
    soup = BeautifulSoup(html.read())
    supplies = []
    for supply in soup.findAll("dd", {'class': 'listboxddm'}):
        #print str(supply) + "\r\n"
        supplies.append(supply.contents[1])
        break;
    return supplies