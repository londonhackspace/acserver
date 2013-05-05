truncate toolusage;
truncate permissions;
truncate cards;
truncate users;
truncate acnodes;
truncate tools;

insert into tools (tool_id, name, status, status_message) values (10, 'Laser', 1, "OK");
insert into tools (tool_id, name, status, status_message) values (20, 'Rage', 0, "Guard Mechanism Broken");     -- taken out of service
insert into tools (tool_id, name, status, status_message) values (30, 'Three in One', 1, "OK");
insert into tools (tool_id, name, status, status_message) values (40, 'Four in One', 1, "OK");

insert into acnodes (
        acnode_id,
        unique_identifier,
        shared_secret,
        tool_id
   ) values (
       1,
       '90-A2-DA-00-F3-BC',                     -- Mac address
       'a2d6286e-addb-11e2-8044-000c2964575b',  -- Output of uuid()
       10
);
insert into acnodes (
       acnode_id,
       unique_identifier,
       shared_secret,
       tool_id
  ) values (
      2,
      '90-A2-DE-AD-BE-EF',                     -- Mac address
      'c687d46a-addb-11e2-8044-000c2964575b',  -- Output of uuid()
      20
);
insert into acnodes (
       acnode_id,
       unique_identifier,
       shared_secret,
       tool_id
  ) values (
      3,
      '90-A2-DE-AD-H0-0F',                     -- Mac address
      'c687d46a-addb-11e2-8044-000c2964575b',  -- Output of uuid()
      30
);
insert into acnodes (
       acnode_id,
       unique_identifier,
       shared_secret,
       tool_id
  ) values (
      4,
      '90-A2-DE-AD-DE-AD',                     -- Mac address
      'c687d46a-addb-11e2-8044-000c2964575b',  -- Output of uuid()
      40
);

insert into users (user_id, nick) values (100, 'Oskar');
insert into users (user_id, nick) values (200, 'Foo');     -- id fields must match the website db ids - I've chosen these randomly
insert into users (user_id, nick) values (201, 'Bar');
insert into users (user_id, nick) values (202, 'Nothing Yet Granted');

insert into cards (card_id, user_id, card_unique_identifier, last_used) values (101, 100, '00000001', now());       -- This card belongs to Oskar
insert into cards (card_id, user_id, card_unique_identifier, last_used) values (300, 200, 'AAAAAAAA', now());       -- This card belongs to Foo
insert into cards (card_id, user_id, card_unique_identifier, last_used) values (301, 200, 'BBBBBBBB', now());       -- This card also belongs to Foo
insert into cards (card_id, user_id, card_unique_identifier, last_used) values (302, 201, 'FFFFFFFF', now());       -- Belongs to Bar
insert into cards (card_id, user_id, card_unique_identifier, last_used) values (303, 202, 'BABABABA', now());       -- Belongs to Nothing Yet Granted

insert into permissions (tool_id, user_id, added_by_user_id, permission, added_on) values (10, 100, 100, 2, now());         -- Oskar has admin access to the Laser
insert into permissions (tool_id, user_id, added_by_user_id, permission, added_on) values (20, 100, 100, 2, now());         -- Oskar has admin access to the Rage, but it's out of service
insert into permissions (tool_id, user_id, added_by_user_id, permission, added_on) values (30, 100, 100, 1, now());         -- Oskar does not have admin access to the Three in One
                                                                                                                            -- Oskar does not have admin access to the Four in One

insert into permissions (tool_id, user_id, added_by_user_id, permission, added_on) values (10, 200, 100, 1, now());         -- Foo has access to the Laser
insert into permissions (tool_id, user_id, added_by_user_id, permission, added_on) values (20, 200, 100, 1, now());         -- Foo has access to the Rage, but it's out of service
insert into permissions (tool_id, user_id, added_by_user_id, permission, added_on) values (30, 200, 100, 2, now());         -- Foo has admin access to the Three in One
insert into permissions (tool_id, user_id, added_by_user_id, permission, added_on) values (40, 200, 100, 1, now());         -- Foo has access to the Four in One

insert into permissions (tool_id, user_id, added_by_user_id, permission, added_on) values (10, 201, 100, 1, now());         -- Bar has access to the Laser
insert into permissions (tool_id, user_id, added_by_user_id, permission, added_on) values (20, 201, 100, 1, now());         -- Bar has access to the Rage, but it's out of service
                                                                                                                            -- Bar has NO access to the Three in One
                                                                                                                            -- Bar has NO access to the Four in One
