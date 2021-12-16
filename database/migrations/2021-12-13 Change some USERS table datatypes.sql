ALTER TABLE `USERS` 
    CHANGE COLUMN `Login Count` `Login Count` INT NULL DEFAULT 0,
    CHANGE COLUMN `Fails Count` `Fails Count` INT NULL DEFAULT 0;

ALTER TABLE `USERS`
    CHANGE `Preferred Highlight Colour Lightness Value` `Preferred Highlight Colour Lightness Value` SMALLINT NOT NULL DEFAULT '127';