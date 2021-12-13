ALTER TABLE `quran-os`.`USERS` 
    CHANGE COLUMN `Login Count` `Login Count` INT NULL DEFAULT 0,
    CHANGE COLUMN `Fails Count` `Fails Count` INT NULL DEFAULT 0;