<?php
include 'curl2.php';

$its=new getitslerning();

$its->storeToDb();

$its->storeAnslagToDb();

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});	


 
class getitslerning { 
    public $aMemberVar = 'aMemberVar Member Variable'; 
    public $aFuncName = 'aMemberFunc'; 
    
	
	public $database;
	
	function __construct() {
      
	  //$database = new Database();
	  
	  
   }

	
	
    
    function storeToDb() { 
      
	  $arr=Itslearning::getNews("user","pass" ); 

	  foreach($arr as $a){
		  
		  echo $a ."<br>";
		  
	  }
	  
    }


	function storeAnslagToDb(){
		
		$anslagArr=Itslearning::getAnslag("user","pass","/ContentArea/ContentArea.aspx?LocationID=9088&LocationType=1" );
		
		
		$i=0;
		foreach($anslagArr as $anslag){
			echo  $anslag[3]."<br><br>";
			echo $anslag[1]."<br>";
			echo  $anslag[2]."<br><br>";
			
			$i++;
		}
		
		$anslagArr=Itslearning::getAnslag("user","pass","/ContentArea/ContentArea.aspx?LocationID=3094&LocationType=1" );
		
		
		$i=0;
		foreach($anslagArr as $anslag){
			echo  $anslag[3]."<br><br>";
			echo $anslag[1]."<br>";
			echo  $anslag[2]."<br><br>";
			
			$i++;
		}
		
	}
	
	
	
	
} 


?> 

