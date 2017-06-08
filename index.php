<?php
require 'eventcalendar.php';
require 'teacherinfo.php';

header('Content-type: text/html; charset=utf-8');
$URI = parse_url($_SERVER['REQUEST_URI']);
$URI_parts  = explode('/', $URI['path']);
$calendar = new EventCalendar();
$calendar->refreshDB();

function getTitle($location){
    switch($location){
        case 'start':
        case '':
            return 'Start';
        case 'kalender':
            if($_GET['klass']=='alla'){
                return 'Hela Teknikprogrammet';
            }
            return $_GET['klass'];
        case 'larare':
            return 'F&ouml;r l&auml;rare';
        default:
            return ucfirst($location);
    }

    return $location;
}

function displayBreadcrumb($location){
    echo '<div style="float: left">';
    switch($location){
        case 'larare':
            echo '<a href="#" data-activates="slide-out" class="breadcrumb last-breadcrumb">F&ouml;r l&auml;rare</a>';
            break;
        case 'kalender':
            echo '<a href="kalender?klass=alla" class="breadcrumb"> Kalender</a>'.
                 '<a href="#!" data-activates="slide-out" class="breadcrumb last-breadcrumb">';
            if($_GET['klass']=='alla'){
                echo 'Hela Teknikprogrammet';
            }
            else {
                echo ucfirst($_GET['klass']);
            }
            echo '</a>';
            break;
        case '':
        case 'start':
            echo '<a href="#" data-activates="slide-out" class="breadcrumb last-breadcrumb">Start</a>';
            break;
        default:
            // Simply display the URL location and
            // make first letter upper case
            //TODO not safe?
            echo '<a href="#" data-activates="slide-out" class="breadcrumb last-breadcrumb">'.ucfirst($location).'</a>';
            break;
    }
    echo '</div>';
}

function displayCalTypeSwitcher(){
    echo '<div style="display: inline-block; margin-left: 100px;">';
    if(!isset($_GET['veckor']) || $_GET['veckor'] == 4){
        echo '<a href="kalender?klass='. $_GET['klass'];
        echo '&veckor=20" id="cal-switch"';
        echo 'class="tooltipped" data-position="down" data-delay="50"';
        echo 'data-tooltip="Visa 20 veckor">';
        echo '<i class="material-icons">view_list</i></a>';
    }
    else {
        echo '<a href="kalender?klass='. $_GET['klass'];
        echo '&veckor=4" id="cal-switch"';
        echo 'class="tooltipped" data-position="down" data-delay="50"';
        echo 'data-tooltip="Visa 4 veckor">';
        echo '<i class="material-icons">view_module</i></a>';
    }
    echo '</div>';
}

function displayMenuCalendarOptions($location){
    // option for all classes
    echo '<li><a href="kalender?klass=alla&veckor=';
    if(!array_key_exists('veckor', $_GET)){
        $_GET['veckor']=4;
    }
    
    echo $_GET['veckor'];

    echo '" class="waves-effect waves-light';
    if($location == 'kalender' && $_GET['klass'] == 'alla'){
        echo ' deep-orange'; // mark orange
    }
    echo '"><i class="material-icons">today</i>Hela Teknikprogrammet</a></li>';

    global $calendar; // obtain access to outer variable
    $statement = $calendar->db->prepare("SELECT name FROM classes");
    $statement->execute();
    $classes = $statement->fetchAll();
    
    foreach($classes as $class){ // add all class calendars to menu
        $menuItem = '<li><a href="kalender?';
        $menuItem .= 'klass='. $class['name'];
        $menuItem .= '&veckor='. max(4, $_GET['veckor']);
        $menuItem .= '" class="waves-effect waves-light';
        //highligt if current location
        if($location  == 'kalender' &&
           $class['name'] == $_GET['klass']){
            $menuItem .= ' deep-orange';
        }
        $menuItem .= '">';
        $menuItem .= '<i class="material-icons">today</i>';
        $menuItem .= $class['name'];
        $menuItem .= '</a></li>';
        echo $menuItem;
    }
}

function displayCalendar($calendar){
    if($_GET['klass'] == 'alla'){
        echo $calendar->getEventCalendar(max(1, $_GET['veckor']), null, null);
    }
    else {
        echo $calendar->getEventCalendar(
            max(1, $_GET['veckor']), null, explode(',', $_GET['klass']));
    }
    //TODO dangerous?

}

