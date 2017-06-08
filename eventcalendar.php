
<?php

//TODO Swedish chars

require 'class.iCalReader.php';

error_reporting(E_ALL);

set_error_handler('exceptions_error_handler');

function exceptions_error_handler($severity, $message, $filename, $lineno) {
    echo $message." in ".$filename. " on line ".$lineno;
}

/*
   echo '<html><head>';
   echo '<meta charset="UTF-8">';
   echo '<link href="eventcalendar.css" rel="stylesheet">';
   echo '</head><body>';

   header('Content-type: text/html; charset=utf-8');

   $calendar = new EventCalendar();
   $calendar->refreshDB();

   echo $calendar->getEventCalendar(4);
 */
class EventCalendar {

    const DATABASE_HOST = 'localhost';
    const DATABASE_NAME = 'id1849698_teknikinformation';
    const DATABASE_USERNAME = 'id1849698_user';
    const DATABASE_PASSWORD = '';

    //
    const CALENDAR_URLS = array('http://kunskapsforbundet.itslearning.com/Calendar/CalendarFeed.ashx?LocationType=3&LocationID=0&PersonId=3727&CustomerId=2480&ChildId=0&Guid=7c6aa3ea77d2009b8cca21181cd5d08f&Culture=sv-SE&FavoriteOnly=True');
    const CALENDAR_TIMEZONE = "UTC";
    const TIMEZONE = "Europe/Stockholm";

    const STRIPE_WIDTH = 18;
    
    public $db = null;

    public function  __construct(){
        date_default_timezone_set('Europe/Stockholm');
        $this->db = $this->getDBConnection();
    }

    private function getCalendarFromIts(){
        $i=0;
        foreach(self::CALENDAR_URLS as $URL){
            $calString = file_get_contents($URL);

            $calFile = fopen("its-calendar$i.ics", "w");//TODO handle error
            fwrite($calFile, $calString);
            fclose($calFile);

            $i++;
        }
        return true;
    }

    private function parseCalandar(){
        $events = array();
        for($i=0;$i<count(self::CALENDAR_URLS);$i++){
            $cal = new ICal("its-calendar$i.ics");
            try {
                $calEvents = $cal->events();
                

                if(!empty($calEvents)){
                    
                    foreach ($calEvents as $calEvent) {
                        array_push($events, array (
                            "start_time" => self::icalTimeToMySQLTime($calEvent['DTSTART']),
                            "end_time"   => self::icalTimeToMySQLTime($calEvent['DTEND']),
                            "course"     => $calEvent['SUMMARY'],
                            "text"       => $calEvent['DESCRIPTION']));
                    }
                }
            }
            catch(Exception $ex){}
        }
        return $events;
    }

    private function icalTimeToMySQLTime($datetime){
        $date = new DateTime($datetime, new DateTimezone(self::CALENDAR_TIMEZONE));
        $date->setTimezone(new DateTimezone(self::TIMEZONE));
        return $date->format('Y-m-d H:i:s');
    }

    private function insertEvents($events){
        if(empty($events)){
            return;
        }

        $sql = "INSERT INTO events (start_time, end_time, course, text) VALUES ";
        
        $i = 0;
        foreach($events as $event){
            $sql .= "(:start$i, :end$i, :course$i, :text$i),"; 
            $i++;
        }

        $sql = substr($sql, 0, -1); //removes the last char (,).
        $sql .= ';';
        

        $statement = $this->db->prepare($sql);
        $i=0;
        foreach($events as $event){
            $statement->bindValue(":start$i",  $event["start_time"]);
            $statement->bindValue(":end$i",    $event["end_time"]);
            $statement->bindValue(":course$i", $event["course"]);
            $statement->bindValue(":text$i",   $event["text"]);
            $i++;
        }

        $statement->execute();
        
        /*
           $stmt->bindParam(":word", $testclean, PDO::PARAM_STR);
           $stmt->execute();*/
    }

