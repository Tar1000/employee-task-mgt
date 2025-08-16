-- Sample data for Employee Task Management System

-- Sample Users
INSERT INTO users (name, email, password, role) VALUES
('Alice Johnson', 'alice@example.com', '$2y$12$K2FJAg6JUhLuQfdJkNEwrO2Jw6YbxOfeLyzjH326WYISLhGB/TfAq', 'user'),
('Bob Smith', 'bob@example.com', '$2y$12$K2FJAg6JUhLuQfdJkNEwrO2Jw6YbxOfeLyzjH326WYISLhGB/TfAq', 'user'),
('Charlie Brown', 'charlie@example.com', '$2y$12$K2FJAg6JUhLuQfdJkNEwrO2Jw6YbxOfeLyzjH326WYISLhGB/TfAq', 'user'),
('Diana Prince', 'diana@example.com', '$2y$12$K2FJAg6JUhLuQfdJkNEwrO2Jw6YbxOfeLyzjH326WYISLhGB/TfAq', 'user'),
('Eve Adams', 'eve@example.com', '$2y$12$K2FJAg6JUhLuQfdJkNEwrO2Jw6YbxOfeLyzjH326WYISLhGB/TfAq', 'user');

-- Sample Tasks
INSERT INTO tasks (title, description, status, creator_id, assignee_id) VALUES
('Prepare project proposal', 'Gather requirements and prepare proposal', 'pending', 1, 2),
('Develop login module', 'Implement authentication', 'in_progress', 1, 3),
('Create database schema', 'Design initial database tables', 'completed', 1, 2),
('Set up CI/CD pipeline', 'Configure continuous integration and deployment', 'on_hold', 1, 4),
('Design landing page', 'Create responsive landing page', 'pending', 1, 5),
('Write unit tests', 'Add tests for core features', 'in_progress', 1, 6),
('Fix bug #123', 'Resolve reported issue in production', 'completed', 1, 2),
('Update documentation', 'Refresh project documentation', 'pending', 1, 3),
('Optimize database queries', 'Improve query performance', 'in_progress', 1, 4),
('Refactor codebase', 'Clean up legacy code', 'on_hold', 1, 5),
('Prepare release notes', 'Summarize changes for release', 'completed', 1, 6),
('Conduct user training', 'Hold training session for users', 'pending', 1, 2),
('Implement caching', 'Add caching layer for faster responses', 'in_progress', 1, 3),
('Review pull requests', 'Review incoming PRs', 'completed', 1, 4),
('Set up monitoring', 'Establish system monitoring', 'pending', 1, 5),
('Update API endpoints', 'Modify endpoints for new requirements', 'in_progress', 1, 6),
('Migrate legacy data', 'Move old data to new system', 'cancelled', 1, 2),
('Research new tools', 'Investigate tools for productivity', 'completed', 1, 3),
('Plan sprint backlog', 'Organize tasks for next sprint', 'pending', 1, 4),
('Security audit', 'Perform security assessment', 'cancelled', 1, 5);

