<?php

class feeds {

   // declaring global connection variable
   public $connection;   
   
   function __construct(){
	   
	   try{
	   	   // database connection
		   $this->connection = new PDO('mysql:host=localhost;dbname=alert_eye','root','');
		   $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	   }
	   catch(PDOException $e){
		   echo "connection failed ".$e->getMessage();
	   }
   }
	   
   function __destruct(){
	   $connection=null;
   }
   
   
   
   	
    /**
     * This method is used to check whether particular alert has been updated or not
     *
     * @author Varun Joshi
     * @param  Int $alert_id       This is alert Id
     * @param  Date $alert_updated_date      This is alert updated date
     * @return Boolean   It returns TRUE if alert is updated else returns FALSE
     */
   	function check_alert_is_updated($alert_id, $alert_updated_date)
   	{
	   	$stmt = $this->connection->prepare("SELECT alert_id FROM alerts WHERE alert_updated_date >= '0000-00-00 00:00:00' AND alert_updated_date < ? AND alert_id=?");
	   	$stmt->bindValue(1, $alert_updated_date, PDO::PARAM_STR);
	   	$stmt->bindValue(2, $alert_id, PDO::PARAM_INT);
	   	$stmt->execute();
	   	$row_count = $stmt->rowCount();
	   	if($row_count > 0)
	   	{
		 	return true;
	   	}
	   	else
	   	{
		 	return false;
	   	}
   	}
   
   
   
   	

    /**
     * This method is used to check whether particular feed of on alert has been updated or not
     *
     * @author Varun Joshi
     * @param  Int $rss_feed_id      This is rss feed Id
     * @param  Int $alert_id        This is alert Id
     * @param  Date $feed_udpated_date      This is feed updated date
     * @return Boolean   It returns TRUE if feed is updated else returns FALSE
     */
   	function check_feed_updated_date($rss_feed_id, $alert_id, $feed_udpated_date)
   	{
       	$stmt = $this->connection->prepare("SELECT feed_id FROM feeds WHERE feed_updated_date > '0000-00-00 00:00:00' AND feed_updated_date < ? AND rss_feed_id=? AND alert_id=?");
       	$stmt->bindValue(1, $feed_udpated_date, PDO::PARAM_STR);
       	$stmt->bindValue(2, $rss_feed_id, PDO::PARAM_INT);
	   	$stmt->bindValue(3, $alert_id, PDO::PARAM_INT);
	   	$stmt->execute();
	   	$row_count = $stmt->rowCount();
	   	if($row_count > 0)
	   	{
		 	return true;
	   	}
	   	else
	   	{
		 	return false;
	   	}
   	}
   
   
   
   	

