-- Migration for adding rabItemId to tbl_lpj_item
-- Date: 2025-12-18

ALTER TABLE `tbl_lpj_item`
  ADD COLUMN `rabItemId` INT(11) DEFAULT NULL COMMENT 'Original ID from tbl_rab' AFTER `lpjId`,
  ADD INDEX `idx_lpj_rab_item` (`lpjId`, `rabItemId`);
