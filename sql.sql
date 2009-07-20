---------------------------------------------------------------------------------------------------
--                                       DATABASE CREATION                                              --
---------------------------------------------------------------------------------------------------

-- CREATE DATABASE --
CREATE DATABASE `ryzom_api` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

---------------------------------------------------------------------------------------------------
--                                         GUILDS LIST TABLE                                                --
---------------------------------------------------------------------------------------------------

-- CREATE GUILDS LIST TABLE --
CREATE TABLE `ryzom_api`.`guilds_list` (
`gid` INT NOT NULL ,
`shardid` VARCHAR( 3 ) NOT NULL ,
`name` VARCHAR(120) NOT NULL ,
`race` VARCHAR(10) NOT NULL ,
`icon` VARCHAR(50) NOT NULL ,
`creation_date` DOUBLE NOT NULL ,
`description` TEXT NOT NULL ,
`deleted` BOOLEAN NOT NULL ,
`deletion_date` DOUBLE NOT NULL ,
UNIQUE (
`gid`
)
);

---------------------------------------------------------------------------------------------------
--                                      CHARACTER TABLES                                                   --
---------------------------------------------------------------------------------------------------

-- CREATE SINGLE CHARACTER TABLE --
CREATE TABLE `ryzom_api`.`character_history` (
`cid` INT NOT NULL ,
`uid` INT NOT NULL ,
`slot` INT NOT NULL ,
`shard` VARCHAR(10) NOT NULL ,
`race` VARCHAR(10) NOT NULL ,
`gender` VARCHAR(1) NOT NULL ,
`apikey` VARCHAR( 30 ) NOT NULL ,
`date` DOUBLE NOT NULL ,
UNIQUE (
`cid`
)
);

-- CREATE SINGLE CHARACTER INFO TABLE --
CREATE TABLE `ryzom_api`.`character_info_history` (
`cid` INT NOT NULL ,
`name` VARCHAR(16) NOT NULL ,
`titleid` VARCHAR(75) NOT NULL ,
`played_time` DOUBLE NOT NULL ,
`money` DOUBLE NOT NULL ,
`cult` VARCHAR(10) NOT NULL ,
`civ` VARCHAR(10) NOT NULL ,
`building` VARCHAR(15) NOT NULL ,
`guild_gid` VARCHAR(15) NOT NULL ,
`sha1` VARCHAR(40) NOT NULL ,
`date` DOUBLE NOT NULL 
);

-- CREATE SINGLE CHARACTER PHYS CARACS TABLE --
CREATE TABLE `ryzom_api`.`character_phys_caracs_history` (
`cid` INT NOT NULL ,
`constitution` INT NOT NULL ,
`metabolism` INT NOT NULL ,
`intelligence` INT NOT NULL ,
`wisdom` INT NOT NULL ,
`strength` INT NOT NULL ,
`wellbalanced` INT NOT NULL ,
`dexterity` INT NOT NULL ,
`will` INT NOT NULL ,
`sha1` VARCHAR(40) NOT NULL ,
`date` DOUBLE NOT NULL 
);

-- CREATE SINGLE CHARACTER PHYS SCORES TABLE --
CREATE TABLE `ryzom_api`.`character_phys_scores_history` (
`cid` INT NOT NULL ,
`hitpoints` INT NOT NULL ,
`hitpoints_max` INT NOT NULL ,
`stamina` INT NOT NULL ,
`stamina_max` INT NOT NULL ,
`sap` INT NOT NULL ,
`sap_max` INT NOT NULL ,
`focus` INT NOT NULL ,
`focus_max` INT NOT NULL ,
`sha1` VARCHAR(40) NOT NULL ,
`date` DOUBLE NOT NULL 
);

