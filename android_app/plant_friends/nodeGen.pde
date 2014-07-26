
// Draws node menu boxes
// this is a class. called from nodeScreen
class NodeBox {
  
  int posx;
  int posy;
  color nodeColorx;
  int nodeRstempx;
  int tempNodeIDx;
  String tempNodeNamex;
  String tempPlantx;
  float tempSoilx;
  float tempHumidx;
  float tempTempx;
  float tempVoltx;

  NodeBox (int tempposx, int tempposy, color nodeColor, String nodeRstemp, String tempNodeID, String tempNodeName, String tempPlant, String tempSoil, String tempTemp, String tempHumid, String tempVolt){
    
    posx = tempposx;
    posy = tempposy;
    tempPlantx = tempPlant;
    tempNodeIDx = int(tempNodeID);
    tempNodeNamex = tempNodeName;
    tempSoilx = float(tempSoil);
    tempHumidx = float(tempHumid);
    tempTempx = float(tempTemp);
    tempVoltx = float(tempVolt);
    nodeColorx = color(nodeColor);
    nodeRstempx = int(nodeRstemp);
    //nodeColorx = int(nodeColor);

  }

  void display() {
    

    // node box
    pushMatrix();

    translate(posx, posy + pad);

    //pushMatrix();
    noStroke();
    rectMode(CORNERS);
    
    //drop shadow
    fill(shadow);
    rect(margin,nodeBoxH,width-margin,nodeBoxH+dshadow);

    //the actual box
    fill(255);
    rect(margin,0,width-margin,nodeBoxH);

    // Node Icon. x,y,rotate,scale,color
    nodeIcon(margin+18+46,(nodeBoxH/2),rSx[nodeRstempx],2,nodeColorx);

    //Draw type
    int textMargin = 148;
    
    textAlign(LEFT);
    fill(blacktype);

    // Node Name
    textFont(fontmed,32);

    tempNodeNamex = tempNodeNamex.toUpperCase();
    text(tempNodeNamex, textMargin, (nodeBoxH-24));

    // Plant Name
    textFont(fontlit,46);
    tempPlantx = tempPlantx.toUpperCase();
    text(tempPlantx, textMargin-2, 54);

    miniGraph(); // draws mini grap via function
    
    // arrow
    // x,y,rotate,scale,color
    nodeIcon(714,nodeBoxH/2,135,0.6,greylit);
    
    popMatrix();
  }
  
  void miniGraph(){
    //Draw quick graphs
    pushMatrix();
    translate(580,16);
    noStroke();
    rectMode(CORNERS);
    
    int mGw = 22;
    int mPad = 4;
    float vOffset = 0;

    // soil moist. scale is mapped to 0 - 600 ADC value
    fill(greenpas);
    rect(0,0,mGw,90);
    vOffset = constrain(map(tempSoilx,0,600,90,0),0,90);
    fill(green);
    rect(0,vOffset,mGw,90);
    
    // humid. scale mapped to 0 - 90%
    fill(blueskypas);
    rect(mGw+mPad,0,mGw*2+mPad,90);
    vOffset = constrain(map(tempHumidx,0,90,90,0),0,90);
    fill(bluesky);
    rect(mGw+mPad,vOffset,mGw*2+mPad,90);
    
    // temp. cale mapped to 0 - 46c
    fill(orangeredpas);
    rect(mGw*2+mPad*2,0,mGw*3+mPad*2,90);
    vOffset = constrain(map(tempTempx,0,46,90,0),0,90);
    fill(orangeredtemp);
    rect(mGw*2+mPad*2,vOffset,mGw*3+mPad*2,90);
    
    
    //batt. mapped to 3 - 6v
    fill(greylit);
    rect(mGw*3+mPad*3,0,mGw*4+mPad*3,90);
    vOffset = constrain(map(tempVoltx,3.3,6,90,0),0,90);
    fill(grey);
    rect(mGw*3+mPad*3,vOffset,mGw*4+mPad*3,90);

    popMatrix();
  }


}





















