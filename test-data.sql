truncate permissions;
truncate cards;
truncate users;
truncate tools;

insert into tools (tool_id, name, status, node) values (10, 'Laser', 1, 1);
insert into tools (tool_id, name, status, node) values (20, 'Rage', 0, 2);     -- taken out of service
insert into tools (tool_id, name, status, node) values (30, 'Three in One', 1, 3);
insert into tools (tool_id, name, status, node) values (40, 'Four in One', 1, 4);

insert into users (id, nick) values (200, 'Foo');     -- id fields must match the website db ids - I've chosen these randomly
insert into users (id, nick) values (201, 'Bar');

insert into cards (user_id, card, added_by_card, added_on, last_used) values (200, 'AAAAAAAA', null, now(), null);
insert into cards (user_id, card, added_by_card, added_on, last_used) values (201, 'BBBBBBBB', 'AAAAAAAA', now(), null);

insert into permissions (tool_id, user_id, permission) values (10, 200, 1);         -- Foo has access to the Laser
insert into permissions (tool_id, user_id, permission) values (20, 200, 1);         -- Foo has access to the Rage, but it's out of service
insert into permissions (tool_id, user_id, permission) values (30, 200, 0);         -- Foo has NO access to the Three in One
insert into permissions (tool_id, user_id, permission) values (40, 200, 1);         -- Foo has access to the Four in One

insert into permissions (tool_id, user_id, permission) values (10, 201, 1);         -- Bar has access to the Laser
-- insert into permissions (tool_id, user_id, permission) values (20, 201, 0);         -- Bar has NO access to the Rage - by ommission
insert into permissions (tool_id, user_id, permission) values (30, 201, 1);         -- Bar has access to the Three in One
insert into permissions (tool_id, user_id, permission) values (40, 201, 1);         -- Bar has access to the Four in One
