import time
import unittest

from PyMata.pymata import PyMata

board = PyMata("/dev/ttyACM0", verbose=False)
pin1 = 12
pin2 = 13
board.sonar_config(pin1, pin1)
board.sonar_config(pin2, pin2)
time.sleep(2)

# Checks to see that both sensors are connected properly and are reading data
class ArduinoUT(unittest.TestCase):
    def test_sensor1(self):
        data = board.get_sonar_data()
        self.assertTrue(data[pin1][1] > 0)

    def test_sensor2(self):
        data = board.get_sonar_data()
        self.assertTrue(data[pin2][1] > 0)

# That's all folks!


if __name__ == '__main__':
    unittest.main()
