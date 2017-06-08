<?php
include 'curl.php';

$its = new getitslerning();

$its->storeToDb();

$its->storeAnslagToDb();

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});	
 
class getitslerning {
    
    const USERNAME = 'luksky';
    const PASSWORD = '4Krimskrams';
 
    public $aMemberVar = 'aMemberVar Member Variable'; 
    public $aFuncName = 'aMemberFunc'; 
	
	public $database;
	
	function __construct() {
        //$database = new Database();
    }
    
    function storeToDb() { 
        $arr = Itslearning::getNews(self::USERNAME, self::PASSWORD); 

        foreach($arr as $a){
            echo $a ."<br>";
        }
    }

	function storeAnslagToDb(){
		$anslagArr = Itslearning::getAnslag(self::USERNAME, self::PASSWORD, "/ContentArea/ContentArea.aspx?LocationID=9088&LocationType=1");
		
		foreach($anslagArr as $anslag){
			echo $anslag[3]."<br><br>";
			echo $anslag[1]."<br>";
			echo $anslag[2]."<br><br>";
		}
		
		$anslagArr = Itslearning::getAnslag(self::USERNAME, self::PASSWORD,"/ContentArea/ContentArea.aspx?LocationID=3094&LocationType=1");
		
		foreach($anslagArr as $anslag){
			echo $anslag[3]."<br><br>";
			echo $anslag[1]."<br>";
            echo $anslag[2]."<br><br>";
		}
	}
} 
?> 

