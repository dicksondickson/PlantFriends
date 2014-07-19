
// splash screen
float ax = 255;
void splashScreen() {
  
pushMatrix();
  translate(masterX, masterY);
  
  background(white);
  
   
  
  
  
  // logotype fade in * does not work properly on android
  //tint(255,lFade);
  image(logosplash, 40, 400);
  //lFade = lFade + 8;
  
  xk = nodeLib.length - 0.001;
  sum = 0;
  // Draw splash screen
  for ( int is = 0; is < nodeLib.length; is++) {
    float dx = 1 - nodeS[is];
    if ( nodeS[is] >= 1.08 ) { nodeS[is] = 1.01; }
      nodeIcon(nodeLib[is][0],nodeLib[is][1],nodeRs[is],nodeS[is],nodeC[is]);
      //nodeRs[i] = nodeRs[i] + 0.8;
      nodeT[is] = nodeT[is] + 0.08;
      //if ( nodeT[i] >= -0.1 ) { nodeS[i] = nodeS[i] * 1.16 ; }
      if ( nodeT[is] >= -0.1 ) { nodeS[is] += dx * easing ; }
  }

  for (int i=0; i < nodeLib.length; i++) {
   sum += nodeS[i];
  }
  

 ax = ax/1.08;
  if(ax > 1){
    fill(white,ax);
    rect(0,0,width,height);
    //println(ax);
  }

  //println (nodeS[0]);
  //println (sum);
  popMatrix();
  
  

}
