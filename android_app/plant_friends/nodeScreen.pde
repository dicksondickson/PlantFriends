
// node menu screen
// shows all nodes

void nodeScreen() {
  
  //reset dataday
  dataDay = 0;
  
  pushMatrix();
  translate(masterX+width, masterY);
  
  
  rectMode(CORNER);
  fill(greybg);
  noStroke();
  rect(0,0,width,height);
  
  // Header
  noStroke();
  rectMode(CORNERS);

  fill(greengrass);
  rect(0,0,width,headerBoxH);
  image(logohead, 18, 12);
  
  // back icon
  nodeIcon(712,76,-45,0.8,white);
  

  // Node Boxes
  pushMatrix();
  translate (0,headerBoxH+dshadow+2);
  

  // draw the node boxes
  for (int i = 0; i < nodeBoxie.length; i++){
    nodeBoxie[i].display();
  }


  popMatrix();
  popMatrix();
}










