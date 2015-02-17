#!/usr/bin/env python

#####################
# log_ex.py
#
# REF: http://code.google.com/p/logex/
# Modified by @xtr4nge (2013-11-12)
#####################

import os
import sys
import time
import base64
import urllib

class LogParser():

    def __init__(self, filePath='sslstrip.log', secureOnly=False):
        self.logins = []
        self.filePath = filePath
        self.passwords = ['Passwd=', 'passwd=', 'word=', 'pwd=', 'password]=', 'passwordTextBox=', 'theAccountPW=', 
                          'Password_Textbox=', 'pass=']
        self.usernames = ['user=', 'name=', 'mail=', 'login=', 'USERID=', 'emailTextBox=', 'email=', 'inputEmailHandle=', 
                          'theAccountName=', 'Email_Textbox=', 'UserName=', 'userid=', 'auth_userId=', 'email]=', 'email=']
        if secureOnly:
            self.postToken = 'SECURE POST'
        else:
            self.postToken = 'POST'
        
    def executeAll(self):
        if self.checkPath():
            self.loadFile()
            self.findSecurePosts()
        else:
            print ' [!] Input file does not exist, see --help'
    
    def checkPath(self):
        return os.path.exists(self.filePath) and os.path.isfile(self.filePath)
    
    def loadFile(self):
        log = open(self.filePath, 'r')
        self.data = log.readlines()
        log.close()
        #print ' [*] Loaded %s (%d lines)' % (self.filePath, len(self.data))

    def findSecurePosts(self):
        postsCaptured = 0
        sys.stdout.write('[*] Posts extracted: ')
        for line in self.data:
            try:
                if line.find(self.postToken) == -1:
                    continue
                else:
                    self.getDataFromLine(self.data.index(line), line)
                    postsCaptured += 1
            except:
                continue
        sys.stdout.write(str(postsCaptured) + '\n')
    
    def getDataFromLine(self, currentLine, line):
        entry = {}
        entry['url'] = line[line.find('(') + 1:line.find(')')] # Get URL
        entry['time_stamp'] = line[:line.find(',')] # Get date/time
        entry['raw_post'] = urllib.unquote(self.data[currentLine + 1]) # Get line after 'POST'
        entry['username'] = self.decodeString(entry['raw_post'], self.usernames)
        entry['password'] = self.decodeString(entry['raw_post'], self.passwords)
        self.logins.append(entry)
    
    def decodeString(self, string, tokens):
            for token in tokens: # Parse line for password strings
                if string.find(token) > -1:
                    start = string[string.find(token) + len(token):]
                    data = start[:start.find('&')]
                    return data
            return 'VALUE_NOT_FOUND'
    
class HtmlGenerator():
    ''' Generates an html document based on '''
    
    def __init__(self, logins):
        self.logins = logins
        self.raw = False
        self.base64 = False
        self.allPosts = False
        self.filePath = 'output.html'
    
    def build(self):
        #self.openFile()
        #self.writeHtmlHeader()
        #self.createHtmlTable()
        #self.writeHtmlTable()
        #self.writeHtmlFooter()
        self.writeOutput()
    
    def openFile(self):
        if os.path.exists(self.filePath):
            reply = raw_input(' [?] Overwrite existing %s file? [y/n]: ' % self.filePath).strip()
            if not reply == 'y' or reply == 'Y':
                print ' [!] User exit, please run again!'
                sys.exit()
        self.htmlFile = open(self.filePath, 'w')
    
    def writeHtmlHeader(self):
        self.htmlFile.write('<html><head><title>%s</title></head>\n' % self.filePath) # Write header info
        self.htmlFile.write('<body>\n') # Create body
        self.htmlFile.write('<h3>%s</h3><h4>Created using <a href="http://0x539.us/" target="newwin">log_ex.py</a></h4><p>\n' % self.filePath )
        
    def createHtmlTable(self):
        print ' [*] Building html file (%d possible entries)' % len(self.logins)
        self.htmlFile.write('<table border="1" cellpadding="5"><tr bgcolor="E0E0E0">') # Create table
        self.htmlFile.write('<th>Date/Time\n<th>URL\n<th>Username\n<th>Password\n')    # Create categories
        if self.base64:
            self.htmlFile.write('<th>Base64 Password Decode\n')
        if self.raw: 
            self.htmlFile.write('<th>Raw POST Data\n') # Create post data category, if enabled

    def writeHtmlTable(self):
        for entry in self.logins:
            if self.allPosts:
                self.writeTableEntry(entry)
            elif entry['username'] != 'VALUE_NOT_FOUND' and entry['password'] != 'VALUE_NOT_FOUND':
                self.writeTableEntry(entry)
        
    def writeTableEntry(self, entry):
        self.htmlFile.write('<tr>') # Create new line
        self.htmlFile.write('<td bgcolor="#FAF0F5"> %s' % entry['time_stamp']) # Write date / time
        self.htmlFile.write('<td bgcolor="#FAF0F5"> %s' % entry['url']) # Write URL
        self.htmlFile.write('<td bgcolor="#FAF0F5"> %s' % entry['username']) # Write username data
        self.htmlFile.write('<td bgcolor="#FAF0F5"> %s' % entry['password']) # Write password data
        if self.base64:
            try:
                self.htmlFile.write('<td bgcolor="#FAF0F5"> %s' % base64.standard_b64decode(entry['password']))
            except:
                self.htmlFile.write('<td bgcolor="#FAF0F5"> NONE')
        if self.raw: # Write raw post data if enabled
            self.htmlFile.write('<td bgcolor="#FAF0F5"> %s' % entry['raw_post'])
        self.htmlFile.write('\n') # End line
    
    def writeHtmlFooter(self):
        self.htmlFile.write('</table>\n</body>\n</html>\n') # End output
        self.htmlFile.close() # Close file
        
    def writeOutput(self):
        for entry in self.logins:
            if entry['username'] != 'VALUE_NOT_FOUND' and entry['password'] != 'VALUE_NOT_FOUND':
                print entry['time_stamp'] + " | " + entry['url'] + " | " + entry['username'] + " | " + entry['password']

