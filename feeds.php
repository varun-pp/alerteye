<?php

class feeds {

   public $connection;   
   function __construct(){
	   try{
		   $this->connection = new PDO('mysql:host=localhost;dbname=alert_eye','root','');
		   $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		   //echo "connection successfull";
	   }
	   catch(PDOException $e){
		   echo "connection failed ".$e->getMessage();
	   }

   }
	   
   function __destruct(){
	   $connection=null;
   }
   
   
   
   function check_feed_is_updated($alert_id, $alert_updated_date)
   {
	  $stmt = $this->connection->prepare("SELECT alert_id FROM alerts WHERE alert_updated_date > '0000-00-00 00:00:00' AND alert_updated_date");
	  $stmt->execute();
	  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	  return $rows;
   }


}

?>