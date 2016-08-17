<?php
 	
    include_once("feeds.php");
    include_once("database.php");
	
 	$feeds_obj = new feeds();
 	$db_obj = new DBFunctions();
 	
 	$feed_category_id = '';
	$feed_category_name = '';
	$feed_user_name = '';
	$feed_user_email = '';
 	
 	$categories = $db_obj->get_all_feed_category();
 	
 	if(isset($categories) && count($categories) > 0)
 	{
		 foreach($categories as $category)
		 {
			   $feed_category_id = $category['feed_category_id'];
			   $feed_category_name = $category['feed_category_name'];
			   $feed_user_name = $category['feed_user_name'];
			   $feed_user_email = $category['feed_user_email'];
		 
			   $feed_alerts = $db_obj->get_feed_alerts($feed_category_id);
			   $feed_alert_count = count($feed_alerts);
			 
			   if(isset($feed_alerts) && count($feed_alerts) > 0)
			   {
					 foreach($feed_alerts as $alert)
					 {
							$alert_id = $alert['alert_id'];
							$alert_url = $alert['alert_url'];
							$alrt_url_title = $alert['alert_url_title'];
					
							$content=file_get_contents($alert_url);
							$feed_content = new SimpleXmlElement($content);
					
							if(isset($feed_content->updated))
							{
								   $feed_alert_count++;
						  
								   $date = new DateTime($feed_content->updated, new DateTimeZone('Etc/GMT'));
								   $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
								   $alert_updated_date = $date->format('Y-m-d h:i:sa');
						
								   if($feeds_obj->check_alert_is_updated($alert_id, $alert_updated_date))
								   {
									  $updated_rows = $feeds_obj->update_alert_updated_date($alert_id, $alert_updated_date);
								   }
							
								   $feed_keywords = $db_obj->get_feed_keywords($alert_id);
					  
								   foreach($feed_content->entry as $feed)
								   {
									   $rss_feed_id = $feed->id;
									   $feed_title = $feed->title;
									   foreach ($feed->link as $link) 
									   {
										   $feed_link = trim($link["href"]);
									   }
									   $feed_content = $feed->content;
									   $date = new DateTime($feed->published, new DateTimeZone('Etc/GMT'));
									   $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
									   $feed_published_date = $date->format('Y-m-d h:i:sa');
								
									   $date = new DateTime($feed->updated, new DateTimeZone('Etc/GMT'));
									   $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
									   $feed_updated_date = $date->format('Y-m-d h:i:sa');
									   $feed_priority = 999999;
									   $keyword_match = 0;
									   if(isset($feed_keywords) && count($feed_keywords) > 0)
									   { 
										   foreach($feed_keywords as $keywords)
										   {
											   $keyword = strtolower($keywords['feed_keyword']);
											   $keyword_priority = $keywords['feed_keyword_priority'];
											   if(strpos(strtolower($feed_content), $keyword) !== false)
											   {
												  $feed_priority = $keyword_priority;
												  $keyword_match = 1;
												  break;
											   }
										
										   }
									   }
								
									   if($feeds_obj->check_feed_exists($rss_feed_id, $alert_id)==true)
									   {
										  if($feeds_obj->check_feed_updated_date($rss_feed_id, $alert_id, $feed_updated_date))
										  { 
											  date_default_timezone_set("Asia/Kolkata");
											  $updated_date = date("Y-m-d h:i:sa");
											  $feed_update_array = array($feed_title, $feed_link, $feed_content, $feed_published_date, $feed_updated_date, $feed_priority, 1, $keyword_match, $updated_date, $rss_feed_id, $alert_id);	
											  $feed_updated_count = $feeds_obj->insert_update_feeds($rss_feed_id, $alert_id, $feed_update_array, 'udpate');
											  echo $feed_updated_count;
										  }
									   }
									   else
									   {
										  date_default_timezone_set("Asia/Kolkata");
										  $created_date = date("Y-m-d h:i:sa");
										  $updated_date = date("Y-m-d h:i:sa");
										  $feed_insert_array = array($rss_feed_id, $alert_id, $feed_category_id, $feed_category_name, $feed_title, $feed_link, $feed_content, $feed_published_date, $feed_updated_date, $feed_priority, 1, $keyword_match, $created_date, $updated_date);
										  $feed_inserted_count = $feeds_obj->insert_update_feeds('','',$feed_insert_array, 'insert');
										  echo $feed_inserted_count;
									   }
								   }
							}
					  }
			   }
 		  }
 	 }		
 	

?>