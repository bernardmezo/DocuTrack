-- =============================================
-- Migration: Add rabItemId column to tbl_lpj_item
-- Date: 2025-12-16
-- Purpose: Link LPJ items to RAB items untuk tracking bukti upload
-- =============================================

-- Step 1: Add rabItemId column (nullable first untuk backward compatibility)
ALTER TABLE tbl_lpj_item 
ADD COLUMN rabItemId INT NULL 
AFTER lpjId;

-- Step 2: Add index for better performance
ALTER TABLE tbl_lpj_item 
ADD INDEX idx_lpj_rab (lpjId, rabItemId);

-- Step 3: Add foreign key constraint (optional, jika tbl_rab ada)
-- ALTER TABLE tbl_lpj_item 
-- ADD CONSTRAINT fk_lpj_item_rab 
-- FOREIGN KEY (rabItemId) REFERENCES tbl_rab(rabItemId) 
-- ON DELETE CASCADE ON UPDATE CASCADE;

-- Step 4: Verify changes
SHOW COLUMNS FROM tbl_lpj_item;
