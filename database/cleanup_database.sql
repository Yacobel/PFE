-- Database Cleanup Script for Task Platform
-- This script removes unused tables and columns from the database

-- 1. Mark unused tables with comments
-- We're keeping task_assignments table for now but it's not actively used
-- Code has been updated to use the bids table with 'accepted' status instead

-- 2. Remove unused columns from existing tables

-- From tasks table, remove unused columns
ALTER TABLE `tasks` 
  DROP COLUMN IF EXISTS `payment_date`;

-- From users table, remove unused columns
ALTER TABLE `users`
  DROP COLUMN IF EXISTS `bio`;

-- 3. Update foreign key constraints
-- Remove constraints related to task_assignments table
-- These constraints are automatically removed when the table is dropped

-- 4. Update enum values to match actual usage
-- Ensure bid status values match what's used in the application
ALTER TABLE `bids` 
  MODIFY `status` enum('pending','accepted','rejected','cancelled','done') DEFAULT 'pending';

-- 5. Clean up unused indexes (if any)
-- No unused indexes identified

-- 6. Optimize tables after cleanup
OPTIMIZE TABLE `users`, `tasks`, `bids`, `categories`, `messages`, `payments`, `reviews`, `task_cancellations`;

-- Script completed