    private function findParticipatingClassesInEventsInDB(){
        $statement = $this->db->prepare('SELECT id, text, course FROM events');
        $statement->execute();
        $events = $statement->fetchAll();

        if(empty($events)){
            return;
        }
        
        $statement = $this->db->prepare('SELECT name FROM classes');
        $statement->execute();
        $classes = $statement->fetchAll();
        
        $sql = "INSERT INTO event_classes (event_id, class) VALUES ";
        
        foreach($events as $event){
            foreach($classes as $class){
                // Test if event text contains a class name.
                // The comparison with false is requried since 0 will evaluate to false
                if((stripos($event['text'], $class['name']) !== false) ||
                   (stripos($event['course'], $class['name']) !== false)){
                    $sql .= '("' . $event['id'] . '", "' . $class['name'] . '"),';
                }
            }           
        }
        
        $sql = substr($sql, 0, -1); //removes the last char (,).
        $sql .= ';';

        $statement = $this->db->prepare($sql);
        $statement->execute();
    }

    private function getDBConnection(){
        $conn = null;
        try {
    	    $conn = new PDO('mysql:host=' . self::DATABASE_HOST . ';dbname=' . self::DATABASE_NAME . '; charset=utf8',
                                self::DATABASE_USERNAME,
                                self::DATABASE_PASSWORD);
    	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    	    
    	    /*
    	       PDO::ERRMODE_SILENT - database-related errors will be ignored.
    	       PDO::ERRMODE_WARNING - database-related errors will cause a warning to be emitted, but execution will continue.
    	       PDO::ERRMODE_EXCEPTION - database-related errors will cause a PDOException to be thrown.
    	     */
        } catch(PDOException $e) {
	       echo 'ERROR: ' . $e->getMessage();
        }
        
        return $conn;
    }

    /*
     * Interface functions
     */

    public function clearEventsFromDB(){
        $statement = $this->db->prepare("DELETE FROM events; DELETE FROM event_classes");
        $statement->execute();
    }

    public function refreshDB(){        
        if($this->getCalendarFromIts()){
            $this->clearEventsFromDB();
            $parsedCal = $this->parseCalandar();
            $this->insertEvents($parsedCal);
            $this->findParticipatingClassesInEventsInDB();
        }
        else return false;
        return true;
    }

    //if $classes is empty array the sql will break
    public function getEvents($weekCount = 1, $startWeek = null, $classes = null){
        if($startWeek == null) $startWeek = date('W');
        if($weekCount < 1) $weekCount = 1;

        
        $year = date('Y');
        if($startWeek < date('W')){
            $year++;
        }
        
        $date = new DateTime();
        $date->setISODate($year, $startWeek);
        $date->setTime(0,0,0);
        $startDate = $date->format('Y-m-d H:i:s');
        $date->modify('+' . (7*$weekCount) . ' days');
        $date->modify('-1 sec');
        $endDate = $date->format('Y-m-d H:i:s');
        
        $sql = "SELECT DISTINCT events.start_time, events.end_time, events.course, events.text, events.id
                FROM events, event_classes
                WHERE (start_time BETWEEN '$startDate' AND '$endDate') ";
        if(isset($classes)){
            $sql .= 'AND (';

            if(!is_array($classes)){ //If single class is given, wrap it in array to enable iteration.
                $classes = array($classes);
            }

            foreach($classes as $class){
                $sql .= " (events.id = event_classes.event_id AND event_classes.class = '$class') OR";
            }
            $sql = substr($sql, 0, -2); //removes the last "OR" .
            $sql .= ')';
        }
        $sql .= ';';
        
        $statement = $this->db->prepare($sql);
        $statement->execute();
        $events = $statement->fetchAll();
        return $events;
    }


