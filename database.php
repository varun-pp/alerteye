<?php

 class DBFunctions extends feeds {



	
    /**
     * This method is used to get all feed categories
     *
     * @author Varun Joshi
     * @return Array    It returns array of all feed categories
     */
	function get_all_feed_category()
	{
		$stmt = $this->connection->prepare("SELECT feed_category.*, feed_users.feed_user_name, feed_users.feed_user_email FROM feed_category feed_category, feed_users feed_users WHERE feed_category.feed_category_id = feed_users.feed_category_id");
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
	
	


    
    /**
     * This method is used to get all alerts set for particular feed category
     *
     * @author Varun Joshi
     * @param  Int $feed_category_id      This is feed category Id
     * @return Array     It returns array of feed alerts
     */
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
	
	


    
    /**
     * This method is used to get all priority keywords for particular alert
     *
     * @author Varun Joshi
     * @param  Int $alert_id     This is alert Id
     * @return Array      It returns array of all keywords
     */
	function get_feed_keywords($alert_id)
	{
	   $stmt = $this->connection->prepare("SELECT feed_keyword, feed_keyword_priority FROM feed_keyword WHERE alert_id=? ORDER BY feed_keyword_priority");
	   $stmt->bindValue(1, $alert_id, PDO::PARAM_INT);
	   $stmt->execute();
	   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	   return $rows;
	}
	
	


    /**
     * This method is used to get user details associated with particular feed category
     *
     * @author Varun Joshi
     * @param  Int $feed_category_id
     * @return Array     It returns array of user details
     */
	function get_feed_category_user($feed_category_id)
	{
	   $stmt = $this->connection->prepare("SELECT feed_user_name, feed_user_email FROM feed_users WHERE feed_category_id=?");
	   $stmt->bindValue(1, $feed_category_id, PDO::PARAM_INT);
	   $stmt->execute();
	   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	   return $rows;
	}
	
	
	
	
	/**
     * This method is used to send email
     *
     * @author Varun Joshi
     * @param  String $email     This is email Id
     * @param  String $Sub      This is mail subject 
     * @param  String $data     This is email body message
     * @return Array     It returns array of user details
     */
	function send_email($email, $sub, $data)
	{
	      error_reporting(2);
          ini_set('display_errors', 1);
          set_error_handler("var_dump");
	      
	      $to = $email;
		  $subject = $sub;
		  
		  $url = "192.168.0.54:8093/emailer/emailer-tech.html";
		  $ch = curl_init();
		  curl_setopt ($ch, CURLOPT_URL, $url);
		  curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
		  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		  $entire_contents = curl_exec($ch);
		  if (curl_errno($ch)) {
			  $entire_contents = '';
		  } else {
			  curl_close($ch);
		  }

		  if (!is_string($entire_contents) || !strlen($entire_contents)) {
			  $entire_contents = '';
		  }
	 
		  $entire_message = $entire_contents;
	 
		  $entire_message = str_replace("_@entire_feeds@_",$data, $entire_message);
		  $entire_message = str_replace("_@name@_",'Tech', $entire_message);
		  $entire_message = str_replace("_@year@_",date('Y'), $entire_message);
		  
		  $msgHeaders = "";
		  $msgHeaders .= "From:Alert Eye <alerteye@paperplane.net>\r\n";
		  $msgHeaders .= "MIME-Version: 1.0\r\n";
		  $msgHeaders .= "Content-Type: text/html; charset=utf-8\r\n";
		  
		  if(mail($to, $subject, $entire_message, $msgHeaders))
		  {
		     echo 1;
		  }
		  else
		  {
		     echo 0;
		  }
	}

		 
 }
?>