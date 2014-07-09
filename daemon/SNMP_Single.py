from pysnmp.entity.rfc3413.oneliner import cmdgen

cmdGen = cmdgen.CommandGenerator()

errorIndication, errorStatus, errorIndex, varBinds = cmdGen.getCmd(
    cmdgen.CommunityData('public'),
    cmdgen.UdpTransportTarget(('10.2.138.30', 161)),
    '1.3.6.1.2.1.1.1.0', #Full Model
    '1.3.6.1.4.1.367.3.2.1.1.1.1.0', #Model Name
    '1.3.6.1.2.1.1.6.0', #Location
    '1.3.6.1.2.1.43.16.5.1.2.1.1', #Message
    '',
)

# Check for errors and print out results
if errorIndication:
    print(errorIndication)
else:
    if errorStatus:
        print('%s at %s' % (
            errorStatus.prettyPrint(),
            errorIndex and varBinds[int(errorIndex)-1] or '?'
            )
        )
    else:
        for name, val in varBinds:
            print('%s = %s' % (name.prettyPrint(), val.prettyPrint()))