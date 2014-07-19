

// Loads the NodeIndex and NodeDetails
// connects to raspi to grab all the sensor node data and dumps into arrays

// Load node index
void loadNodeIndex() {
  String[] tempData1;
  String[] tempData2;

  tempData1 = loadStrings(NodeIndexURL);
  
  NodeIndex = new String[tempData1.length][0];

  // split the data into multi dim array
  for (int x = 0 ; x < tempData1.length ; x++ ) {
    tempData2 = split(tempData1[x],':');
      for (int xi = 0 ; xi < tempData2.length ; xi++ ) {
        NodeIndex[x]= append(NodeIndex[x],tempData2[xi]);
      }
  }

  // generate random color for node icons on menu and details screen. currently not used
  for (int xix = 0 ; xix < NodeIndex.length ; xix++ ) {
    NodeIndex[xix]= append(NodeIndex[xix],str(random(0,palleteNode.length)));
  }
  
  // generate random rotation for node icons on node menu screen and details screen. appends to Nodeindex  
  for (int xix = 0 ; xix < NodeIndex.length ; xix++ ) {
    NodeIndex[xix]= append(NodeIndex[xix],str(random(0,rSx.length)));
  }

  //printArray(NodeIndex[1]); // debug
  //println("load index done"); // debug
  
  loadIndexStatus = 1; // done loading
  
  // Load node details
  int xDays = 6; // number of days of data. first dataset is right now.
  String[][] NodeDetailY;
  NodeDetail = new String[NodeIndex.length][xDays][0]; // initialize array. append datatypes
  
  for (int x = 0 ; x < NodeIndex.length ; x++ ) {
    
    NodeID = NodeIndex[x][0];
    NodeDetailY = loadNodeDetail(NodeID);

    for (int xi = 0 ; xi < NodeDetailY.length ; xi++ ) {
      for (int xe = 0 ; xe < NodeDetailY[xi].length ; xe++ ) {
        NodeDetail[x][xi] = append(NodeDetail[x][xi],NodeDetailY[xi][xe]);
        //println (NodeDetail[x][xi]);
      }
    }
  }
  
  // Generates array of objects for each node. display on node menu screen
  //NodeIndex[yy][5] // for color
  int offset = 0;
  int index = 0;
  
  // bounding box init
  bBoxNode = new int[NodeIndex.length][4];
  
  // get node index stuff
  nodeBoxie = new NodeBox[NodeIndex.length];
  
  for (int yy = 0; yy < nodeBoxie.length; yy++) {
    offset = (nodeBoxH+dshadow+pad) * yy; // offsets each node. for spacing
    
    //NodeB(int tempposx, int tempposy, color nodeColor, String nodeRstemp, String tempNodeID, String tempNodeName, String tempPlant, String tempSoil, String tempTemp, String tempHumid, String tempVolt)
    nodeBoxie[yy] = new NodeBox(0,offset,palleteNode[yy][0],NodeIndex[yy][6],NodeIndex[yy][0],NodeIndex[yy][1],NodeIndex[yy][2],NodeDetail[yy][0][2],NodeDetail[yy][0][3],NodeDetail[yy][0][4],NodeDetail[yy][0][5]);
   
    //generate bounding box
    bBoxNode[yy][0] = 0;
    bBoxNode[yy][1] = width;
    bBoxNode[yy][2] = headerBoxH+dshadow+2+offset;
    bBoxNode[yy][3] = headerBoxH+dshadow+2+nodeBoxH+dshadow+pad+offset;

  }
  
  //println("load details done"); // debug
  loadDetailStatus = 1; // done loading
}





// Load Node Details function called from loadNodeIndex
String[][] loadNodeDetail(String NodeID) {
  String[] tempData1;
  String[] tempData2;

  //tempData1 = loadStrings(NodeIndexURL);
  tempData1 = loadStrings(NodeURL+NodeID);
  
  //printArray(tempData1);
  
  String[][] NodeDetailx = new String[tempData1.length][0];

  // split the data into multi dim array
  for (int x = 0 ; x < tempData1.length ; x++ ) {
    tempData2 = split(tempData1[x],':');
      for (int xi = 0 ; xi < tempData2.length ; xi++ ) {
        NodeDetailx[x]= append(NodeDetailx[x],tempData2[xi]);

      }
  }
 // printArray(NodeDetailx[5]);
  //loadDetailStatus = 1; // done loading
  return(NodeDetailx);
}

