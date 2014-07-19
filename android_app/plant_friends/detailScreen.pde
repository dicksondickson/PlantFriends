

// Node details screen
// NodeIdex controls everything

void detailScreen() {
  pushMatrix();
  
  translate(masterX+width+width, masterY);
  rectMode(CORNER);
  fill(palleteNode[NodeIdex][0]);
  noStroke();
  rect(0,0,width,height);

  //background(palleteNode[NodeIdex][0]);
  
  pushMatrix();
  //translate(0,0);
  
  // large icon
  nodeIcon(112,112,0,3,white);
  
  // back icon
  nodeIcon(712,76,-45,0.8,white);

  int textMargin1 = 208;
  
  int textMargin2 = 112;
  
  // Plant name
  textFont(fontreg,68);
  textAlign(LEFT);

  fill(blacktype);
  
  String nName = NodeIndex[NodeIdex][2];
  nName = nName.toUpperCase();
  text(nName, textMargin1-4, 104);
    
  // node name
  textFont(fontmed,26);
  nName = NodeIndex[NodeIdex][1];
  nName = nName.toUpperCase();
  text(nName, textMargin1, 150);
    
  // node id
  nName = "node id " + NodeIndex[NodeIdex][0];
  nName = nName.toUpperCase();
  text(nName, textMargin1, 180);

  
  // location
  textFont(fontmed,18);
  nName = "location";
  nName = nName.toUpperCase();
  text(nName, textMargin1, 234);

  // comments
  nName = "comments";
  nName = nName.toUpperCase();
  text(nName, textMargin1, 312);
    
  // location text
  textFont(fontlit,32);
  nName = NodeIndex[NodeIdex][3];
  text(nName, textMargin1, 270);

  // comments text
  nName = NodeIndex[NodeIdex][4];
  text(nName, textMargin1, 348);
  
  
  popMatrix();
  

  //  data details
  pushMatrix();
  translate(0,400);
  rectMode(CORNER);
  fill(palleteNode[NodeIdex][1]);
  rect(0,0,width,496);

  // date forward < icon
  nodeIcon(60,100,-45,0.6,white);
  
  // date back > icon
  nodeIcon(width-60,100,135,0.6,white);

  textFont(fontmed,18);
  fill(blacktype);
  
  // generate day display cycling
  // make sure dataDay doesn't go out of bounds
  if ( dataDay < 0 ) { dataDay = 0; }
  if ( dataDay > 5 ) { dataDay = 5; }
  
  // showing data for
  if (dataDay == 0) { nName = "right now"; } else { nName = "showing data for"; }

  nName = nName.toUpperCase();
  text(nName, textMargin2, 68);

  // date
  textFont(fontmed,50);
  nName = NodeDetail[NodeIdex][dataDay][0].replace("-"," ");
  text(nName, textMargin2, 120);

  //spacing=74
    
  // soil moist title
  textFont(fontmed,24);
  nName = "soil moisture";
  nName = nName.toUpperCase();
  text(nName, textMargin2, 198);
    
  // humidity title
  nName = "humidity";
  nName = nName.toUpperCase();
  text(nName, textMargin2, 272);

  // warmth title
  nName = "warmth";
  nName = nName.toUpperCase();
  text(nName, textMargin2, 272+74);

  // battery title
  nName = "battery";
  nName = nName.toUpperCase();
  text(nName, textMargin2, 272+74+74);
    
  // data margin
  int dMargin = 390;

  // soil moist data
  textFont(fontlit,46);
  nName = NodeDetail[NodeIdex][dataDay][2];
  nName = nName.toUpperCase();
  text(nName, dMargin, 204);
    
    
    
  // humid data
  nName = NodeDetail[NodeIdex][dataDay][4] + "%";
  text(nName, dMargin, 204+74);

  // toggle temperature C or F
  if (tempToggle) { 
    nName = nf((float(NodeDetail[NodeIdex][dataDay][3])*9/5+32),2,1) + " \u00baF";
  } else { 
    nName = NodeDetail[NodeIdex][dataDay][3] + " \u00baC";
  }
    
  // warmth data
  text(nName, dMargin, 204+74+74);
  
  // battery data
  nName = nf(float(NodeDetail[NodeIdex][dataDay][5]),1,2) + "v";
  text(nName, dMargin, 204+74+74+74);

  // color legend
  int cMargin = 608;

  // soil moist
  fill(greengrass);
  rect(cMargin,180,46,14);
    
  // humid
  fill(bluesky);
  rect(cMargin,180+74,46,14);
   
  // warmth
  fill(orangeredtemp);
  rect(cMargin,180+74+74,46,14);

  // battery
  fill(grey);
  rect(cMargin,180+74+74+74,46,14);
   
  popMatrix();
  

  //  graph
  pushMatrix();
  translate(0,890);
  rectMode(CORNER);
  fill(white);
  noStroke();
  rect(0,0,width,298);
   
  int gMargin = 48;
  int gspace = (width-(gMargin*2))/5;

  // graph type
  textFont(fontmed,18);
  textAlign(CENTER);
  fill(blacktype);
   
  
  // draw dates in as M/dd on graph 
  pushMatrix();
  translate(gMargin,0);
  nName = "No Data";
  String[] dateGx;
  for ( int i = 0 ; i < 6 ; i++ ) {
    if ( NodeDetail[NodeIdex][i][0].equals("No Data") == false) {
      dateGx = split(NodeDetail[NodeIdex][i][0],'-');
      dateGx[0] = dateGx[0].substring(0,3);
      dateGx[1] = dateGx[1].substring(0,2);
      dateGx[1] = dateGx[1].replace("s","");
      dateGx[1] = dateGx[1].replace("t","");
      nName = dateGx[0] + " " + dateGx[1];
        if ( i == 0 ) { nName = "NOW"; }
    } else { nName = "No Data"; }
    text(nName, gspace*i, 44);
  }
  

  // GRAPHS!!

  // draw the graph for each datatype. NodeIndex is global set and changed before entering the details screen. 
  //int datatype, int contLow, int contHigh, color gColor, int dGdot, int dGstroke, int gspace
  
  // Soil Moisture graph. Mapped to 150 - 750 ADC value.
  grapher(2,150,750,greengrass,14,3,gspace);

  // humidity graph. Mapped to 0 - 90%
  grapher(4,0,90,bluesky,12,3,gspace);

  // Temperature graph. Mapped to 0 - 49c.
  grapher(3,0,49,orangeredtemp,10,3,gspace);
  
  // Battery voltage graph. Mapped to 3.3 - 6 volts.
  grapher(5,3.3,6,grey,8,3,gspace);
  
  popMatrix();
  popMatrix();
  popMatrix();
}
