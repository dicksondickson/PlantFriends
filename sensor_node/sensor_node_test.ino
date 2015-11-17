/*


Plant Friends Sensor Node

Sensor Node communicates with Moteino gateway.
Node contains soil moisture probe, temperature/humidity sensor and battery meter.

NOTE: There is a lot of stuff going on so make you go through the initial variable declarations! 


By Dickson Chow
http://dicksonchow.com


First Release: July 28th, 2013.
Updated: July 7th, 2014.

MIT License
http://opensource.org/licenses/mit-license.php


------------------------------

Soil Moisture Chart

 0 : in air @ 24c
 120 : skin
 0 - 250 : dry
 300 - 600 : moist 
 600 - 700 : soaked
 700 > : in water @ 24c
 1021 : direct short
 
 Needs further calibration
 
------------------------------
 
 Sensor Node Data Format
 
 NODEID:ErrorLvl:SoilMoist:TempC:Humid:Voltage
 
------------------------------

*/


// Include libraries
#include <DHT.h> // DHT sensor library
#include <LowPower.h> // low power library
#include <RFM12B.h> // RFM12B radio library
#include <avr/sleep.h> // sleep library
#include <stdlib.h> // library for maths


// Soil moisture sensor define
#define moistPIN1 A2 // soil probe pin 1 with 56kohm resistor
#define moistPIN2 7 // soil probe pin 2 with 100ohm resistor
#define moistREADPIN1 A0 // analog read pin. connected to A2 PIN with 56kohm resistor


// DHT Humidity + Temperature sensor define
#define DHTPIN 5 // Data pin (D5) for DHT
#define DHTPWR 4 // turn DHT on and off via transistor
#define DHTTYPE DHT11 // sensor model DHT11
DHT dht(DHTPIN, DHTTYPE); // define DHT11


// Battery meter 
#define VoltagePinRead A7 // analogue voltage read pin for batery meter
#define VoltagePinEnable A3 // current sink pin. ( enable voltage divider )
#define VoltageRef 3.3 // reference voltage on system. use to calculate voltage from ADC
#define VoltageDivider 2 // if you have a voltage divider to read voltages, enter the multiplier here.
int VoltageLow = 4; // low battery threshhold. 4 volts.
int VoltageADC;


// LED Pin
#define led 6


// RADIO SETTINGS
// You will need to initialize the radio by telling it what ID it has and what network it's on
// The NodeID takes values from 1-127, 0 is reserved for sending broadcast messages (send to all nodes)
// The Network ID takes values from 0-255
#define NODEID        23  // The ID of this node. Has to be unique. 1 is reserved for the gateway!
#define NETWORKID    20  //the network ID we are on
#define GATEWAYID     1  //the gateway Moteino ID (default is 1)
#define ACK_TIME     2800  // # of ms to wait for an ack


// Power Management Sleep cycles
int sleepCycledefault = 1; // Sleep cycle 450*8 seconds = 1 hour. DEFAULT 450
int soilMoistThresh = 250; // soil moisture threshold. reference chart


String senseDATA; // sensor data STRING
String ErrorLvl = "0"; // Error level. 0 = normal. 1 = soil moisture, 2 = Temperature , 3 = Humidity, 4 = Battery voltage


// Need an instance of the Radio Module
RFM12B radio;
bool requestACK=true;


void setup()
{
  
  Serial.begin(9600);
 
  //LED setup. 
  pinMode(led, OUTPUT);
  
  // Battery Meter setup
  pinMode(VoltagePinRead, INPUT);
  pinMode(VoltagePinEnable, INPUT);
 
  // Moisture sensor pin setup
  pinMode(moistPIN1, OUTPUT);
  pinMode(moistPIN2, OUTPUT);
  pinMode(moistREADPIN1, INPUT);
  
  // Humidity sensor setup
  pinMode(DHTPWR, OUTPUT);
  dht.begin();

  // power on indicator
  LEDBlink(80);
  LEDBlink(80);
   
  // Initialize the radio
  radio.Initialize(NODEID, RF12_915MHZ, NETWORKID);
  radio.Sleep(); //sleep right away to save power
  
}



