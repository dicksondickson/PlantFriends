/*

Plant Friends Gateway

Moteino communicates with the Pi via UART
*NOTE* THIS VERSION ONLY WORKS WITH MOTEINOS WITH RFM69(W, HW) radios!!
*

      RPI		Moteino
PIN2	5v	  VIN
PIN6	GND	  GND
PIN8	TX	  RX
PIN10	RX	  TX


Dickson Chow
http://dicksonchow.com

First Release: July 1, 2013.
Updated: Nov 15th 2015.


MIT License
http://opensource.org/licenses/mit-license.php


*/


#include <LowPower.h> // low power library. Get Felix's version: https://github.com/LowPowerLab/LowPower
#include <RFM69.h> // RFM69 library. Get it here: https://www.github.com/lowpowerlab/rfm69
#include <SPI.h>
#include <avr/sleep.h> // sleep library
#include <stdlib.h> // library for maths


#define NODEID  1  // Node ID used for this unit. 1 is reserved for gateway
#define NETWORKID  20  //the network ID we are on
#define FREQUENCY     RF69_915MHZ
RFM69 radio;


#define SERIAL_BAUD 115200 // define serial port speed


int LedPin = 5;


void setup() {
  
  // open serial port
  Serial.begin(SERIAL_BAUD);
  
  // LED
  pinMode(LedPin, OUTPUT);
  
  LEDBlink(80);
  LEDBlink(80);
  
  // Initialize the radio
  radio.initialize(FREQUENCY,NODEID,NETWORKID);
  #ifdef IS_RFM69HW
    radio.setHighPower(); //uncomment only for RFM69HW!
  #endif
}


void loop() {
  
  int datalen;
  char charbuf;
  
  LEDPulse();
  
  if (radio.receiveDone()) // radio finishes recieving data
  {
      // get length
      for (byte i = 0; i < radio.DATALEN; i++)
      
      // dumps data to the serial port
      Serial.print((char)radio.DATA[i]);
      Serial.println();

      // sends ack to sensor node
      if (radio.ACKRequested())
      {
        radio.sendACK();
        //Serial.print(" - ACK sent");
      }
      
      // blink led
      LEDBlink(30);
      LEDBlink(30);   
  }
}



// LED Pulse function
void LEDPulse() {
  int i;
  delay (12);
  for (int i = 18; i < 128; i++) { // loop from 0 to 254 (fade in)
    analogWrite(LedPin, i);      // set the LED brightness
    delay(12);
  }

  for (int i = 128; i > 18; i--) { // loop from 255 to 1 (fade out)
    analogWrite(LedPin, i); // set the LED brightness
    delay(12);       
  }
  //digitalWrite(LedPin, LOW);
  //delay (128);
}


// LED Blink function
void LEDBlink(int DELAY_MS)
{
  digitalWrite(LedPin,HIGH);
  delay(DELAY_MS);
  digitalWrite(LedPin,LOW);
  delay(DELAY_MS);
}

