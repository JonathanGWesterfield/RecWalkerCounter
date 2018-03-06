###########################################################################################################
#COPY AND PAST THESE FUNCTIONS
###########################################################################################################

import mysql.connector
from mysql.connector import errorcode

def insert(cnx, cursor, inOrOut):
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
    try:
        # declare cnx and cursor as global so they can be used in other functions
        global cnx
        global cursor

        # make the connection
        cnx = mysql.connector.connect(user='jgwesterfield', password='Whoop19!', host='database.cse.tamu.edu', database='jgwesterfield-WalkerData')
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

# test code
dbConnect()
if (insert(cnx, cursor, True)):
    print("ERROR!")
else:
    print("IT WORKED!")
cnx.close()

"""class PDBAPI:

    __config = {
        'user': 'jgwesterfield',
        'password': 'Whoop19!',
        'host': 'database.cse.tamu.edu',
        'database': 'jgwesterfield-WalkerData'
    }

    __config = {
        'user': 'root',
        'password': 'root',
        'host': 'localhost',
        'database': 'WalkerProject'
    }

    # class constructor connefts to the database
    def __init__(self):
        try:
            self.cnx = mysql.connector.connect(user='root', password='root', host='localhost', database='WalkerProject')
            self.cursor = self.cnx.cursor()

        except mysql.connector.Error as err:
            if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
                print("Something is wrong with your user name or password")
            elif err.errno == errorcode.ER_BAD_DB_ERROR:
                print("Database does not exist")
            else:
                print(err)
        else:
            self.cnx.close()

    def __del__(self):
        self.cnx.close()
        print("Closing Database Connection")

    # inserts the Location, InOrOut, Weekday, Datetime and timestamp into the database
    def insert(self, inOrOut):
        try:
            sql = "INSERT INTO WalkerData (Location, InOrOut, WeekDay, DateTime) VALUES (\"Student Recreation Center\", "
            sql += str(inOrOut) + ", dayofweek(now()), now())"


            print("Attempting to Insert into the Database: ")


            print(sql)
            print("\n")
            self.cursor.execute(sql)
            self.cnx.commit()
            return False

        except mysql.connector.Error as err:
            # say it missed it an move on
            print("Something went wrong: {}".format(err))
            return True


dbConn = PDBAPI()

if dbConn.insert(True):
    print("ERROR!!!")
else:
    print("It's Good")"""