void loop()
{
  
  int sleepCYCLE = sleepCycledefault; // Sleep cycle reset
  ErrorLvl = "0"; // Reset error level
  
  
  // Battery level check
  pinMode(VoltagePinEnable, OUTPUT); // change pin mode
  digitalWrite(VoltagePinEnable, LOW); // turn on the battery meter (sink current)
  for ( int i = 0 ; i < 3 ; i++ ) {
    delay(48); // delay, wait for circuit to stabalize
    VoltageADC = analogRead(VoltagePinRead); // read the voltage 3 times. keep last reading
  }
  float Voltage = ((VoltageADC * VoltageRef) / 1023) * VoltageDivider; // calculate the voltage
  if (Voltage < VoltageLow){
    ErrorLvl = "4"; // assign error level
  }
  pinMode(VoltagePinEnable, INPUT); // turn off the battery meter
  Serial.print("Battery Level: ");
  Serial.println(Voltage);
  
  
  // Soil Moisture sensor reading
  int moistREADavg = 0; // reset the moisture level before reading
  int moistCycle = 3; // how many times to read the moisture level. default is 3 times
  for ( int moistReadCount = 0; moistReadCount < moistCycle; moistReadCount++ ) {
    moistREADavg += moistREAD();
  }
  moistREADavg = moistREADavg / moistCycle; // average the results
  Serial.print("Soil Moisture: ");
  Serial.println(moistREADavg);
  
  
  // if soil is below threshold, error level 1
  if ( moistREADavg < soilMoistThresh ) {
    ErrorLvl += "1"; // assign error level
      LEDBlink(128);
      LEDBlink(128);
      LEDBlink(128);
  }
    
    
    
  // Humidity + Temperature sensor reading
  // Reading temperature or humidity takes about 250 milliseconds!
  // Sensor readings may also be up to 2 seconds 'old' (its a very slow sensor)
  digitalWrite(DHTPWR, HIGH); // turn on sensor
  delay (38); // wait for sensor to stabalize
  int dhttempc = dht.readTemperature(); // read temperature as celsius
  int dhthumid = dht.readHumidity(); // read humidity
  //Serial.println(dhttempc);
  
  // check if returns are valid, if they are NaN (not a number) then something went wrong!
  if (isnan(dhttempc) || isnan(dhthumid) || dhttempc == 0 || dhthumid == 0 ) {
    dhttempc = 0;
    dhthumid = 0;
    ErrorLvl += "23";
  }
  delay (18);
  digitalWrite(DHTPWR, LOW); // turn off sensor
  Serial.print("Temperature in C: ");
  Serial.println(dhttempc);
  Serial.print("Humidity in percent: ");
  Serial.println(dhthumid);


  // PREPARE READINGS FOR TRANSMISSION
  senseDATA = String(NODEID);
  senseDATA += ":";
  senseDATA += ErrorLvl;
  senseDATA += ":";
  senseDATA += String(moistREADavg);
  senseDATA += ":";
  senseDATA += String(dhttempc);
  senseDATA += ":";
  senseDATA += String(dhthumid);
  senseDATA += ":";
  char VoltagebufTemp[10];
  dtostrf(Voltage,5,3,VoltagebufTemp); // convert float Voltage to string
  senseDATA += VoltagebufTemp;
  byte sendSize = senseDATA.length();
  sendSize = sendSize + 1;
  char sendBuf[sendSize];
  senseDATA.toCharArray(sendBuf, sendSize); // convert string to char array for transmission

  
    
  //Transmit the data
  LEDPulse (); // pulse the LED
  radio.Wakeup(); // wakeup the radio
  radio.Send(GATEWAYID, sendBuf, sendSize, requestACK); // send the data
  if (requestACK)
  {
    //wait for ack
    if (waitForAck()) {
      //ack recieved
    } else {
        //ack not recieved
       // sleepCYCLE = sleepCYCLE / 2; // since we didnt recieve ack, halve sleep cycle
      }
  }
  radio.Sleep(); // sleep the radio to save power
  
  
  

  // Error Level handing
  // If any error level is generated, halve the sleep cycle
  //if ( ErrorLvl.toInt() > 0 ) {
  //  sleepCYCLE = sleepCYCLE / 2;
  //  LEDBlink(30);
  //  LEDBlink(30);
  //  LEDBlink(30);
 // }
  
  
  
  
  // Randomize sleep cycle a little to prevent collisions with other nodes
  //sleepCYCLE = sleepCYCLE + random(8);



  // POWER MANAGEMENT DEEP SLEEP
  // after everything is done, go into deep sleep to save power
  for ( int sleepTIME = 0; sleepTIME < sleepCYCLE; sleepTIME++ ) {
    LowPower.powerDown(SLEEP_1S, ADC_OFF, BOD_OFF); //sleep duration is 8 seconds multiply by the sleep cycle variable.
  }

}



// Radio ACK recieve/send function
// wait a few milliseconds for proper ACK, return true if received
static bool waitForAck() {
  long now = millis();
  while (millis() - now <= ACK_TIME)
    if (radio.ACKReceived(GATEWAYID))
      return true;
  return false;
}



// LED Blink function
void LEDBlink(int DELAY_MS)
{
  digitalWrite(led,HIGH);
  delay(DELAY_MS);
  digitalWrite(led,LOW);
  delay(DELAY_MS);
}



// LED Pulse function
void LEDPulse() {
  int i;
  delay (88);
  for (int i = 0; i < 88; i++) { // loop from 0 to 254 (fade in)
    analogWrite(led, i);      // set the LED brightness
    delay(12);
  }

  for (int i = 88; i > 0; i--) { // loop from 255 to 1 (fade out)
    analogWrite(led, i); // set the LED brightness
    delay(12);       
  }
  digitalWrite(led, LOW);
  delay (128);
}



// Moisture sensor reading function
// function reads 3 times and averages the data
int moistREAD() {
  int moistREADdelay = 88; // delay to reduce capacitive effects
  int moistAVG = 0;
  // polarity 1 read
  digitalWrite(moistPIN1, HIGH);
  digitalWrite(moistPIN2, LOW);
  delay (moistREADdelay);
  int moistVal1 = analogRead(moistREADPIN1);
  //Serial.println(moistVal1);
  digitalWrite(moistPIN1, LOW);
  delay (moistREADdelay);
  // polarity 2 read
  digitalWrite(moistPIN1, LOW);
  digitalWrite(moistPIN2, HIGH);
  delay (moistREADdelay);
  int moistVal2 = analogRead(moistREADPIN1);
  //Make sure all the pins are off to save power
  digitalWrite(moistPIN2, LOW);
  digitalWrite(moistPIN1, LOW);
  moistVal1 = 1023 - moistVal1; // invert the reading
  //Serial.println(moistVal2);
  moistAVG = (moistVal1 + moistVal2) / 2; // average readings. report the levels
  return moistAVG;
}




