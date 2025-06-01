-- SQL script to fix existing bid statuses in the database
-- This will ensure all bids have the correct status based on task status

-- 1. First make sure the bids table has the correct enum values
ALTER TABLE bids MODIFY COLUMN status ENUM('pending','accepted','rejected','cancelled','done') DEFAULT 'pending';

-- 2. Fix bids for cancelled tasks (where executor_id is NULL but task was previously in_progress)
UPDATE bids b
JOIN tasks t ON b.task_id = t.task_id
SET b.status = 'cancelled'
WHERE t.status = 'posted' 
AND t.executor_id IS NULL
AND b.status = 'accepted'
AND EXISTS (
    SELECT 1 FROM task_cancellations tc 
    WHERE tc.task_id = b.task_id 
    AND tc.executor_id = b.executor_id
);

-- 3. Fix bids for completed tasks
UPDATE bids b
JOIN tasks t ON b.task_id = t.task_id
SET b.status = 'done'
WHERE t.status = 'completed'
AND t.executor_id = b.executor_id
AND b.status = 'accepted';

-- 4. Reset any other bids for completed tasks to rejected
UPDATE bids b
JOIN tasks t ON b.task_id = t.task_id
SET b.status = 'rejected'
WHERE t.status = 'completed'
AND t.executor_id != b.executor_id
AND b.status = 'pending';

-- 5. Make sure bids for in-progress tasks have correct statuses
UPDATE bids b
JOIN tasks t ON b.task_id = t.task_id
SET b.status = 'accepted'
WHERE t.status = 'in_progress'
AND t.executor_id = b.executor_id
AND b.status != 'accepted';

-- 6. Reset any other bids for in-progress tasks to pending
UPDATE bids b
JOIN tasks t ON b.task_id = t.task_id
SET b.status = 'pending'
WHERE t.status = 'in_progress'
AND t.executor_id != b.executor_id
AND b.status NOT IN ('pending', 'rejected');
