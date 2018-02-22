import mysql.connector
from mysql.connector import errorcode

class PDBAPI:
    __config = {
        'user': 'root',
        'password': 'root',
        'host': 'localhost:8889',
        'database': 'WalkerProject'
    }

    # class constructor connects to the database
    def __init__(self):
        try:
            self.__cnx = mysql.connector.connect(**PDBAPI.__config)
            self.__cursor = self.__cnx.cursor()

        except mysql.connector.Error as err:
            if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
                print("Something is wrong with your user name or password")
            elif err.errno == errorcode.ER_BAD_DB_ERROR:
                print("Database does not exist")
            else:
                print(err)
        else:
            self.__cnx.close()

    def __del__(self):
        self.__cnx.close()
        print("Closing Database Connection")

    # inserts the Location, InOrOut, Weekday, Datetime and timestamp into the database
    def insert(self, inOrOut):
        try:
            sql = "INSERT INTO WalkerData (Location, InOrOut, WeekDay, DateTime) VALUE (\"Student Recreation Center\", "
            sql += inOrOut + ", dayofweek(now()), now())"
            print("Attempting to Insert into the Database: ")
            print(sql)
            self.__cursor.execute(sql)
            self.__cnx.commit()

        except mysql.connector.Error as err:
            # say it missed it an move on
            print("Something went wrong: {}".format(err))
            return

