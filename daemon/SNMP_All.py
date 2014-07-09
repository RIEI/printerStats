from pysnmp import debug
from pysnmp.entity.rfc3413.oneliner import cmdgen

#debug.setLogger(debug.Debug('all'))
from pysnmp.entity.rfc3413.oneliner import cmdgen

cmdGen = cmdgen.CommandGenerator()

errorIndication, errorStatus, errorIndex, varBindTable = cmdGen.bulkCmd(
    cmdgen.CommunityData('public'),
    cmdgen.UdpTransportTarget(('10.2.138.30', 161)),
    0, 25,
    '1.3.6.1.2.1',
    '1.3.6.1.2.1.56',
)

if errorIndication:
    print(errorIndication)
else:
    if errorStatus:
        print('%s at %s' % (
            errorStatus.prettyPrint(),
            errorIndex and varBindTable[-1][int(errorIndex)-1] or '?'
            )
        )
    else:
        for varBindTableRow in varBindTable:
            for name, val in varBindTableRow:
                print('%s = %s' % (name.prettyPrint(), val.prettyPrint()))



#1.3.6.1.2.1.43.11.1.1.8.1.1 = 100
#1.3.6.1.2.1.43.11.1.1.8.1.2 = 100
#1.3.6.1.2.1.43.11.1.1.9.1.1 = 20
#1.3.6.1.2.1.43.11.1.1.9.1.2 = 100