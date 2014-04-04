__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2


def getstatus(shost):
    html = urllib2.urlopen("http://" + shost + "/web/guest/en/websys/webArch/getStatus.cgi")
    soup = BeautifulSoup(html.read())
    status = ""
    for supply in soup.findAll("dd", {'class': 'listboxddm'}):
        if len(supply.contents) > 1:
            status = str(supply.contents[1])
        else:
            status = str(supply.contents[0])
        break;
    return status