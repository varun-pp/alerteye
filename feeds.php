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

   function trigger_alert_email($feed_category_id)
	{
		$stmt = $this->connection->prepare("SELECT feeds.feed_category_name, feeds.feed_title, feeds.feed_link, feeds.feed_content feed_users.feed_user_name, feed_users.feed_user_email FROM feeds feeds, feed_category feed_category, feed_users feed_users WHERE feeds.feed_category_id = feed_category.feed_category_id AND feed_category.feed_category_id = feed_users.feed_category_id AND feed_category.feed_category_id=?");
			$stmt->bindValue(1, $feed_category_id, PDO::PARAM_INT);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $rows;
	}


}

?>