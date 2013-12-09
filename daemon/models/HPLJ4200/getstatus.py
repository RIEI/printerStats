__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2


def getstatus(shost):
    html = urllib2.urlopen("http://" + shost + "/hp/device/this.LCDispatcher?dispatch=html&cat=0&pos=0")
    soup = BeautifulSoup(html.read())
    supplies = []
    for supply in soup.findAll("tr", {'class': 'staticProp'}):
        print str(supply) + "\r\n"

    return 0