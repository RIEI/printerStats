__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2


def getserialnumber(shost):
    html = urllib2.urlopen("http://" + shost + "/web/guest/en/websys/status/configuration.cgi")
    soup = BeautifulSoup(html.read())
    i = 0
    ii = 0
    for supply in soup.findAll("tr", {'class': 'staticProp'}):
        if i == 9:
            for found in supply.findAll("td"):
                if ii == 3:
                    return str(found.contents)
                ii += 1
        i += 1
