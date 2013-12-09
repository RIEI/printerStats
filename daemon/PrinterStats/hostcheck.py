__author__ = 'pferland'
import urllib2


def hostcheck(shost):
        try:
            code = urllib2.urlopen("http://" + shost).getcode()
            #print code
            if code != 200:
                return "down"
            return "up"
        except urllib2.URLError:
            return "down"