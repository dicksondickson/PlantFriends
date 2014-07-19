
// Draw node icon
// x,y,rotate,scale,color
void nodeIcon(float posx, float posy, float srotate, float sscale, color scolor){
  pushMatrix();
  translate(posx,posy);
  rotate(radians(srotate));
  scale(sscale);
  pushMatrix();
  translate(-23,-23);
  noStroke();
  fill(scolor);
  beginShape();
  vertex(0,0);
  vertex(46,0);
  vertex(46,23);
  vertex(23,23);
  vertex(23,46);
  vertex(0,46);
  endShape(CLOSE);
  popMatrix();
  popMatrix();
}



// generate purdy splash image
void splashGen () {
  // Generate node icons for splash image
  nodeRs = new float [nodeLib.length];
  nodeC = new int [nodeLib.length];
  nodeT = new float [nodeLib.length];
  nodeS = new float [nodeLib.length];
  for (int i = 0; i < nodeLib.length; i++) {
     nodeLib[i][0] = nodeLib[i][0] + random(-18,18);
     nodeLib[i][1] = nodeLib[i][1] + random(-18,18);
     nodeRs[i] = rS[int(random(rS.length))];
     nodeC[i] = pallete[int(random(pallete.length))];
     nodeT[i] = random(-6,-1);
     nodeS[i] = 0;
  }
}





// draws graph points and lines in node details screen
void grapher ( int dataTypeX, float contLow, float contHigh, color gColor, int dGdot, int dGstroke, int gspace) {

  float dG;
  float dGx;
  float dGx2;
  int iG = 0;
  float cont1 = 0;
  float cont2 = 204;
  
  pushMatrix();
  translate(0,66);
  // points
  noStroke();
  fill(gColor);
  //dGdot = 12;
  ellipseMode(CENTER);
  for ( iG = 0; iG < NodeDetail[NodeIdex].length ; iG++ ) {
    dG = constrain(map(float(NodeDetail[NodeIdex][iG][dataTypeX]),contHigh,contLow,cont1,cont2),cont1,cont2);
    ellipse(gspace*iG, dG, dGdot, dGdot);
  }
  
  //lines
  iG = 0; // reset
  noFill();
  stroke(gColor);
  strokeWeight(dGstroke);
  for ( iG = 0; iG < NodeDetail[NodeIdex].length ; iG++ ) {
    dG = constrain(map(float(NodeDetail[NodeIdex][iG][dataTypeX]),contHigh,contLow,cont1,cont2),cont1,cont2);
    if ( iG < NodeDetail[NodeIdex].length - 1 ) {
      dGx = constrain(map(float(NodeDetail[NodeIdex][iG+1][dataTypeX]),contHigh,contLow,cont1,cont2),cont1,cont2);
      dGx2 = (gspace*iG)+gspace;
    } else { 
      dGx = dG; 
      dGx2 = gspace*iG;
    }
    line(gspace*iG, dG,dGx2,dGx);
  }
  
  

  
  
  popMatrix();
}
