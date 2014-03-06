__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2


def getsupplies(shost):
    html = urllib2.urlopen("http://" + shost + "/web/guest/en/webprinter/supply.cgi")
    soup = BeautifulSoup(html.read())
    supplies = []
    for supply in soup.findAll("td", {'bgcolor': '#CCCCCC'}):
        img = supply.find("img")
        width = round((((float(img['width']))/162)*100), 2)
        supplies.append(str(width))
        #print img['width']
    return supplies