    /**
     * This method is used to udpate the alert's latest udpated date
     *
     * @author Varun Joshi
     * @param  Int $alert_id       This is alert Id
     * @param  Date $alert_updated_date      This is alert updated date
     * @return Int   It return number of affected rows
     */
   	function update_alert_updated_date($alert_id, $alert_updated_date)
   	{
		$created_date = '0000-00-00 00:00:00';
		$stmt = $this->connection->prepare("SELECT created_date FROM alerts WHERE alert_id=?");
		$stmt->bindValue(1, $alert_id, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	   
		if(isset($rows) && count($rows) > 0)
		{
		  $created_date = $rows[0]['created_date'];
		}
	   
		if($created_date != '0000-00-00 00:00:00')
		{
		   date_default_timezone_set("Asia/Kolkata");
		   $updated_date = date("Y-m-d h:i:sa");
		 
		   $stmt = $this->connection->prepare("UPDATE alerts SET alert_updated_date=:alert_updated_date, updated_date=:updated_date WHERE alert_id=:alert_id");
		   $stmt->bindParam(":alert_updated_date", $alert_updated_date, PDO::PARAM_STR);
		   $stmt->bindParam(":updated_date", $updated_date, PDO::PARAM_STR);
		   $stmt->bindParam(":alert_id", $alert_id, PDO::PARAM_INT);
		   $stmt->execute();
		   $affected_rows = $stmt->rowCount();
		   return $affected_rows;
		}
		else
		{
		   date_default_timezone_set("Asia/Kolkata");
		   $updated_date = date("Y-m-d h:i:sa");
		   $created_date = date("Y-m-d h:i:sa");
		 
		   $stmt = $this->connection->prepare("UPDATE alerts SET alert_updated_date=:alert_updated_date, created_date=:created_date, updated_date=:updated_date WHERE alert_id=:alert_id");
		   $stmt->bindParam(":alert_updated_date", $alert_updated_date, PDO::PARAM_STR);
		   $stmt->bindParam(":created_date", $created_date, PDO::PARAM_STR);
		   $stmt->bindParam(":updated_date", $updated_date, PDO::PARAM_STR);
		   $stmt->bindParam(":alert_id", $alert_id, PDO::PARAM_INT);
		   $stmt->execute();
		   $affected_rows = $stmt->rowCount();
		   return $affected_rows;
		}
   	}
   
   
   
   	

    /**
     * This method is used to check whether particular feed exists or not
     *
     * @author Varun Joshi
     * @param  Int $rss_feed_id      This is rss feed Id
     * @param  Int $alert_id       This is alert Id
     * @return Boolean   It returns TRUE if feed exists else returns FALSE
     */
   	function check_feed_exists($rss_feed_id, $alert_id)
   	{
     	$stmt = $this->connection->prepare("SELECT feed_id FROM feeds WHERE rss_feed_id=? AND alert_id=?");
     	$stmt->bindValue(1, $rss_feed_id, PDO::PARAM_INT);
		$stmt->bindValue(2, $alert_id, PDO::PARAM_INT);
		$stmt->execute();
		$row_count = $stmt->rowCount();
		if($row_count > 0)
		{
		  return true;
		}
		else
		{
		  return false;
		}
   	}
   
   
   
   	

    /**
     * This method is used to insert or udpate feeds of particular alert
     *
     * @author Varun Joshi
     * @param  Int $rss_feed_id     This is rss feed Id
     * @param  Int $alert_id     This is alert Id
     * @param  Int $feed_array     This is array of values that are inserted or updated
     * @param  Int $flag        This is flag which represents the DB operation "Insert" OR "Update"
     * @return Int     It returns affected rows
     */
   	function insert_update_feeds($rss_feed_id='', $alert_id='', $feed_array, $flag)
   	{
       	if($flag == 'insert')
       	{
          	$stmt = $this->connection->prepare("INSERT INTO feeds(`rss_feed_id`, `alert_id`, `feed_category_id`, `feed_category_name`, `feed_title`, `feed_link`, `feed_content`, `feed_published_date`, `feed_updated_date`, `feed_priority`, `is_new`, `keyword_match`, `created_date`, `updated_date`) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
          	$stmt->execute($feed_array);
          	$affected_rows = $stmt->rowCount();
          	return $affected_rows;
       }
       else
       {
          	$stmt = $this->connection->prepare("UPDATE feeds SET feed_title=?, feed_link=?, feed_content=?, feed_published_date=?, feed_updated_date=?, feed_priority=?, is_new=?, keyword_match=?, updated_date=? WHERE rss_feed_id=? AND alert_id=?");
          	$stmt->execute($feed_array);
          	$affected_rows = $stmt->rowCount();
          	return $affected_rows;
       	}
   	}

   
   
   	

    /**
     * This method is used to get all newly added feed details as per feed category
     *
     * @author Varun Joshi
     * @param  Int $feed_category_id   This is feed category Id
     * @return [type]                   [description]
     */
   	function get_all_feeds_per_category($feed_category_id)
   	{
	   	$stmt = $this->connection->prepare("SELECT feeds.feed_category_name, feeds.feed_title, feeds.feed_link, feeds.feed_content, feed_users.feed_user_name, feed_users.feed_user_email, feeds.keyword_match, feeds.feed_priority FROM feeds feeds, feed_category feed_category, feed_users feed_users WHERE feeds.feed_category_id = feed_category.feed_category_id AND feed_category.feed_category_id = feed_users.feed_category_id AND feeds.is_new=1 AND feed_category.feed_category_id=? ORDER BY feeds.feed_priority");
	   	$stmt->bindValue(1, $feed_category_id, PDO::PARAM_INT);
	   	$stmt->execute();
	   	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	   	return $rows;
   	}
   
   
   
   	

    /**
     * This method is used to get all newly added feeds of particular alert
     *
     * @author Varun joshi
     * @param  Int $alert_id    This is alert Id
     * @return Array     It returns array containing results
     */
   	function get_all_feeds_per_alert($alert_id)
   	{
       	$stmt = $this->connection->prepare("SELECT feed.feed_title, feed.feed_link, feed.feed_content, feed.keyword_match, feed.feed_priority FROM feeds feed WHERE feed.alert_id=? AND is_new=1 ORDER BY feed.feed_priority");
	   	$stmt->bindValue(1, $alert_id, PDO::PARAM_INT);
	   	$stmt->execute();
	   	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	   	return $rows;
   	}
   
   
   	

    /**
     * This method is used to update status of the feeds
     *
     * @author Varun Joshi
     * @param  Int $feed_category_id     This is feed category Id
     * @return Int     It return number of affected rows
     */
   	function update_feed_status($feed_category_id)
   	{
        $stmt = $this->connection->prepare("UPDATE feeds SET is_new=? WHERE feed_category_id=?");
        $stmt->bindValue(1, 0, PDO::PARAM_INT);
        $stmt->bindValue(2, $feed_category_id, PDO::PARAM_INT);
		$stmt->execute();
		$affected_rows = $stmt->rowCount();
		return $affected_rows;
   	}
}

?>