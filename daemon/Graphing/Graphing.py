__author__ = 'pferland'

import matplotlib.pyplot as plt
import sys, os
import datetime


class Graphing:
    def __init__(self, conn):
        self.conn = conn.conn

    def gplot(self, data, host_name, label):
        fig = plt.figure(num=None, figsize=(30, 20))
        ax = fig.add_axes((0.1, 0.2, 0.8, 0.7))
        ax.spines['right'].set_color('none')
        ax.spines['top'].set_color('none')

        if label == "Counts":
            len_data = len(data[0])
            low_limit = data[0][0]
            high_limit = data[0][len_data-1]
            data_range = high_limit - low_limit  # only need to cal for the page count, all others are 0-100
            calc = data_range/len_data
            rcalc = data_range/calc

            ax.set_ylim([low_limit-10, high_limit])
            yticks = range(low_limit, high_limit, rcalc)
        else:
            ax.set_ylim([0, 100])
            yticks = range(0, 100, 25)

        plt.yticks(yticks)

        days = (((data[1][-1] - data[1][0])/60)/60)/24

        a = datetime.datetime.today()
        dateList = []
        for x in range (0, days):
            dateList.append(str(a - datetime.timedelta(days = x)))

        dateList.reverse()
        xticks = []
        for x in range(0, len(dateList), int(round(len(dateList)/9, 0)+1)) :
            xticks.append(dateList[x])

        ax.set_xticklabels(xticks, rotation='vertical')

        plt.plot(data[0])

        plt.xlabel('time')
        plt.ylabel('Paper Count')
        fig.text(
            0.5, 0.05,
            host_name + " Feature: " + label,
            ha='center')
        plt.savefig(os.path.join("graphs", host_name+"_"+label+".png"))
        plt.close(fig)

    def graph(self, host_id, host_name):
        print "Graphing " + host_name

        ####################
        print " Feature: " + "Counts"
        data = self.conn.getcounts(host_id)
        if not data[0]:
            print "No data, skipping."
        else:
            self.gplot(data, host_name, "Counts")

        ####################
        print " Feature: " + "Tray 1"
        data1 = self.conn.gettray1(host_id)
        if not data1[0]:
            print "No data, skipping."
        else:
            self.gplot(data1, host_name, "Tray 1")

        ####################
        print " Feature: " + "Tray 2"
        data2 = self.conn.gettray2(host_id)
        if not data2[0]:
            print "No data, skipping."
        else:
            self.gplot(data2, host_name, "Tray 2")

        ####################
        print " Feature: " + "Tray 3"
        data3 = self.conn.gettray3(host_id)
        if not data[0]:
            print "No data, skipping."
        else:
            self.gplot(data3, host_name, "Tray 3")

        ####################
        print " Feature: " + "Toner"
        data4 = self.conn.gettoner(host_id)
        if not data[0]:
            print "No data, skipping."
        else:
            self.gplot(data4, host_name, "Toner")

        ####################
        print " Feature: " + "Maint Kit A"
        data5 = self.conn.getkita(host_id)
        if not data[0]:
            print "No data, skipping."
        else:
            self.gplot(data5, host_name, "Maint Kit A")

        ####################
        print " Feature: " + "Maint Kit B"
        data6 = self.conn.getkitb(host_id)
        if not data[0]:
            print "No data, skipping."
        else:
            self.gplot(data6, host_name, "Maint Kit B")