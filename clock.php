<?php


echo getClock();


function getClock(){
    $html = '<canvas width="1000" height="500" id="clock"></canvas>';

    return $html;
}

?>
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script>
 var halfSegmentWidth = 4;
 var segmentColor = '#00ff00';

 var segmentWidth = 0.04;
 var segmentSpacing = 0.1;
 var segmentLength = 0.35;
 
 var segmentLocations = [
     {x: 0, y: 0, standing: false},//0 top
     {x: 0, y: 0, standing: true},//1 top left
     {x: 0, y: 0, standing: true},//2 top right
     {x: 0, y: 0, standing: false},//3 middle
     {x1: 0, y1: 0, x2: 0, y2: true},//4 bottom left
     {x1: 0, y1: 0, x2: 0, y2: false},//5 bottom right
     {x1: 0, y1: 0, x2: 0, y2: 0} //6 bottom
 ];

 //segment locations for the different numbers
 var digits = [
     [0,1,2,0,4,5,6],//0
     [0,1,2,3,4,5,4],//1
     [0,1,2,3,4,5,4],//2
     [0,1,2,3,4,5,4],//3
     [0,1,2,3,4,5,4],//4
     [0,1,2,3,4,5,4],//5
     [0,1,2,3,4,5,4],//6
     [0,1,2,3,4,5,4],//7
     [0,1,2,3,4,5,4],//8
     [0,1,2,3,4,5,4] //9
 ];

 function draw(){
     var canvas = document.getElementById("clock");
     var ctx = canvas.getContext("2d");

     var width = canvas.width;
     var height = canvas.height;
     
     ctx.fillStyle = "#000000";
     ctx.fillRect(0, 0, width, height);

     drawSegment({}, ctx, width, height, 0);
     
 }

 function drawDigit(x, y, height, width, ctx){
 }
 
 function drawSegment(seg, ctx, width, height, rotation){
     ctx.fillStyle = segmentColor;
     
     ctx.beginPath();
     // ctx.ellipse(100, 100, 40, 72, 0, 0, 2*Math.PI);
     
     ctx.ellipse(width*segmentWidth/2, height/4, width*segmentWidth/2, height*segmentLength/2, rotation, 0, 2*Math.PI);
     ctx.ellipse(width*segmentWidth/2, height/4*3, width*segmentWidth/2, height*segmentLength/2, rotation, 0, 2*Math.PI);
     
     ctx.fill();

     setTimeout(function () {drawSegment(seg,ctx,width,height,rotation+0.1);},100);
 }

draw();
</script>