-- CREATE SINGLE CHARACTER EQUIPHANDS TABLE --
CREATE TABLE `ryzom_api`.`character_equiphands_history` (
`cid` INT NOT NULL ,
`equipments_feet` VARCHAR(75) NOT NULL , -- Note: sheet#c#q (e.g. ictahb_3.sitem#0#250)
`equipments_hands` VARCHAR(75) NOT NULL ,
`equipments_legs` VARCHAR(75) NOT NULL ,
`equipments_arms` VARCHAR(75) NOT NULL ,
`equipments_chest` VARCHAR(75) NOT NULL ,
`equipments_ankle_l` VARCHAR(75) NOT NULL ,
`equipments_ankle_r` VARCHAR(75) NOT NULL ,
`equipments_wrist_l` VARCHAR(75) NOT NULL ,
`equipments_wrist_r` VARCHAR(75) NOT NULL ,
`equipments_head_dress` VARCHAR(75) NOT NULL ,
`equipments_necklace` VARCHAR(75) NOT NULL ,
`equipments_finger_l` VARCHAR(75) NOT NULL ,
`equipments_finger_r` VARCHAR(75) NOT NULL ,
`equipments_ear_l` VARCHAR(75) NOT NULL ,
`equipments_ear_r` VARCHAR(75) NOT NULL ,
`hands_left` VARCHAR(75) NOT NULL , -- Note: sheet#q#sap
`hands_right` VARCHAR(75) NOT NULL ,
`sha1` VARCHAR(40) NOT NULL ,
`date` DOUBLE NOT NULL 
);

-- CREATE SINGLE CHARACTER FACTION POINTS TABLE --
CREATE TABLE `ryzom_api`.`character_faction_points_history` (
`cid` INT NOT NULL ,
`kami` INT NOT NULL ,
`karavan` INT NOT NULL ,
`fyros` INT NOT NULL ,
`matis` INT NOT NULL ,
`tryker` INT NOT NULL ,
`zorai` INT NOT NULL ,
`sha1` VARCHAR(40) NOT NULL ,
`date` DOUBLE NOT NULL 
);

-- CREATE SINGLE CHARACTER FAMES TABLE --
CREATE TABLE `ryzom_api`.`character_fames_history` (
`cid` INT NOT NULL ,
`faction` VARCHAR(50) NOT NULL ,
`value` INT NOT NULL ,
`date` DOUBLE NOT NULL 
);

-- CREATE SINGLE CHARACTER SKILLS TABLE --
CREATE TABLE `ryzom_api`.`character_skills_history` (
`cid` INT NOT NULL ,
`skill` VARCHAR(10) NOT NULL ,
`value` INT NOT NULL ,
`date` DOUBLE NOT NULL 
);

-- CREATE SINGLE CHARACTER PETS TABLE --
CREATE TABLE `ryzom_api`.`character_pets_history` (
`cid` INT NOT NULL ,
`pet0_sheet` VARCHAR(75) NOT NULL ,
`pet0_price` DOUBLE NOT NULL ,
`pet0_satiety` INT NOT NULL ,
`pet0_status` VARCHAR(50) NOT NULL ,
`pet0_stable` VARCHAR(50) NOT NULL ,
`pet1_sheet` VARCHAR(75) NOT NULL ,
`pet1_price` DOUBLE NOT NULL ,
`pet1_satiety` INT NOT NULL ,
`pet1_status` VARCHAR(50) NOT NULL ,
`pet1_stable` VARCHAR(50) NOT NULL ,
`pet2_sheet` VARCHAR(75) NOT NULL ,
`pet2_price` DOUBLE NOT NULL ,
`pet2_satiety` INT NOT NULL ,
`pet2_status` VARCHAR(50) NOT NULL ,
`pet2_stable` VARCHAR(50) NOT NULL ,
`pet3_sheet` VARCHAR(75) NOT NULL ,
`pet3_price` DOUBLE NOT NULL ,
`pet3_satiety` INT NOT NULL ,
`pet3_status` VARCHAR(50) NOT NULL ,
`pet3_stable` VARCHAR(50) NOT NULL ,
`sha1` VARCHAR(40) NOT NULL ,
`date` DOUBLE NOT NULL 
);

---------------------------------------------------------------------------------------------------
--                                            GUILD TABLES                                                       --
---------------------------------------------------------------------------------------------------

