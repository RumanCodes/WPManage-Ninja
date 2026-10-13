-- FINAL: merge FluentCRM contact @dup INTO @keep, delete @dup, and give @keep the duplicate's email.
-- 1) FULL DATABASE BACKUP FIRST.  2) Set the two IDs.  3) Replace "wp_" if your table prefix differs.
-- Run the whole script in ONE go (phpMyAdmin: SQL tab, paste everything, Go).
SET @keep = 0;   -- contact to KEEP (old email, most activity)
SET @dup  = 0;   -- contact created by the booking (its email becomes the kept contact's email)

SET @new_email = (SELECT email FROM wp_fc_subscribers WHERE id = @dup);

START TRANSACTION;

-- 1. Move activity linked by contact ID
UPDATE wp_fc_campaign_emails       SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_campaign_url_metrics  SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_funnel_metrics        SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE IGNORE wp_fc_funnel_subscribers SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_sequence_tracker      SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_subscriber_notes      SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_sms_messages          SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_message_threads       SET contact_id    = @keep WHERE contact_id    = @dup;
UPDATE IGNORE wp_fc_subscriber_pivot SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_subscriber_meta SET subscriber_id = @keep
 WHERE subscriber_id = @dup
   AND `key` NOT IN (SELECT k FROM (SELECT `key` AS k FROM wp_fc_subscriber_meta WHERE subscriber_id = @keep) AS existing);
UPDATE wp_fcal_bookings SET person_contact_id = @keep WHERE person_contact_id = @dup;

-- 2. Remove the duplicate (leftover shared tags/automations/fields, then the contact itself)
DELETE FROM wp_fc_subscriber_pivot   WHERE subscriber_id = @dup;
DELETE FROM wp_fc_funnel_subscribers WHERE subscriber_id = @dup;
DELETE FROM wp_fc_subscriber_meta    WHERE subscriber_id = @dup;
DELETE FROM wp_fc_subscribers        WHERE id = @dup;

-- 3. Give the kept contact the duplicate's email (hash = md5(email), as FluentCRM does on save).
--    Bookings and booking emails are found by email in FluentBooking, so they now show on this contact.
UPDATE wp_fc_subscribers SET email = @new_email, hash = MD5(@new_email), updated_at = NOW()
 WHERE id = @keep AND @new_email IS NOT NULL;

COMMIT;

-- OPTIONAL (only if these tables exist; ignore "table doesn't exist" errors):
-- UPDATE wp_fc_event_tracking          SET subscriber_id = @keep WHERE subscriber_id = @dup;
-- UPDATE IGNORE wp_fc_contact_relations SET subscriber_id = @keep WHERE subscriber_id = @dup;
-- UPDATE wp_fc_contact_relation_items  SET subscriber_id = @keep WHERE subscriber_id = @dup;

-- Check: the duplicate should be gone and everything on @keep
SELECT id, email FROM wp_fc_subscribers WHERE id IN (@keep, @dup);
SELECT 'emails' t, COUNT(*) FROM wp_fc_campaign_emails WHERE subscriber_id = @keep
UNION ALL SELECT 'notes', COUNT(*) FROM wp_fc_subscriber_notes WHERE subscriber_id = @keep
UNION ALL SELECT 'tags/lists', COUNT(*) FROM wp_fc_subscriber_pivot WHERE subscriber_id = @keep
UNION ALL SELECT 'bookings (by email)', COUNT(*) FROM wp_fcal_bookings WHERE email = @new_email;
