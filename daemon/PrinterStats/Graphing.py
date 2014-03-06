__author__ = 'pferland'

import matplotlib.pyplot as plt
import sys, os
import datetime
from matplotlib.ticker import FormatStrFormatter


class Graphing:
    def __init__(self, conn, www_path):
        self.conn = conn
        self.www_path = www_path

##############################################

    def gplot(self, data, host_name, label, campus_name):
        fig = plt.figure(num=None, figsize=(30, 20))

        border_width = 0.05
        ax_size = [0+border_width, 0+border_width,
                   1-2*border_width, 1-2*border_width]
        ax = fig.add_axes(ax_size)
                #ax = fig.add_axes((0.1, 0.2, 0.8, 0.7))
        ax.yaxis.set_major_formatter(FormatStrFormatter('%0.0f'))
        ax.spines['right'].set_color('none')
        ax.spines['top'].set_color('none')
        ax.grid(True)

        if label == "count":  # Only need to calculate yticks for the page count, all others are 0-100
            len_data = len(data[0])
            low_limit = data[0][0]
            high_limit = data[0][len_data-1]
            data_range = high_limit - low_limit
            #print len_data, low_limit, high_limit, data_range
            rcalc = (data_range/len_data)
            if data_range == 0 or rcalc == 0:
                low_limit -= len_data
                high_limit += len_data
                data_range = high_limit - low_limit
                rcalc = (data_range/len_data)

            #print len_data, low_limit, high_limit, data_range, rcalc

            ax.set_ylim([low_limit-10, high_limit+10])
            yticks = range(low_limit, high_limit, rcalc)
        else:
            #print data[0]
            ax.set_ylim([-2, 110])
            yticks = range(0, 125, 25)

        ################################################
        ################################################
        plt.yticks(yticks)

        xticks = []
        delta = data[1][-1] - data[1][0]
        #print delta # seconds of delta total

        spinterv = delta/100  # seconds per interval (100 intervals)

        #print spinterv
        stamp = data[1][0]
        for x in range(0, 100):
            xticks.append(datetime.datetime.fromtimestamp(stamp).strftime("%Y-%m-%d %H:%M:%S"))
            stamp += spinterv
        #print datetime.datetime.fromtimestamp(data[1][0]).strftime("%Y-%m-%d %H:%M:%S"), datetime.datetime.fromtimestamp(data[1][-1]).strftime("%Y-%m-%d %H:%M:%S")
        #print len(xticks)
        #print xticks

        plt.xticks(range(len(xticks)), xticks, rotation='vertical')

        plt.plot(data[0])

        plt.xlabel('time')
        plt.ylabel('Paper Count')
        fig.text(
            0.5, 0.05,
            host_name + " Feature: " + label,
            ha='center')
        plt.savefig(os.path.normpath(self.www_path + "/graphs/" + campus_name + "_" + host_name+"_" + label + ".png"))
        plt.close(fig)

##############################################################

    def graph(self, host_id, host_name, campus_name):
        sys.stdout.write("Graphing Feature: ")
        sys.stdout.flush()
        ####################
        sys.stdout.write("Counts")
        data = self.conn.getcounts(host_id)
        if not data[0]:
            sys.stdout.write(" (No data, skipping.), ")
        else:
            self.gplot(data, host_name, "count", campus_name)
        sys.stdout.flush()
        ####################
        sys.stdout.write(", Tray 1")
        data1 = self.conn.gettray1(host_id)
        if not data1[0]:
            sys.stdout.write(" (No data, skipping.), ")
        else:
            self.gplot(data1, host_name, "tray_1", campus_name)
        sys.stdout.flush()
        ####################
        sys.stdout.write(", Tray 2")
        data2 = self.conn.gettray2(host_id)
        if not data2[0]:
            sys.stdout.write(" (No data, skipping.), ")
        else:
            self.gplot(data2, host_name, "tray_2", campus_name)
        sys.stdout.flush()
        ####################
        sys.stdout.write(", Tray 3")
        data3 = self.conn.gettray3(host_id)
        if not data[0]:
            sys.stdout.write(" (No data, skipping.), ")
        else:
            self.gplot(data3, host_name, "tray_3", campus_name)
        sys.stdout.flush()
        ####################
        sys.stdout.write(", Toner")
        data4 = self.conn.gettoner(host_id)
        if not data[0]:
            sys.stdout.write(" (No data, skipping.), ")
        else:
            self.gplot(data4, host_name, "toner", campus_name)
        sys.stdout.flush()
        ####################
        sys.stdout.write(", Maint Kit A")
        data5 = self.conn.getkita(host_id)
        if not data[0]:
            sys.stdout.write(" (No data, skipping.), ")
        else:
            self.gplot(data5, host_name, "kit_a", campus_name)
        sys.stdout.flush()
        ####################
        sys.stdout.write(", Maint Kit B")
        data6 = self.conn.getkitb(host_id)
        if not data[0]:
            sys.stdout.write(" (No data, skipping.), ")
        else:
            self.gplot(data6, host_name, "kit_b", campus_name)
        sys.stdout.write("\n")
        sys.stdout.flush()