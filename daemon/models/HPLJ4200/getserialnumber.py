__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2


def getserialnumber(shost):
    html = urllib2.urlopen("http://" + shost + "/hp/device/this.LCDispatcher?dispatch=html&cat=0&pos=4")
    soup = BeautifulSoup(html.read())
    supplies = []
    for supply in soup.findAll("font", {'face': 'Helvetica,Arial'}):
        print str(supply) + "\r\n"

    return 0