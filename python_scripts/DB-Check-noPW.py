# This connects to the CUCM and checks the DB Status
# If errors are found, it will connect to the server again and initiate a repair
import pexpect
import sys
import re


# GLOBAL VARS
host = '10.134.174.10'
password = '*******' 


# Global VARS
def db_fix():
    if 'Mismatches Were Found!!!' in open('DB-check-log2.txt').read():
        error_check = True
    else:
        error_check = False
    return(error_check)

fout = open('DB-check-log.txt', 'wb')

print('Opening Connection...')
child = pexpect.spawn('ssh Administrator@%s' % host)
# child.logfile = sys.stdout

child.logfile = fout

child.expect('password: ')
print(child.before)
child.sendline(password)
child.expect('admin:')
print(child.before)
child.sendline('utils dbreplication runtimestate')
child.expect('admin:', timeout=90)
print(child.before)
child.sendline('exit')
print('Check complete.  Exiting.  Check log file for results...')

if db_fix():
    fout = open('DB-fix.txt', 'wb')
    print("NEED TO FIX DB.  Connecting now...")
    child = pexpect.spawn('ssh Administrator@%s' % host)
    child.logfile = fout
    child.expect('password: ')
    print(child.before)
    child.sendline(password)
    child.expect('admin:')
    print(child.before)
    child.sendline('utils dbreplication repair')
    child.expect('admin:', timeout=-1)
    print(child.before)
    child.sendline('exit')
    print('FIX COMPLETE.  Exiting.  Check log file for results...')

