Plant Friends MK II
==================

What is Plant Friends?

Plant Friends is a plant environmental monitor system. It monitors the soil moisture, air temperature, and air humidity of your indoor plant(s) and will alert you via email and SMS when your plants are thirsty. The system is battery operated, wireless, Arduino and Raspberry Pi based and comes with an Android app. The app enables you to look at the real-time and historical data (temperature, humidity, soil moisture) on your phone.

The (many) sensor nodes consists of a Moteino (an Arduino clone with an RF transceiver), a soil moisture sensor, a humidity sensor, temperature sensor and a battery meter. Once the sensor node collects the sensor readings, it transmits the data via the transceiver over the 915mhz ISM band to the base station.

The base station houses another Moteino, which acts as a gateway to recieve the RF signals, and a Raspberry Pi where the data is logged into a MySQL database and serves the data to the Plant Friends mobile app. The Plant Friends app is a native Android app that displays the senor node data in a pretty way. :)

I wrote an extensive how-to tutorial instructable where you can build your own Plant Friends system!
http://dicksonchow.com/plantfriends2


Plant Friends is based on my original proof-of-concept project:
http://dicksonchow.com/plantfriends


If you use Plant Friends, I'd love to hear about it!



NOTES
==================
 The app looks nice but missing some functionality like scrolling through the list of nodes (lol) and the ability to edit sensor node information. I have other plans for the app like adding sensor nodes to the system and controlling ( an army?) of Moteinos so i'll be working on that. :)

The main python script that runs on the Pi and the sensor node / gateway Moteino code all works great. 





Large Fine Print
==================

The Plant Friends name, logo, UI and all associated graphic design is Copyright © 2014 Dickson Chow.

The Plant Friends MKII Sensor Node and Base Station enclosure design is released under the Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0) License.

The Plant Friends MKII software, source code, Arduino sketchs and Processing sketches is released under The MIT License.
