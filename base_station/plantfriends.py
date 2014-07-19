#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Plant Friends Master Python Script
# Main Python script that reads serial data and inserts into the database.
# Also does check to verify the data and spawn threads to send out alerts.
# 
# 
# Reads data from UART on Raspberry Pi and inserts the data into MYSQL database
# minicom check: minicom -b 115200 -o -D /dev/ttyAMA0
#
# Make sure serial port speed is 115200
#
# Put it in any dir. This script needs to be daemonized.
#
#
# Dickson Chow
# http://dicksonchow.com
#
#
# First Release: July 28, 2013.
# Updated: June 26, 2014.
#
#
# Released under the MIT License
#	http://opensource.org/licenses/mit-license.php
#
#


# Import modules
import time
import sys
import serial # allows us to use the serial port directly
import threading # allows us to do multithreading
import string
import smtplib # allows us to send out emails
import MySQLdb as mdb # interact with MySQL




# Declare database variables. Change accordingly
con = mdb.connect('localhost', 'plantuser', 'password', 'plantfriendsdb');


# Information for email. This will be used by the Plant Friends system to send out emails.
emailaddr = 'sent_from@emailaddress.here'
emailpass = 'smtp_password'


# These are email address that you want to send alerts TO. Email addresses are stored in an array.
emailList = ['personal_email@address.com','friends_email@address.com','PHONENUMBER@yourMobileCarrier.com']


# Delare serial port settings
ser = serial.Serial('/dev/ttyAMA0', 115200, timeout=0)


# This function is for sending out emails. 
def alertMail(msg):
	for emaildest in emailList:
		EMAIL_BODY = string.join((
			"From: %s" % emailaddr,
			"To: %s" % emaildest,
			"Subject: Haaaaaay",
			"",
			msg + " K' thanks.  \r\n - %s" % Alias,
			), "\r\n")
		server = smtplib.SMTP('smtp.gmail.com:587') # Gmail SMTP server address
		server.ehlo()
		server.starttls()
		server.login(emailaddr, emailpass)
		server.sendmail(Alias, [emaildest], EMAIL_BODY)
		server.quit()


# NodeID Check function. Returns boolean.
def nodeCheck(NodeID):
	global idex
	idex = -1
	for x in NodeCache:
		idex = idex + 1
		if NodeID == x[0]:
			return True

	
	

# Loads all the NodeID and Aliases from NodeIndex table into array
def loadNodes():
	global NodeCache
	with con:
		cur = con.cursor()
		cur.execute("SELECT NodeID, Alias FROM NodeIndex")
		NodeCache = cur.fetchall()

		
		
# First start up. Load NodeIndex
loadNodes()

		
# Open the serial port.
ser.open()
try:
	while 1:
		
		time.sleep(1); #Sleep for 1 sec to prevent node spamming (if a node goes crazy)
		
		# Read the serial buffer.
		serdata = ser.readline()
		
		
		# The serial port likes to insert random spaces so we just want the lines ones that actually have data.
		if len(serdata)>3:
			
			# referesh the node list
			loadNodes()
		
			# Clean the data. Strip any extra spaces.
			nodedata = serdata.strip()
			
			
			# Our data format uses colon to deliminate data types. We split them into an array here.
			nodedata = nodedata.split(':')
			nodedata = [x.strip('\x00') for x in nodedata]
						
						
			# Grab the NodeID from the message recieved
			NodeID = nodedata[0]
			NodeID = int(NodeID)

			
			# Check NodeID against the list in the database. If not true, reload index from database and check again
			if not nodeCheck(NodeID):
				loadNodes()
						
						
			# Second NodeID check.
			if nodeCheck(NodeID):

			
				# Grab the Alias from the cached index
				#print NodeCache[idex][1]
				Alias = NodeCache[idex][1]
				AliasID = Alias + str(NodeID)
				AliasID.replace(" ", "") #strip spaces!!

				
				# Break data out from array
				ErrorLvl = nodedata[1]
				SoilMoist = nodedata[2]
				TempC = nodedata[3]
				Humid = nodedata[4]
				Voltage = nodedata[5]
			
			
				# Check if the sensor node marked an error. Check the type of error. Generate message.
				if int(ErrorLvl) > 0:
					
					# Reset the email message
					ErrorMsg = " \r\n"
				
					# Soil moisture low
					if "1" in ErrorLvl:
						SoilMoistErr = "OMG so thirsty. \r\n"
						ErrorMsg = ErrorMsg + SoilMoistErr
				
					# Temperature error
					if "2" in ErrorLvl:
						TempCErr = "Temperature ERROR!  \r\n"
						ErrorMsg = ErrorMsg + TempCErr
				
					# Humidity error
					if "3" in ErrorLvl:
						HumidErr = "Humidity ERROR!  \r\n"
						ErrorMsg = ErrorMsg + HumidErr
				
					# Battery voltage low
					if "4" in ErrorLvl:
						VoltageErr = "Oh nos, we need more POWA!!  \r\n"
						ErrorMsg = ErrorMsg + VoltageErr

				
					# Send out email. This gets spawned as a thread so it doesn't block the rest of the program.
					OhNos = threading.Thread(target=alertMail(ErrorMsg))
					OhNos.start()
				
								
				# Check if a table for the NodeID exits. If not, create one.
				tableCheck = "SHOW TABLES LIKE '{}'".format (AliasID)
				with con:
					cur = con.cursor()
					cur.execute(tableCheck)
					tableResult = cur.fetchone()

					
				# Doesn't exist, create table. (invert boolean)
				if not tableResult:
					# print "no"
					with con:
						# Create the table
						nodeTable = "CREATE TABLE {}(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, DateTime TIMESTAMP, ErrorLvl INT, SoilMoist INT, TempC INT, Humid INT, Voltage FLOAT)".format (AliasID)
						# Insert dummy data for the first row
						nodeDummy = "INSERT INTO {}(DateTime, ErrorLvl, SoilMoist, TempC, Humid, Voltage) VALUES (CURRENT_TIMESTAMP,'0','0','0','0','0')".format (AliasID)
						cur = con.cursor()
						cur.execute(nodeTable)
						cur.execute(nodeDummy)
			
			
				# Insert sensor node data into the database
				datasql = "INSERT INTO {}(DateTime, ErrorLvl, SoilMoist, TempC, Humid, Voltage) VALUES (CURRENT_TIMESTAMP,'{}','{}','{}','{}','{}')".format (AliasID,ErrorLvl,SoilMoist,TempC,Humid,Voltage)
						
				with con:
					cur = con.cursor()
					cur.execute(datasql)
				
except KeyboardInterrupt:
	ser.close()

	




