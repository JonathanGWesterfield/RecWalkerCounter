#!/usr/bin/env python #LINUX?

import time
import sys
import signal
import mysql.connector

from PyMata.pymata import PyMata
from mysql.connector import errorcode

# Create a PyMata instance
board = PyMata("/dev/ttyACM0", verbose=True)  # Change port to COM# (look in device manager) for Windows


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
    """
    Connects to the database using my credentials and the mysql password. Will print out a string
    depeding on the status of the connection i.e.: whether if failed or not and why.
    :return:
    """
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

			
def calibDist():
    """
    This function reads data from the sensors for a specified amount of time and then takes the average
    of those values to find the distance to check against for comparison (sensor-to-wall distance).
    :return: integer
    """
    totalDist = 0
    measurements = 0
    t_end = time.time() + 5  # Run for 5 seconds
    print("Performing distance calibration, please wait!")
    while time.time() < t_end:
        data = board.get_sonar_data()
        totalDist += data[pin1][1]
        measurements += 1

    distance = totalDist / measurements
    print("Distance: " + str(distance))
    print("Measurements: " + str(measurements))
    return distance


def readIn(pin):
    """
    This function reads the data from the ultrasonic sensor associated with the passed-in Arduino pin for 1/10th of
    a second and calculates the average distance for those measurements. This is to provide more accurate data to
    reconcile the slight inaccuracy of the sensors. Note: ~100k measurements are taken in 1/10th of a second.
    :param pin:
    :return:
    """
    totalDist = 0
    measurements = 0
    t_end = time.time() + .1  # Run for distanceToCheck seconds
    while time.time() < t_end:
        data = board.get_sonar_data()
        # Check for bad sensor read, loop until we get good data
        while data[pin][1] == 0:
            # print ("BAD DATA!")
            data = board.get_sonar_data()
        totalDist += data[pin][1]
        measurements += 1

    avgDistance = totalDist / measurements
    # print("Distance: " + str(distance))
    # print("Measurements: " + str(measurements))
    return avgDistance


def readFromArduino(distanceToCheck):
    """
    This function reads data in from HC-SR04 (ultrasonic) sensors and registers a pedestrian
    entering/exiting based on proximity in comparison to "distanceToCheck" and which sensor is triggered first.
    It inserts this data into the MySQL database, passing in whether the pedestrian was entering or exiting.
    :param distanceToCheck:
    :return:
    """

    # print("Check against: " + str(distanceToCheck))
    distance1 = readIn(pin1)
    distance2 = readIn(pin2)
    hit1 = 0
    hit2 = 0

    # Check distances for both sensors, trip at close distances
    if distance1 < distanceToCheck:
        timeout = time.time() + 2
        # print(str(data[pin1][1]) + 'CM on sensor 1')
        print("Entering...")  # Debug
        hit1 = 1

    if distance2 < distanceToCheck:
        timeout = time.time() + 2
        # print(str(data[pin2][1]) + 'CM on sensor 2')
        print("Exiting...")  # Debug
        hit2 = 1

    # Entering has been triggered, wait to complete before reading again
    while hit1 == 1 and hit2 == 0 and time.time() < timeout:
        dist = readIn(pin2)
        if dist < distanceToCheck:
            hit1 = 0
            hit2 = 0
            print("ENTERED! Inserting into DB...")  # Debug
            # insert(cnx, cursor, True)
            time.sleep(.4)
            break

    # Exiting has been triggered, wait to complete before reading again
    while hit1 == 0 and hit2 == 1 and time.time() < timeout:
        dist = readIn(pin1)
        if dist < distanceToCheck:
            hit1 = 0
            hit2 = 0
            print("EXITED! Inserting into DB...")  # Debug
            # insert(cnx, cursor, False)
            time.sleep(.4)
            break			
			

# Interrupt signal handler - May need to press ctrl c twice
def signalHandler(sig, frame):
    print('You pressed Ctrl+C')
    cnx.close()
    if board is not None:
        board.reset()
        board.close()
    sys.exit(0)


signal.signal(signal.SIGINT, signalHandler)

# Connect to the MySQL database
dbConnect()

# Sensor pins: pin1 = entering, pin2 = exiting
pin1 = 12
pin2 = 13

# Configure the trigger and echo pins for both sensors and wait to finish
board.sonar_config(pin1, pin1)
board.sonar_config(pin2, pin2)
time.sleep(1)

distToCheck = calibDist() - 10  # Leave 10 CM for sensor inaccuracy "padding"

# Loop to read from ultrasonic sensors and add data to DB
while 1:
    readFromArduino(distToCheck)
    time.sleep(.2)
