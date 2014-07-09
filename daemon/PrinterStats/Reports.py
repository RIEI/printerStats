__author__ = 'pferland'
__email__ = "pferland@randomintervals.com"
__lastedit__ = "2014-01-24"

class Reports:
    def __init__(self, conn):
        self.conn = conn
        self.printer_name = ""
        self.printer_id = 0

    def start(self, printer_id):
        self.printer_name = self.conn.gethostname(printer_id)
        self.printer_id = printer_id
        return 0

    def reset(self):
        self.printer_id = 0
        self.printer_name = ""

    def gatherData(self):

