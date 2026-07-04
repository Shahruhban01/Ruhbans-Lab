INSERT INTO roles (name, slug) VALUES
    ('Admin', 'admin'),
    ('Editor', 'editor'),
    ('Author', 'author'),
    ('Visitor', 'visitor')
ON DUPLICATE KEY UPDATE name = VALUES(name);
