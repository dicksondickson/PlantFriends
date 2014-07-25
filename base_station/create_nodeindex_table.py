#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Plant Friends
# Create NodeIndex Table script
#
#
# Dickson Chow
# http://dicksonchow.com
#
# First Release: July 18, 2013.
# Updated: June 24, 2014.
#
# MIT License
#	http://opensource.org/licenses/mit-license.php
#
#

import MySQLdb as mdb

### Change the username, password and the database name accordingly
con = mdb.connect('localhost', 'plantuser', 'password', 'plantfriendsdb');

with con:

	cur = con.cursor()
	
	### Prepare the SQL statement to be executed
	nodeindexcreate = "CREATE TABLE NodeIndex(NodeID INT PRIMARY KEY, Alias VARCHAR(50), Location VARCHAR(200), Plant VARCHAR(200), Comments VARCHAR(1000))"

	cur = con.cursor()
	
	### This will delete the existing NodeIndex table if it exists in the database.
	### Useful if you want to reset the whole table. Uncomment to use.
	#cur.execute("DROP TABLE IF EXISTS NodeIndex")
	
	### Execute our prepared statement
	cur.execute(nodeindexcreate)