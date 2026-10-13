-- Merge FluentCRM contact @dup INTO contact @keep (moves all activity; @dup is deleted afterwards in FluentCRM).
-- 1) Take a full database backup first.  2) Replace the two IDs.  3) Replace "wp_" if your table prefix differs.
SET @keep = 0;   -- ID of the contact you KEEP (the old one with most activity)
SET @dup  = 0;   -- ID of the contact created by the booking (will be removed)

START TRANSACTION;

-- Email history, link clicks, automations, sequences, notes, SMS
UPDATE wp_fc_campaign_emails       SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_campaign_url_metrics  SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_funnel_metrics        SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE IGNORE wp_fc_funnel_subscribers SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_sequence_tracker      SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_subscriber_notes      SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_sms_messages          SET subscriber_id = @keep WHERE subscriber_id = @dup;
UPDATE wp_fc_message_threads       SET contact_id    = @keep WHERE contact_id    = @dup;

-- Tags and lists (skips ones the kept contact already has)
UPDATE IGNORE wp_fc_subscriber_pivot SET subscriber_id = @keep WHERE subscriber_id = @dup;

-- Custom field values: only those the kept contact doesn't have yet
UPDATE wp_fc_subscriber_meta SET subscriber_id = @keep
 WHERE subscriber_id = @dup
   AND `key` NOT IN (SELECT k FROM (SELECT `key` AS k FROM wp_fc_subscriber_meta WHERE subscriber_id = @keep) AS existing);

-- FluentBooking: bookings linked to the duplicate contact
UPDATE wp_fcal_bookings SET person_contact_id = @keep WHERE person_contact_id = @dup;

COMMIT;

-- OPTIONAL (only if these tables exist in your database; skip the line if you get "table doesn't exist"):
-- UPDATE wp_fc_event_tracking          SET subscriber_id = @keep WHERE subscriber_id = @dup;
-- UPDATE IGNORE wp_fc_contact_relations SET subscriber_id = @keep WHERE subscriber_id = @dup;
-- UPDATE wp_fc_contact_relation_items  SET subscriber_id = @keep WHERE subscriber_id = @dup;