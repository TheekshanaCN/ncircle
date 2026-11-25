-- Migration: profile photos. Keeps all existing data.
ALTER TABLE users ADD COLUMN avatar VARCHAR(80) NULL AFTER bio;
