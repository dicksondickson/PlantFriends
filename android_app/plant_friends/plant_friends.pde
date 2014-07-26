/*

  Plant Friends Android App
  
  Gets data from Plant Friends base station and displays it.
  
  DATA FORMAT:
  0 DATE : 1 ERROR LEVEL : 2 SOIL MOISTURE : 3 TEMPERATURE in C : 4 HUMIDITY : 5 BATTERY VOLTAGE

  Dickson Chow
  http://dicksonchow.com

  First release: August 8, 2013
  Updated: July 26, 2014


  The Plant Friends MKII software, source code, Arduino sketchs and Processing sketches is released under The MIT License.
  http://opensource.org/licenses/mit-license.php

  The Plant Friends name, logo, UI and all associated graphic design is Copyright © 2014 Dickson Chow.
  
  
  
  ToDo:
  implement gestures to go between screens
  scroll through list of nodes.
  do something with errorlvl data.
  allow server settings to be entered in app.
  customizable colors.
  
  --------------------------------------------------
  
  Soil Moisture Sensor Chart:

  0 : in air @ 24c
  120 : skin
  120 - 250 : dry soil
  300 - 600 : moist soil
  600 - 700 : soaked soil
  700 > : in water @ 24c
  1021 : direct short

*/

 
// Server settings. Change IP address or hostname accordingly.
static final String NodeIndexURL = "http://RASPIADDRESSS:PORT";

// for accessing node specific data
static final String NodeURL = NodeIndexURL + "/index.php?NodeID=";

// colours
color leaf = #099500; // logotype leaf
color shadow = #eaeaea; // drop shadow
//color shadow = #d5d5d5; // drop shadow
color white = #fff9f0; //bg
color whitebox = #fffbf6; 
color whitetype = #fbf9f1; // white type
color greylit = #e9e9e9; // for graph bg
color greybg = #e8e8e8; // background
color greypas = #dad7d3; // batt and arrow
color grey = #a1a1a1; // batt
color blacktype = #555554; //black type
color blue = #2989cd; //icon
color blueskypas = #bff1f2; // humid
color bluesky = #18d6e7; // humid
color bluebaby = #63d1fd; // icon
color teal = #29b8a8; // icon
color tealpas = #7ce6da; // icon
color green = #30a526; //icon
color greenpas = #c6e7c3; // soilmoist
color greengrass = #8fb95e; // header + soilmoist
color greenlit = #d0fe4a; // icon
color yellow = #f2de00; //icon
color yellowlit = #fff19a; //icon
color brown = #dbb8a8; //icon
color orangelit = #ffb767; //icon
color orange = #ff9936; //icon
color orangered = #ff452c; //icon
color orangeredpas = #ffcebb; // temp
color orangeredtemp = #ff5b22; // temp
color red = #c92b2b; //icon
color pinkhothot = #ff0387; //icon
color pinkhot = #ff2a98; //icon
color pinkmid = #ff93cb; //details screen? test with brightness
color pink = #ffbcd6; //icon pink proper
color pinkpurple = #ff65d5; //icon maybe for splash only
color purple = #b770ff; //icon


// colour pallete for assigning random colours to splash
color[] pallete = {blue,green,blueskypas,bluebaby,teal,green,greengrass,greenpas,greenlit,yellow,
yellowlit,brown,orangelit,orange,orangered,red,pinkhothot,pinkhot,pink,pinkmid,pinkpurple,purple};


// colour pallete for node details and node menu icons
color[][] palleteNode = {
{pinkhot,pink},
{teal,tealpas},
{orange,orangelit},
{bluebaby,blueskypas},
{greengrass,greenpas},
{pinkmid,pink},
{yellow,yellowlit},
{purple,purple},
};



// fonts
//static PFont fontbold;
static PFont fontmed;
static PFont fontreg;
static PFont fontlit;


// shapes and images
PShape nodeshape; // draw node icon
PImage logosplash; // logotype for splash
PImage logohead; // logotype for header


// coordinates of node icons for splash screen
// x,y,rotate,scale,color
float[][] nodeLib = {
  {62,58},{270,116},{432,56},{574,128},{721,76},
  {72,329},{150,208},{282,307},{400,232},{544,281},{673,226},{729,349},
  {532,418},{663,502},{721,617},
  {55,717},{240,716},{448,759},{612,708},
  {37,923},{186,843},{351,876},{544,887},{697,834},
  {175,1004},{387,1004},{583,1011},{694,983},
  {79,1116},{294,1140},{490,1104},{703,1122},
};


