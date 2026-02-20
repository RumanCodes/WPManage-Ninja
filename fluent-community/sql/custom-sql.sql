UPDATE wp_fcom_user_activities
SET user_id = NEW_USER_ID
WHERE feed_id = POST_ID
AND action_name = 'feed_published';