class DaemonDisplay():
    
    def __init__(self, logParser):
        self.logParser = logParser
        self.refreshRate = 60
        self.displayed = []
    
    def executeAll(self):
        try:
            while True:
                self.checkLogins()
                time.sleep(self.refreshRate)
                self.logParser.executeAll()
        except KeyboardInterrupt:
            print '\n [*] User Exit'
        except:
            os._exit(1)
    
    def checkLogins(self):
        for entry in self.logParser.logins:
            if not entry in self.displayed:
                if entry['username'] != 'VALUE_NOT_FOUND' and entry['password'] != 'VALUE_NOT_FOUND':
                    self.displayNewLogin(entry['url'], entry['username'], entry['password'])
                self.displayed.append(entry)
    
    def displayNewLogin(self, url, username, password):
        durl = self.escapeString(url)
        duser = self.escapeString(username)
        dpass = self.escapeString(password)
        os.system('notify-send -t 95000 "Url: %s\nUsername: %s\nPassword: %s"' % (durl, duser, dpass))
        log = open('notify.log', 'a')
        log.write('Url: %s \n Username: %s \n Password: %s\n\n' % (url, username, password))
        log.close()
    
    def escapeString(self, string):
        string = string.replace('\\', '\\\\')
        string = string.replace('$', '\$')
        return string
        
# ============ [ Interface Code ] ============
authors = 'Moloch'
version = 'v0.3'

def banner():
    print '\n       -- [buffer]overflow Proudly Presents ---'
    print "     ooooo                                oooooooooooo             "
    print "     888'                                `888'     `8              "
    print "     888          .ooooo.   .oooooooo     888         oooo    ooo  "
    print "     888         d88' `88b 888' `88b      888oooo8     `88b..8P'   "
    print "     888         888   888 888   888      888             Y888'    "
    print "     888       o 888   888 `88bod88P      888       o   .o8''88b   "
    print "     o888ooooood8 `Y8bod8P' `8oooooo      o888ooooood8 o88'   888o "
    print "                                 YD   "
    print "                           Y88888P'       [%s by %s]\n" % (version, authors)

def help():
    banner()
    print ' Usage: log_ex.py file [options]'
    print ' Options:'
    print '\t-o, --out......................Specify an output file name'
    print '\t-t, --time.....................Add a time stamp to the output filename'
    print '\t-a, --all......................Include all possible logins'
    print '\t-r, --raw......................Include raw post data in the output'
    print '\t-b, --base64...................Include a base64 decode of passwords'
    print '\t-d, --daemon...................Run as daemon, only displays Username/Password'

def getArgument(token, arguments):
    index = 0
    try:
        for arg in arguments:
            if token in arg:
                return arguments[index + 1]
            index+=1
    except IndexError:
        print " [!] Error: Malformed command, see --help"
        sys.exit()

if __name__ == '__main__': 
    if len(sys.argv) == 1 or '-h' in sys.argv or '--help' in sys.argv:
        help()
        sys.exit()
    logp = LogParser(sys.argv[1])
    if logp.checkPath():
        logp.executeAll()
        if '-d' in sys.argv or '--daemon' in sys.argv:
            DaemonDisplay(logp).executeAll()
            sys.exit()
        else:
            htmlGen = HtmlGenerator(logp.logins)
            if '-a' in sys.argv or '--all' in sys.argv:
                htmlGen.allPosts = True
            if '-r' in sys.argv or '--raw' in sys.argv:
                htmlGen.raw = True
            if '-b' in sys.argv or '--base64' in sys.argv:
                htmlGen.base64 = True
            if '-o' in sys.argv or '--output' in sys.argv:
                htmlGen.filePath = getArgument('-o', sys.argv)
            if '-t' in sys.argv or '--time' in sys.argv:
                htmlGen.filePath = htmlGen.filePath + '_' + str(time.strftime('%H-%M-%S')) + '.html'
            try:
                htmlGen.build()
            except:
                print ' [!] An error occurred while building', htmlGen.filePath
            #os.popen('firefox %s' % htmlGen.filePath)
    else:
        print ' [!] Error: The log file (%s) does not exist, see --help' % sys.argv[1]