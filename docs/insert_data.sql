INSERT INTO contacts (id, name, email, message, created_at) VALUES
(1, 'Green Tea', 'user1@test.com', 'Test Message', '2025-07-01 05:41:11'),
(2, 'Green Tea', 'user1@test.com', 'rgsdfgzsdgsdg', '2025-07-02 06:00:13'),
(3, 'お茶', 'khatrihemant100@gmail.com', 'This is my TEst Message', '2025-07-03 04:38:23'),
(4, 'お茶', 'khatrihemant100@gmail.com', 'This is my TEst Message', '2025-07-03 04:41:32'),
(5, 'お茶', 'khatrihemant100@gmail.com', 'This is my TEst Message', '2025-07-03 04:41:36'),
(6, 'Green Tea', 'khatrihemant100@gmail.com', 'test', '2025-07-03 04:41:53'),
(7, 'Green Tea', 'khatrihemant100@gmail.com', 'test', '2025-07-03 04:42:16'),
(8, 'お茶', 'khatrihemant100@gmail.com', 'adfad', '2025-07-03 04:42:33'),
(9, 'wertre', 'khatrihemant100@gmail.com', 'twgrewgtre', '2025-07-04 02:58:36');


INSERT INTO rooms (id, user_id, title, location, price, type, description, image, created_at) VALUES
(1, 2, 'tesyt', 'tesyt', 5000, 'single', 'tesy', 'uploads/room_1751519829_7114.png', '2025-07-03 05:17:09'),
(2, 2, 'tesyt', 'tesyt', 5000, 'single', 'tesy', 'uploads/room_1751519881_8073.png', '2025-07-03 05:18:01'),
(3, 2, 'asdf', 'adf', 3434, 'double', 'adfadf', 'uploads/room_1751519897_3967.png', '2025-07-03 05:18:17'),
(4, 2, 'asdf', 'adf', 3434, 'double', 'adfadf', 'uploads/room_1751519903_2191.png', '2025-07-03 05:18:23'),
(5, 2, 'test', 'yokohama', 30000, 'double', 'test', 'uploads/room_1751519941_3775.gif', '2025-07-03 05:19:01'),
(6, 2, 'HEMANT', 'YOKOHAMA', 20000, 'hostel', 'This is just check for the details', 'uploads/room_1751520370_1112.png', '2025-07-03 05:26:10'),
(7, 2, 'Test', 'Yokohma', 5000, 'double', 'This is ONly Test Message; ', 'uploads/room_1751590339_7177.png', '2025-07-04 00:52:19'),
(8, 2, 'Aakash', 'Yokohama', 50000, 'double', 'TEst', 'uploads/room_1751590436_3566.jpg', '2025-07-04 00:53:56'),
(9, 2, 'Check', 'yokohama', 1500, 'double', 'This is The test message for the room', 'uploads/room_1751592795_4237.png', '2025-07-04 01:33:15'),
(10, 2, 'chekc', 'yokohama', 10000, 'flat', 'test', 'uploads/room_1751593419_1451.png', '2025-07-04 01:43:39'),
(11, 2, 'Single', 'YOkohama', 100000, 'flat', 'This is only Test Message', 'uploads/room_1751594711_3028.png', '2025-07-04 02:05:11'),
(12, 1, 'Check', 'Check', 0, 'double', 'check', 'uploads/room_1751596166_6451.png', '2025-07-04 02:29:26'),
(13, 1, 'MIN TU', 'JAPAN', 1000, 'hostel', 'GAY', 'uploads/room_1751597866_3224.png', '2025-07-04 02:57:46'),
(15, 2, 'Aakash romm', 'Yokohama, Japan', 5000, 'double', 'TEST', 'uploads/room_1751848335_5656.png', '2025-07-07 00:32:15');


INSERT INTO users (id, name, email, password, role) VALUES
(1, 'Hemant Khatri', 'user1@test.com', '$2y$10$fIq.BHIa4inDnTRf7KTA5eDRHbW5ta7i6pB/J7oR97Y1OAMj/51iy', ''),
(2, 'Hemant Khatri', 'khatrihemant100@gmail.com', '$2y$10$9FrqqV/PX2ahp.o7WRo9aehiI40OzDaIxhuZCTfaxq1BqeEs11ipq', ''),
(5, 'Green Tea', 'kchemant073@gmail.com', '$2y$10$9pF.gQEYUnNzxLxL4HyZYer3X5TiiaRp73iQkmDepqRzNNGOFATqy', ''),
(11, 'Hemant Khatri', 'same@gmail.com', '$2y$10$7USujRDb3A6St6LdN0EqSeDq1kl5Z.ft4cIYYMZDBLXZA2oNrrNsW', ''),
(12, 'TEST', 'nepal@gmail.com', '$2y$10$z./azdRtrM8ynfw9XTMBkeFTJtEr2sSiiMhrYdU1weFjzmZ8jXGL6', '');