function displayMarquee(){
    echo '
    <div id="info-wrapper">
    <ul>
    <li id="lunch">
    <img src="img/knifeandfork.png">
    <p>';
    
    $food = file_get_contents(
        'http://skolmaten.se/birger-sjoberggymnasiet/rss/today/');
    // scrape RSS feed
    $CDATA_start = strpos($food, '[CDATA[');
    if($CDATA_start) {//Menu available. Handles weekends
        $food = substr($food, strpos($food, '[CDATA['));
        $food = substr($food, 7, strpos($food, ']]')-7);//7, and -7 stips RSS code
        //remove asterixes
        $food = str_replace('*','',$food);
        echo $food;
    }
    /*
       $temadagar = file_get_contents('https://temadagar.se/');
       $temadagar = substr($temadagar, strpos($temadagar, '<center>'));
       $temadagar = substr($temadagar, 0, strpos($temadagar, '</center>'));
       echo $temadagar;*/

    echo '
    </p>
    </li>
    <li><p>Lite mer information som kan vara bra att visa.</p>
    <p>F&ouml;rslag p&aring; vad som skall visar h&auml;r &auml;r v&auml;lkommna!</p></li>
    </ul>
    </div>';
    
}

?>
<html lang="sv">
    <head>
        <meta charset="utf-8"/>
	<title><?php echo getTitle((isset($URI_parts) && array_key_exists(1, $URI_parts))?$URI_parts[1]:"start"); ?></title>	
        
        <!-- Materialize CSS -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">      
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css" type="text/css">
        
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <link href="css/eventcalendar.css" rel="stylesheet">
        <?php echo getTeacherInfoCSSLink(); ?>
    </head>
    <body>
        <nav class="deep-orange" role="navigation">
            <div id="header-container" class="nav-wrapper right">
                <a href="#" data-activates="slide-out" id="show-menu"><i class="material-icons left">menu</i></a>
                <?php
                if((isset($URI_parts) && array_key_exists(1, $URI_parts))){
                    displayBreadcrumb($URI_parts[1]);
                    
                    if($URI_parts[1]=="kalender") {
                        displayCalTypeSwitcher();
                    }
                }
                ?>
                
                <a id="logo-container" href="#" class="brand-logo center">Teknik Information</a>
                <img src="img/BSG_logga2.png" id="bsg-logo" class="right">

                <link href="https://fonts.googleapis.com/css?family=Fontdiner+Swanky" rel="stylesheet">
                <link href="https://fonts.googleapis.com/css?family=Press+Start+2P" rel="stylesheet">
                <h3 id="clock" class="right" style="margin-top: 10px; margin-right:4px; font-family: 'Fontdiner Swanky', cursive; width:190px; color:black;"></h3>

                
                <ul id="slide-out" class="side-nav left">
                    <li><a href="start" class="waves-effect waves-light">Start</a></li>
                    <li><div class="divider"></div></li>
                    <li><a class="subheader">H&auml;ndelsescheman</a></li>
                    <?php displayMenuCalendarOptions($URI_parts[1]); ?>

                    <li><div class="divider"></div></li>
                    <li><a class="subheader">Information</a></li>
                    <li><a href="larare" class="waves-effect waves-light"><i class="material-icons">info_outline</i>F&oumlr l&auml;rare</a></li>
                    <li><a href="#!" class="waves-effect waves-light"><i class="material-icons">info_outline</i>F&oumlr IT-elever</a></li>
                </ul>            
            </div>
        </nav>

        <!-- <div id="content"> -->

        <div id="calendar-wrapper" class="left">
            <?php
            if(isset($URI_parts) && array_key_exists(1, $URI_parts)){
                switch($URI_parts[1]){
                    case '':
                        break;
                    case 'kalender':
                        displayCalendar($calendar);
                        break;
                    case 'larare':
                        echo getTeacherInfoContent();
                        break;            
                }
            }
            else {
                //Do nothing
            }
            ?>
        </div>

        <?php if(isset($URI_parts) && array_key_exists(1, $URI_parts) && $URI_parts[1]=='kalender'){displayMarquee();}?>
        <!-- </div> -->
        
        <!-- JQuery -->
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <!-- Materialize JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>

        <!-- Page script -->
        <script src="js/script.js"></script>
        <script src="js/clock.js"></script>
        <script src="js/calendar.js"></script>
    </body>
</html>
