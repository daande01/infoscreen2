<?php
//Itslearning::login("username","password" );
error_reporting(E_ALL);
include_once('simple_html_dom.php');

//Itslearning::getAnslag("","",'/ContentArea/ContentArea.aspx?LocationID=9088&LocationType=1' );



Itslearning::getNews("luksky", "4Krimskrams" );

class Itslearning {

    public static function login($username, $password) {
        $url = "https://kunskapsforbundet.itslearning.com";
        $ckfile = tempnam("/tmp", "CURLCOOKIE");
        $useragent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.342.3 Safari/533.2';
        /**
           Get __VIEWSTATE & __EVENTVALIDATION
        */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'/index.aspx');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
  
        $pre_login = curl_exec($ch);
  
        curl_close($ch);
  
        preg_match('~<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" />~', $pre_login, $viewstate);
        preg_match('~<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" />~', $pre_login, $eventValidation);
        $viewstate = $viewstate[1];
        $eventValidation = $eventValidation[1];
  
        /**
           Start Login process
        */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'/index.aspx');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
  
        // Collecting all POST fields
        $postfields = array();
        $postfields['__EVENTTARGET'] = "";
        $postfields['__EVENTARGUMENT'] = "";
        $postfields['__VIEWSTATE'] = $viewstate;
        $postfields['__EVENTVALIDATION'] = $eventValidation;
        $postfields['ctl00$ContentPlaceHolder1$Username$input'] = $username;
        $postfields['ctl00$ContentPlaceHolder1$Password$input'] = $password;
        $postfields['ctl00$ContentPlaceHolder1$nativeLoginButton'] = 'Inloggen voor gast accounts';
  
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $ret = curl_exec($ch); // Get result after login page.
    
        return $ch;
    }

    public static function getAnslag($user, $password,$kurs) {
        $ch = self::login($user, $password);
        $url = "https://kunskapsforbundet.itslearning.com";
    
        // hÃ¤mtar start sidan
        //curl_setopt($ch, CURLOPT_URL, $url.'/DashboardMenu.aspx?LocationType=Hierarchy&LocationId=4');
    
        curl_setopt($ch, CURLOPT_URL, $url.$kurs);
	
        $src = curl_exec($ch);
    
        $html = str_get_html($src);
    
        //echo $html;
	
        $DTDL = $html->find('iframe[id=ctl00_ContentAreaIframe]');
    
        $cursename = $html->find('div[id=ctl00_TreeMenu_ViewPort]');
        $name = $cursename[0]->find('span');
	
        //echo $name[1];
	
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url.$DTDL[0]->src);
        $digitalContent = curl_exec($ch);
    
        $iframe = str_get_html($digitalContent);
    
        //echo $iframe;
	
        $links = $iframe->find('ul');
        $subjects = $iframe->find('h2');
    
        //print_r ($subjects);
	
        $i = 1;
        $anslagArr;
   
        echo $subjects[0]. '<br>';
        $contents = $links[$i]->find('div[class=h-userinput itsl-light-bulletins-list-item-text]');
        $times = $links[$i]->find('div[class=itsl-light-bulletins-timespan]');
	  
        // echo $cursename[0]->plaintext;
        //  echo $contents;
        $i=0;
        foreach ($contents as $content) {
            //echo $times[$i]->plaintext."<br>";
            $anslagArr[$i][1]=$times[$i]->plaintext;
		
            //echo $content->plaintext .'<br><br>';
            $anslagArr[$i][2]=$content->plaintext;
            $anslagArr[$i][3]=$name[1];
            $i++;
        }
        echo '<br>';
        $i++;
    
        return $anslagArr;
    }

    public static function getNews($user, $password) {
        $ch = self::login($user, $password);
        $url = "https://kunskapsforbundet.itslearning.com";
    
        // hämtar start sidan
        curl_setopt($ch, CURLOPT_URL, $url.'/DashboardMenu.aspx?LocationType=Hierarchy&LocationId=4');
	
        $src = curl_exec($ch);
    
        $html = str_get_html($src);
    
        //echo $html;
        $DTDL = $html->find('iframe[id=ctl00_ContentAreaIframe]');
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url.$DTDL[0]->src);
        $digitalContent = curl_exec($ch);
    
        $iframe = str_get_html($digitalContent);
    
        //echo $iframe;
	
        $links = $iframe->find('ul');
        $subjects = $iframe->find('h2');
	
        //print_r ($subjects);
	
        $i = 1;
        $newsarr;
        //  echo $subjects[0]. '<br>';
      
        $contents = $links[$i]->find('li a');
	
        //  echo $contents;
	
        $k=0;
        foreach ($contents as $content) {
            //  echo $content->plaintext .'<br>';
            $newsarr[$k] = $content->plaintext;
            $k++;
        }
        //echo '<br>';
        $i++;
    
        return $newsarr;
    }
}
?>
