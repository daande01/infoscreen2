$(function() {
    startTime();
});

function startTime() {
    var date = new Date();
    var h = date.getHours();
    var m = date.getMinutes();
    var s = date.getSeconds();
    m = formatTime(m);
    s = formatTime(s);
    
    $('#clock').html( h + ":" + m + ":" + s);
    var t = setTimeout(startTime, 1000);
}

function formatTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}
