__author__ = 'pferland'
from BeautifulSoup import BeautifulSoup
import urllib2
import re

def getpaperlevels(shost):
    html = urllib2.urlopen("http://" + shost + "/web/guest/en/websys/webArch/getStatus.cgi")
    soup = BeautifulSoup(html.read())
    supplies = []
    rg = re.compile('(?<=deviceSt)(.*?)(?=16)', re.IGNORECASE|re.DOTALL)
    i = 0
    for supply in soup.findAll("dd", {'class': 'listboxddm'}):
        #print str(supply) + "\r\n"
        for img in supply.findAll("img"):
            #print str(img) + "\r\n"
            m = rg.search(str(img))
            if m:
                level = m.group(1).replace("_", "").replace("P", "")
                print level
                if i == 3:
                    break
                print i
                if level == "end":
                    i += 1
                    supplies.append(str(level))
                if level.isdigit() is True:
                    i += 1
                    supplies.append(str(level))
                print "---------------"
    if supplies[2] == "Ot":
        #print "Bad results, try again."
        supplies = []
        i = 0
        for supply in soup.findAll("tr", {'class': 'staticProp'}):
            #print str(supply) + "\r\n"
            for img in supply.findAll("img"):
                #print str(img) + "\r\n"
                m = rg.search(str(img))
                if m:
                    i += 1
                    if i > 7:
                        break
                    if i < 5:
                        continue
                    #print i
                    level = m.group(1).replace("_", "").replace("P", "")
                    #print level
                    supplies.append(str(level))

    return supplies
