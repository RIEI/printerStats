__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2


def getphysicaladdress(shost):
    html = urllib2.urlopen("http://" + shost + "/hp/jetdirect")
    soup = BeautifulSoup(html.read())
    supplies = []
    for supply in soup.findAll("pre"):
        print supply

    return 0