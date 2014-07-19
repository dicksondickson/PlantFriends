
// Mouse clicks and bounding box detection

// function to test if mouse is over bounding box
boolean overBox(int x, int xx, int y, int yy) {
  if (mouseX > x && mouseX < xx && mouseY > y && mouseY < yy) {
    return true;
  } 
  else { 
    return false;
  }
}


// on mouse click / android tap do stuff
// 0:splash, 1:node menu, 2:node details
void mousePressed() {

  // NODE MENU SCREEN
  if (screenSelect == 1 && tempSelect == 0){
    // test bounding boxes from nodescreen. goto details screen for selected node.
    for (int i = 0; i < bBoxNode.length; i++){
      if (mouseX > bBoxNode[i][0] && mouseX < bBoxNode[i][1] && mouseY > bBoxNode[i][2] && mouseY < bBoxNode[i][3]) {
        //println (i);
        NodeIdex = i;
        tempSelect = 1;
      }
    }
    
  // refresh button on node menu screen
  if (mouseX > refreshIDX[0] && mouseX < refreshIDX[1] && mouseY > refreshIDX[2] && mouseY < refreshIDX[3]) {
      // reset parameters for full refresh
      
      ax = 0.1;
      allLoaded = 0;
      tempSelect = 3;
      //screenSelect = 0;
      loadIndexStatus = 0;
      loadDetailStatus = 0;
      sum = 0;
      //masterX = 0;
      
      // regenerate splash image
      //thread("splashGen");
      
      // reload all data from server
      //thread("loadNodeIndex");
      
      // show the splash screen again
      //splashScreen();
      

    }
  }



  // DETAILS SCREEN
  if (screenSelect == 2 && tempSelect == 1){

    // refresh button
    if (mouseX > refreshIDX[0] && mouseX < refreshIDX[1] && mouseY > refreshIDX[2] && mouseY < refreshIDX[3]) {
      // reset parameters
      tempSelect = 2;
    }
      
    // dateForward < button
    dateForwardx = overBox(dateForward[0],dateForward[1],dateForward[2],dateForward[3]); 
    if ( dateForwardx ) {
      dataDay = dataDay-1;
      //println ("forward");
    }

    // dateBack > button
    dateBackx = overBox(dateBack[0],dateBack[1],dateBack[2],dateBack[3]); 
    if ( dateBackx ) {
      dataDay = dataDay+1;
      //println ("back");
    }
   
    // temp toggle c/f
    tempTogglex = overBox(382,748,750-48,750+48);
    if ( tempTogglex ) {
      tempToggle = !tempToggle;
      //println ("temp toggle"); // debug
    }
  }
}
