<?php
 class DBFunctions extends feeds {



	function get_all_feed_category()
	{
		$stmt = $this->connection->prepare("SELECT feed_category.*, feed_users.feed_user_name, feed_users.feed_user_email FROM feed_category feed_category, feed_users feed_users WHERE feed_category.feed_category_id = feed_users.feed_category_id");
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
	
	function get_feed_alerts($feed_category_id='')
	{
		if($feed_category_id=='')
		{
		   $stmt = $this->connection->prepare("SELECT alert_id, alert_url, alert_url_title FROM alerts");
		}
		else
		{
		   $stmt = $this->connection->prepare("SELECT alert_id, alert_url, alert_url_title FROM alerts WHERE feed_category_id=?");
		   $stmt->bindValue(1, $feed_category_id, PDO::PARAM_INT);
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
	
	function get_feed_keywords($alert_id)
	{
	   $stmt = $this->connection->prepare("SELECT feed_keyword, feed_keyword_priority FROM feed_keyword WHERE alert_id=? ORDER BY feed_keyword_priority");
	   $stmt->bindValue(1, $alert_id, PDO::PARAM_INT);
	   $stmt->execute();
	   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	   return $rows;
	}
	
	function get_feed_category_user($feed_category_id)
	{
	   $stmt = $this->connection->prepare("SELECT feed_user_name, feed_user_email FROM feed_users WHERE feed_category_id=?");
	   $stmt->bindValue(1, $feed_category_id, PDO::PARAM_INT);
	   $stmt->execute();
	   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	   return $rows;
	}
		 
 }
?>