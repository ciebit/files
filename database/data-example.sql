INSERT INTO `cb_files` (
    `id`,
    `name`,
    `description`,
    `url`,
    `size`,
    `views`,
    `mimetype`,
    `datetime`,
    `metadata`,
    `status`
) VALUES (
    1,
    'Title File 1',
    'Description File 1',
    'url-file-1.jpg',
    10,
    0,
    'image/jpg',
    '2018-05-26 10:33:22',
    '{"width": 600, "height": 150}',
    3
),(
    2,
    'Title File 2',
    'Description File 2',
    'url-file-2.jpg',
    20,
    1205,
    'image/jpeg',
    '2017-10-15 10:35:22',
    '{"width": 900, "height": 500}',
    2
),(
    3,
    'Title File 3',
    'Description File 3',
    'url-file-3.pdf',
    30,
    100000,
    'application/pdf',
    '2017-08-11 14:50:00',
    '{}',
    3
),(
    '4',
    'Title File 4',
    'Description File 4',
    'url-file-4.jpg',
    40,
    5058,
    'application/json',
    '2017-01-19 15:00:15',
    '{}',
    4
);
