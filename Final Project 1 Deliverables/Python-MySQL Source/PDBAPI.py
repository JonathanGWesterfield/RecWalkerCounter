"""
This file is the Python API that allows the Arduino to
communicate with the database. It allows one way communication
from the Arduino pushing data to the database. dbConnect()
must be called before insert will work. Otherwise,
it will throw an exception (that gets caught within the function itself).
"""

#####################################################################################################################
#                                COPY AND PAST THESE FUNCTIONS AT THE TOP OF THE FILE                               #
#####################################################################################################################

## @file PDBAPI.py

import mysql.connector
from mysql.connector import errorcode

def insert(cnx, cursor, inOrOut):
    """
    This function inserts an entry into the database. Will insert the location (should
    be the Student Recreation Center but can be changed), a boolean value as to whether
    the subject exited or entered, the day of the week in number format (using the mysql
    dayofweek(now()) function) and a DateTime stamp (using mysql now() function)
    :param cnx:
    :param cursor:
    :param inOrOut: True=In, False=Out
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
        cnx = mysql.connector.connect(user='XXXXX', password='Whoop19!', host='database.cse.tamu.edu', database='XXXXX-WalkerData')
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


#####################################################################################################################################################

################################                         TEST CODE                         ##########################################################

#####################################################################################################################################################
# This test case is sparse, however, there are try-catch blocks in the python code that are the real "unit tests"
dbConnect()
if (insert(cnx, cursor, True)):
    print("ERROR!")
else:
    print("IT WORKED!")
cnx.close()


