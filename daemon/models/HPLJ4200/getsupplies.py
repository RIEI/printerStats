__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2


def getsupplies(shost):
    html = urllib2.urlopen("http://" + shost + "/hp/device/this.LCDispatcher?dispatch=html&cat=0&pos=2")
    soup = BeautifulSoup(html.read())
    supplies = []
    for supply in soup.findAll("table", {'bgcolor': '#000000'}):
        print str(supply) + "\r\n"

    return 0