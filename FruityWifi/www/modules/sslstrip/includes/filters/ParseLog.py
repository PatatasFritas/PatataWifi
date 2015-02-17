#!/usr/bin/env python

#####################
# ParseLog.py
#
# By z3ros3c@gmail.com
# Modified by @xtr4nge (2013-11-12)
#####################

""" This file parses the sslstrip.log created by
    sslstrip for usernames and passwords (and other
    interesting information) defined in the file
    resources/definitions.sslstrip. It will also
    give you a complete list of all unknown information,
    with the exception of anything listed in the file
    resources/blacklist.sslstrip.
"""

import sys
from urllib import unquote

getIP = lambda origin: origin[origin.find('(')+1:origin.find(')')]

blacklist = []
accounts = []
definitions = {}
sslstrip_log = sys.argv[1]

resources_path = sys.argv[2]

def getDefs(defs):
  d = {}
  for definition in defs:
    tmp = definition.split('|')
    a = tmp.pop(0)
    b = tmp.pop()
    if('\n' in b):
      b = b[:-1]
    tmp.append(b)
    d[a] = tmp[:]
  return d

def getAllVars(line):
  while('&&' in line):
    line = line.replace('&&','&')
  vars = {}
  tmp = line.split('&')
  for var in tmp:
    try:
      (a,b) = var.split('=')
      if('$' in unquote(a)):
        a = unquote(a).split('$').pop()
      if('\n' in unquote(b)):
        b = unquote(b)[:-1]
      vars[unquote(a)] = unquote(b)
    except:
      pass
  return vars

def process(origin,line):
  origin = getIP(origin)
  if(origin not in blacklist):
    vars = getAllVars(line)
    if(origin in definitions):
      definition = definitions[origin][:]
      name = definition.pop(0)
      account = "**(%s) | " % name
      #account = "%s | " % name
      for variable in definition:
        try:
          v = vars[variable]
        except:
          v = 'UNDEFINED'
        #account += "%s = %s :: " % (variable,v)
        account += "%s = %s | " % (variable,v)
      if('UNDEFINED' not in account):
        if(account not in accounts):
          accounts.append(account)
          #account += "**NEW**"
        print(account)
    else:
      print("Unknown:\t%s" % origin)
      for var in vars:
        if(vars[var] != ""):
          print("\t%s:\t%s" % (var,vars[var]))
try:
  lines = open(sslstrip_log,'r').readlines()
except:
  lines = []
try:
  blacklist = open(resources_path + '/resources/blacklist.sslstrip','r').read().split('\n')
except:
  print("--blacklist not defined--")
try:
  accounts = open('accounts.txt','r').read().split('\n')
except:
  pass
try:
  definitions = getDefs(open(resources_path + '/resources/definitions.sslstrip','r').readlines())
except:
  pass

try:
  line = lines.pop(0)
  while(1):
    while('POST' not in line):
      try:
        line = lines.pop(0)
      except:
        break
    process(line,lines.pop(0))
    try:
      line = lines.pop(0)
    except:
      break
except:
  ''''''
  #print("Empty logfile.")

'''
output = open('accounts.txt','w')
accounts.sort()
for account in accounts:
  if(account != ''):
    output.write(account + '\n')
'''