    public function getEventCalendar($weekCount, $startWeek = null, $classes = null){
        if($startWeek == null) $startWeek = date('W');
        if($weekCount < 1) $weekCount = 1;

        $events = $this->getEvents($weekCount, $startWeek, $classes);

        $html = <<<'TABLE'
        <table id="event-calendar">
          <thead>
            <tr class="day-label-row">
              <th style="border: none"><a id="toggle-auto-update" class="tooltipped" style="color:#ff3d00"  data-position="right" data-delay="50" data-tooltip="Uppdatera automatiskt var femte minut "><i class="material-icons">restore</i></a></th>
              <th>M&aring;ndag</th>
              <th>Tisdag</th>
              <th>Onsdag</th>
              <th>Torsdag</th>
              <th>Fredag</th>
            </tr>
          </thead>
          <tbody>
TABLE;

        $year = date('Y');
        if($startWeek < date('W')){
            $year++;
        }
        $date = new DateTime();
        $date->setISODate($year, $startWeek);
        $date->setTime(0,0,0);
        $startDate = $date->format('Y-m-d H:i:s');
        
        /* $classes = array(array('color' => "dde6fb"),array('color' => 'fef3cd'));*/

        $today = date('m-d');
        
        for($i = 0; $i<$weekCount; $i++){
            $html .= '<tr class="'. ($i%2==0?"odd-row":"even-row") .'">';
            $html .= '<th class="week-nr-label">'. (($startWeek + $i)%52) .'</th>';
            for($j = 0; $j<5;$j++){
                $html .= '<td class="day-cell';
                
                //TODO test this during week, does not apply during week ends
                if($date->format('m-d') == $today) { 
                    $html .= ' today';
                }
                $html .= '"><p class="day-cell-background">'. (false?'&#x3c0;' : $date->format('d')) .'</p>';

                /*$html .=
                   '<div class="event">This is an event with some text.
                   It is quite long and will take up more than one row.</div>'.
                   '<div class="event">This is another event.</div>';
                 */
                $eventNumber = 0;
                foreach($events as $event){
                    //Test if the event occurs on the current date
                    if(strcmp(
                        (new DateTime($event['start_time']))->format('Y-m-d'),
                        ($date->format('Y-m-d'))) == 0){

                        $html .= '<p class="event'.($eventNumber==0?" first-event":"") .'"';

                        //Find the colors for the events
                        $sql = <<<QUERY
                          SELECT classes.color
                          FROM classes, events, event_classes
                          WHERE (events.id = event_classes.event_id AND event_classes.class = classes.name)
                            AND (events.id = :eventid)
QUERY;
                        $statement = $this->db->prepare($sql);
                        $statement->bindValue(':eventid', $event['id']);
                        $statement->execute();
                        $classes = $statement->fetchAll();
                        
                        
                        $html .= 'style="background: repeating-linear-gradient(-45deg';

                        $first = true;
                        for($k = 0; $k<count($classes); $k++){
                            $color = '#'. $classes[$k]['color'];
                            $html .= ', '. $color .' ';
                            if(!$first){
                                $html .= (self::STRIPE_WIDTH*($k)) .'px';
                            }
                            $html .= ', '. $color. ' '. (self::STRIPE_WIDTH*($k+1)) .'px';
                            
                            $first = false;
                        }

                        $html .= ');"> '. $event['text'].'</p>';
                        $eventNumber++;
                    }
                    
                }

                $html .= '</div></td>';
                $date->add(new DateInterval('P1D'));
            }
            $html .= '</tr>';
            $date->add(new DateInterval('P2D'));//Add weekend
        }
        $html .= '</tbody></table>';
        
        $statement = $this->db->prepare("SELECT * FROM classes");
        $statement->execute();
        $res = $statement->fetchAll();

        /*
           foreach($res as $r){
           echo '<div width=50 height=20
           style="background:#' . $r["color"] .         
           ';text-align:center;font-size: 50">'.
           $r["name"] . '</div>';
           }*/

        
        return $html;
    }
}
