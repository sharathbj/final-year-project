#!/usr/bin/env python
import socket
import re

# Standard socket stuff:
host ='127.0.0.5'# do we need socket.gethostname() ?
port = 8085
sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
sock.bind((host, port))
print "host address " + host 
print "waiting for server"
sock.listen(1) # don't queue up any requests

# Loop forever, listening for requests:
while True:
    csock, caddr = sock.accept()
    print "Connection from: " + `caddr`
    req = csock.recv(1024) # get the request, 1kB max
    
    	
    print "Message:",req
    execfile("/var/www/html/project43/sampleDemo.py")
