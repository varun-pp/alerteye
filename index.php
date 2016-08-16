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
						  $date = new DateTime($feed_content->updated, new DateTimeZone('Etc/GMT'));
						  $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
						  $alert_updated_date = $date->format('Y-m-d h:i:sa');
						
						  if($feeds_obj->check_feed_is_updated($alert_id, $alert_updated_date))
						  {
						  
						  }
					 
					   }
			      }
		 
		     }
 		}
 	}		
 	

?>