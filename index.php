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
			   $email_data = '';
			   
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
							
							$feed_keywords = array();
							$feed_keywords = $db_obj->get_feed_keywords($alert_id);
					
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
											   
											   if($keyword == 'Deprecat')
											   {
											      if(preg_match("/\bDeprecat[a-zA-Z]\b/i", addslashes($feed_content), $match))
											      {
											         $feed_priority = $keyword_priority;
													 $keyword_match = 1;
													 break;
											      }
											   }
											   else if($keyword == 'v')
											   {
											       if(preg_match("/\bv[\d]\b/i", addslashes($feed_content), $match))
											       {
											          $feed_priority = $keyword_priority;
													  $keyword_match = 1;
													  break;
											       }
											   }
											   else
											   {
												   if(strpos(strtolower(addslashes($feed_content)), $keyword) !== false)
												   {
													  $feed_priority = $keyword_priority;
													  $keyword_match = 1;
													  break;
												   }
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
										  }
									   }
									   else
									   {
										  date_default_timezone_set("Asia/Kolkata");
										  $created_date = date("Y-m-d h:i:sa");
										  $updated_date = date("Y-m-d h:i:sa");
										  $feed_insert_array = array($rss_feed_id, $alert_id, $feed_category_id, $feed_category_name, $feed_title, $feed_link, $feed_content, $feed_published_date, $feed_updated_date, $feed_priority, 1, $keyword_match, $created_date, $updated_date);
										  $feed_inserted_count = $feeds_obj->insert_update_feeds('','',$feed_insert_array, 'insert');
									   }
								   }
							}
					  }
			    }
			   
			   if(isset($feed_alerts) && count($feed_alerts) > 0)
			   {
			        $feed_category_user = $db_obj->get_feed_category_user($feed_category_id);
			        $feed_user_name = $feed_category_user[0]['feed_user_name'];
			        $feed_user_email = $feed_category_user[0]['feed_user_email'];
			        
			        foreach($feed_alerts as $alert)
			        {
						  $alert_id = $alert['alert_id'];
						  $alert_url = $alert['alert_url'];
						  $alert_url_title = $alert['alert_url_title'];
						
						  $new_feeds = $feeds_obj->get_all_feeds_per_alert($alert_id);
						  
						  if(isset($new_feeds) && count($new_feeds) > 0)
						  {
								$email_data .= '<table border="0" cellspacing="0" cellpadding="0">';
								   $email_data .= '<tr>
													   <td style="padding-bottom: 10px; font-weight: normal; font-size: 15px; line-height: 18px;  font-family: Arial; color: #757067;">'.$alert_url_title.'</td>
												   </tr>';
								
								foreach($new_feeds as $feeds)
								{
									  $feed_title = $feeds['feed_title'];
									  $link = explode("&", $feeds['feed_link'])[2];
									  $feed_link = explode("=", $link)[1];
									  $feed_content = $feeds['feed_content'];
									  $keyword_match = $feeds['keyword_match'];
								  
									  $color = '';
									  if($keyword_match == 1)
									  {
										$color = "red";
									  }
								  
									  $email_data .= '<tr>
														  <td style="padding-bottom: 4px; font-weight: normal; font-size: 11px; line-height: 14px;  font-family: Arial; color: #757067; color:'.$color.'">'.$feed_content.'</td>
													  </tr>

													  <tr>
														  <td style="padding-bottom: 10px; color: #d7d1c7;"><a href="'.$feed_link.'" target="_blank" style="font-weight: normal; font-size: 11px; line-height: 15px; font-family: Arial; color: #b18910; text-transform: uppercase; text-decoration: none;">VIEW DETAILS</a></td>
													  </tr>';
								}
								
								$email_data .= '</table>';
						  }
			        }
			        
			        if($email_data != '')
					{
						  $to = $feed_user_email;
						  $subject = 'Alerts For: '.$feed_category_name;
						  
						  $url = $_SERVER['HTTP_HOST']."/emailer.html";
						  $ch = curl_init();
						  curl_setopt ($ch, CURLOPT_URL, $url);
						  curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
						  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
						  $contents = curl_exec($ch);
						  if (curl_errno($ch)) {
							  $contents = '';
						  } else {
							  curl_close($ch);
						  }

						  if (!is_string($contents) || !strlen($contents)) {
							  $contents = '';
						  }
					 
						  $message = $contents;
					 
						  $message = str_replace("_@feeds@_",$email_data, $message);
						  $message = str_replace("_@feed_category@_",$feed_category_name, $message);
						  $message = str_replace("_@name@_",$feed_user_name, $message);
						  $message = str_replace("_@year@_",date('Y'), $message);
						  
						  $msgHeaders = "";
						  $msgHeaders .= "From:Alert Eye <alerteye@paperplane.net>\r\n";
						  $msgHeaders .= "MIME-Version: 1.0\r\n";
						  $msgHeaders .= "Content-Type: text/html; charset=utf-8\r\n";
						  
						  mail($to, $subject, $message, $msgHeaders);
						  
						  //$feeds_obj->update_feed_status($feed_category_id);
					 
						  echo $message;
					}
			   }
 		  }
 	 }		
 	

?>