// splash screen variables to generate some randomness
float[] rS = {0,45,90,135,180,225,270,315}; // rotation points
float[] rSx = {0,90,180,270}; // rotation points for node icons in node menu
int[] rSi;
color[] nodeC; // colorer
float[] nodeRs; // rotater
float[] nodeT; // timer
float[] nodeS; // scaler


// Screen / menu control
// 0:splash, 1:node menu, 2:node details
int screenSelect = 0; 
int tempSelect = 0;


// Animation
float easing = 0.14; // splash screen icon easing
float lFade = -80; // logotype fader

// master transform cords for menu animations
float masterX = 0;
float masterY = 0;
float measing = 0.17; // menu easing
float targetX = -768;
float targetX2 = -1538;
float travelX = 0;


// details screen vars
int[]dateForward = {60-58,60+58,500-58,500+58}; // date forward bounding box <
int[]dateBack = {768-60-58,768-60+58,500-58,500+58}; // date back bounding box >
int dataDay = 0; // temp var for day select
boolean tempToggle = false; // toggle temperature c/f
boolean dateForwardx;
boolean dateBackx;
boolean tempTogglex;
int[][] bBoxDetail; // bounding boxes


// back button on node menu and node details
float[] refreshIDX = {712-58,712+58,76-58,76+58};


// Node menu vars
NodeBox[] nodeBoxie;
int margin = 12;
int dshadow = 3;
int pad = 6;
int headerBoxH = 248;
int nodeBoxH = 122;
int[][] bBoxNode; // bounding box array.  nodeid => x, x, y, y


// global vars for storing data of all nodes
String[][] NodeIndex; // data format INDEX => NODEID : NODE ALIAS : PLANT NAME : LOCATION : COMMENTS :  NODE ICON ROTATION : NODE ICON COLOR(not used right now)
String[][][] NodeDetail; // data format INDEX => 0 DATE : 1 ERROR LEVEL : 2 SOIL MOISTURE : 3 TEMPERATURE in C : 4 HUMIDITY : 5 BATTERY VOLTAGE
String NodeID; // the id number of the actual node. relate this to NodeIdex to access all the data in arrays
int NodeIdex = 0; // see above
int loadIndexStatus = 0;
int loadDetailStatus = 0;
int allLoaded = 0;
float xk;
float sum; // count to make sure splah image is done animating itself






void setup() {
  
  // nexus 4 screen size sans bottom menu bar
  size(768, 1184);
  
  // the hamburger way
  orientation(PORTRAIT);  
  
  // doesn't do anything on android?
  smooth(); 
  
  // Load images
  logosplash = loadImage("logotype_bow.png");
  logohead = requestImage("logotype_wog.png");

  // Generate node icons for splash image. spawns thread
  thread("splashGen");
  
  // Loads the NodeIndex. Spawns thread.
  thread("loadNodeIndex");

  // Alternate font set. from google, safe to distribute
  fontmed = createFont("Roboto-Medium.ttf", 50, true);
  fontreg = createFont("Roboto-Regular.ttf", 72, true);
  fontlit = createFont("Roboto-Light.ttf", 48, true);


}




void draw() {
  // Screen selection
  switch (screenSelect) {
    case 0:
      //display splash screen
      splashScreen();
      allLoaded = loadIndexStatus+loadDetailStatus; // make sure everything is loaded ie. data from server
      // animate into node menu screen
      if (allLoaded == 2 && sum > xk) { 
        nodeScreen();
        travelX = targetX - masterX;
        masterX += travelX * measing;
        if ( masterX <= -767 ) { screenSelect = 1; } 
      } else { screenSelect = 0; }
    break;
    case 1: 
      // display nodes. main menu
      nodeScreen();
      // make sure to not switch modes too prematurely cause shit breaks. boo.
      // animate into details screen
      if (tempSelect == 1 ) {
        detailScreen();
        travelX = targetX2 - masterX;
        masterX += travelX * measing;
        if ( masterX <= -1535) { screenSelect = 2; }
      } 
      if (tempSelect == 3 ) {
        ax = ax*1.3;
        if (ax <= 265) {
          fill(white,ax);
          rect(0,0,width,height);
          //println(ax);
        }
        if (ax > 255) {
          //reset everything
          screenSelect = 0;
          tempSelect = 0;
          masterX = 0;
          splashScreen();
          ax = 255;
          thread("splashGen");
          thread("loadNodeIndex");
        }
      }
    break;
    case 2: 
      // display details screen
      detailScreen();
      // animate back to node menu screen
      if (tempSelect == 2 ) {
        nodeScreen();
        travelX = targetX - masterX;
        masterX += travelX * measing;
        if ( masterX >= -769 ) { screenSelect = 1; tempSelect=0; }
      }
    break;
    default: 
      screenSelect = 0;
    break;
  }
}





















