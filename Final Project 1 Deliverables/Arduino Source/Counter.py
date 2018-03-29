#!/usr/bin/env python #LINUX?

import time
import sys
import signal
import mysql.connector

from PyMata.pymata import PyMata
from mysql.connector import errorcode


def insert(cnx, cursor, inOrOut):
    """
    This function inserts an entry into the database. Will insert the location (should be the Student Recreation Center
    but can be changed), a boolean value as to whether the subject exited or entered, the day of the week in number
    format (using the mysql dayofweek(now()) function) and a DateTime stamp (using mysql now() function)
    :param cnx:
    :param cursor:
    :param inOrOut:
    :return: Boolean
    """
    try:
        sql = "INSERT INTO WalkerData (Location, InOrOut, WeekDay, DateTime) VALUES (\"Student Recreation Center\", "
        sql += str(inOrOut) + ", dayofweek(now()), now())"

        print("Attempting to Insert into the Database: ")

        print(sql)
        print("\n")
        cursor.execute(sql)
        cnx.commit()
        return False

    except mysql.connector.Error as err:
        # say it missed it an move on
        print("Something went wrong: {}".format(err))
        return True


def dbConnect():
    '''
    Connects to the database using my credentials and the mysql password. Will print out a string
    depeding on the status of the connection i.e.: whether if failed or not and why.
    :return:
    '''
    try:
        # declare cnx and cursor as global so they can be used in other functions
        global cnx
        global cursor

        # make the connection
        cnx = mysql.connector.connect(user='jgwesterfield', password='Whoop19!', host='database.cse.tamu.edu',
                                      database='jgwesterfield-WalkerData')
        cursor = cnx.cursor()
        print ("DB Connected")
        return

    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
            cnx.close()
            return
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
            cnx.close()
            return
        else:
            print(err)
            cnx.close()
            return

# Connect to the MySQL database
dbConnect()

# Create a PyMata instance
board = PyMata("/dev/ttyACM0", verbose=True)  # Change port to COM# (look in device manager) for Windows

def readfromArduino():
    """
    This function reads data in from HC-SR04 (ultrasonic) sensors and registers a pedestrian
    entering/exiting based on proximity and which sensor is triggered first. It inserts this data
    into the MySQL database, passing in whether the pedestrian was entering or exiting
    :return:
    """
    data = board.get_sonar_data()

    distance1 = data[12][1]
    distance2 = data[13][1]
    hit1 = 0
    hit2 = 0

    # Check distances for both sensors, trip at close distances
    if (distance1 < 15):
        print(str(data[12][1]) + 'CM on sensor 1')
        print("Entering...") #Debug
        hit1 = 1

    if (distance2 < 15):
        print(str(data[13][1]) + 'CM on sensor 2')
        print("Exiting...") #Debug
        hit2 = 1

    # Entering has been triggered, wait to complete before reading again
    while hit1 == 1 and hit2 == 0:
        dist = 0
        data = board.get_sonar_data()
        dist = data[13][1]
        if (dist < 15):
            hit1 = 0
            hit2 = 0
            print("ENTERED! Inserting into DB...") #Debug
            insert(cnx, cursor, True)
            time.sleep(.4)
            break

    # Exiting has been triggered, wait to complete before reading again
    while hit1 == 0 and hit2 == 1:
        dist = 0
        data = board.get_sonar_data()
        dist = data[12][1]
        if (dist < 15):
            hit1 = 0
            hit2 = 0
            print("EXITED! Inserting into DB...") #Debug
            insert(cnx, cursor, False)
            time.sleep(.4)
            break

# Interrupt signal handler - May need to press ctrl c twice
def signal_handler(sig, frame):
    print('You pressed Ctrl+C')
    cnx.close()
    if board is not None:
        board.reset()
        board.close()
    sys.exit(0)

signal.signal(signal.SIGINT, signal_handler)

# Configure the trigger and echo pins - Sensor 1
board.sonar_config(12, 12)

# Configure the trigger and echo pins - Sensor 2
board.sonar_config(13, 13)
time.sleep(1)

# Loop to read from ultrasonic sensors and add data to DB
while 1:
    readfromArduino()
    time.sleep(.2)