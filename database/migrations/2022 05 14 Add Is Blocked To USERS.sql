ALTER TABLE `USERS`
ADD COLUMN `Is Blocked` TINYINT 
AFTER `Last Login Timestamp`

-- To Rollback if needed
-- ALTER TABLE `USERS` DROP COLUMN `Is Blocked`;