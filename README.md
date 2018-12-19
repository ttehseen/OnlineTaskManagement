# Online Task Sourcing

Collaborators:
1. Lee Jun Han, Bryan
2. Bark HyeHyeon
3. Tehseen Taha
4. Tan Ping Min, Jasmine

## Web Server, Server Page Language, DBMS
We used the Bitnami MAPP stack, PostgreSQL, Apache and PHP. Clone the repo and access our application at http://localhost:8080/cs2102/login.php
The application starts with the login page at login.php. Also, do make sure that you change the Postgres dbname and password to your credentials in the php files before using the application.
## Entity Relationship Diagram for Task Sourcing
Idher daalna hai


## SQL codes
### SQL DDL code for relational schema
```SQL
CREATE TABLE users (
user_id SERIAL PRIMARY KEY,
user_name VARCHAR(64) NOT NULL,
user_password VARCHAR(64) NOT NULL,
user_email VARCHAR(64) NOT NULL,
is_admin BOOLEAN NOT NULL DEFAULT FALSE,
CHECK (user_email LIKE "%@%.%"),
CHECK (user_name NOT LIKE "%[^A-Z0-9*]%" AND LEN(user_name)>=5),
CHECK (user_password LIKE "%[0-9]%" AND user_password LIKE "%[A-Z]%"
AND user_password LIKE "%[!@#$%a^&*()-_+=.,;:`~]%" AND
LEN(user_password)>=8);

CREATE TABLE tasks (
task_id SERIAL PRIMARY KEY,
owner_id INTEGER NOT NULL,
due_date DATE NOT NULL,
due_time TIME NOT NULL,
description VARCHAR(256) NOT NULL,
FOREIGN KEY owner_id REFERENCES users(user_id) ON DELETE CASCADE),
CHECK (due_date >= GetDate());

CREATE TABLE bids (
bid_id SERIAL PRIMARY KEY,
bidder_id INTEGER NOT NULL,
task_id INTEGER NOT NULL,
amount INTEGER NOT NULL,
FOREIGN KEY bidder_id REFERENCES users(user_id) ON DELETE CASCADE,
FOREIGN KEY task_id REFERENCES task(task_id) ON DELETE CASCADE)
CHECK (amount < 100000);

CREATE TABLE is_picked_for (
task_id INTEGER PRIMARY KEY,
bid_id INTEGER NOT NULL,
FOREIGN KEY task_id REFERENCES task(task_id) ON DELETE CASCADE,
FOREIGN KEY bid_id REFERENCES bid(bid_id) ON DELETE CASCADE);
```

### SQL code for functionalities
#### Log In
- To find user information from users table based on user name
``` SQL
SELECT user_id, user_name, is_admin FROM users WHERE user_name =
'$_POST[username]'
```
- To find user information from users table based on user name and password
``` SQL
SELECT user_id, user_name FROM users WHERE user_password =
'$_POST[password]' AND user_name = '$_POST[username]'
```
#### Register
- To check if user name and user email already exist in users table
``` SQL
SELECT user_name FROM users WHERE user_name = '$_POST[username]'
SELECT user_email FROM users WHERE user_email = '$_POST[email]'
```
- To register user by inserting necessary information in users table
``` SQL
INSERT INTO users (user_name ,user_password,user_email) VALUES
('$_POST[username]','$_POST[password]','$_POST[email]')
```
#### All Tasks
- To display all available tasks that are not assigned yet
``` SQL
SELECT t.description, t.due_date, t.due_time, u.user_name FROM tasks t,
users u WHERE t.owner_id = u.user_id AND t.task_id NOT IN (SELECT
p.task_id FROM is_picked_for p) AND t.owner_id <> $userid ORDER BY
t.due_date
``` SQL
- To search for tasks with task description
``` SQL
SELECT t.task_id, t.description, t.due_date, t.due_time, u.user_name
FROM tasks t, users u WHERE t.owner_id = u.user_id AND
t.task_id
NOT IN (SELECT p.task_id FROM is_picked_for p) AND t.owner_id
<> $userid AND t.description LIKE '%$_POST[search_bar]%' ORDER BY
t.due_date
```
- To bid for a certain task
```SQL
SELECT t.task_id FROM tasks t, users u WHERE t.owner_id = u.user_id AND
t.task_id NOT IN (SELECT p.task_id FROM is_picked_for p) AND t.owner_id <>
$userid ORDER BY t.due_date LIMIT 1 OFFSET $rownumber;
INSERT INTO bids(bidder_id, task_id, amount) VALUES ($userid, $taskno,
$amount)
```
#### To Do
- To display the tasks that the user is assigned to do
``` SQL 
SELECT u.user_name, t.due_date, t.due_time, t.description FROM
is_picked_for p, tasks t, users u, bids b WHERE b.bidder_id = $userid and
p.bid_id = b.bid_id and p.task_id = t.task_id and t.owner_id = u.user_id
```
- To display the tasks that the user has bidded for and pending for assignment
```SQL
SELECT u.user_name, t.description, t.due_date, t.due_time, b.amount FROM
bids b, tasks t, users u WHERE t.owner_id = u.user_id and b.task_id =
t.task_id and b.bidder_id = $userid and t.task_id NOT IN (SELECT p.task_id
FROM is_picked_for p) ORDER BY t.due_date
```
- To delete the tasks that are done by the user
```SQL
SELECT t.task_id FROM is_picked_for p, tasks t, users u, bids b WHERE
b.bidder_id = $userid and p.bid_id = b.bid_id and p.task_id = t.task_id
and t.owner_id = u.user_id ORDER BY t.due_date LIMIT 1 OFFSET $rownumber
DELETE FROM tasks WHERE task_id = $done_with
```
#### Assign Task
- To display bids for the task that user owns and did not assigned yet
``` SQL
SELECT u.user_name, t.description, t.due_date, t.due_time, b.amount FROM
bids b, tasks t, users u WHERE b.bidder_id = u.user_id and b.task_id =
t.task_id and t.owner_id = $userid and t.task_id NOT IN (SELECT p.task_id
FROM is_picked_for p)
```
- To pick a certain bid to assign the task
``` SQL
SELECT t.task_id, b.bid_id FROM bids b, tasks t, users u WHERE b.bidder_id
= u.user_id and b.task_id = t.task_id and t.owner_id = $userid and
t.task_id NOT IN (SELECT p.task_id FROM is_picked_for p) ORDER BY user_id
LIMIT 1 OFFSET $rownumber
```

#### Create Task
- To create a new task
``` SQL
INSERT INTO tasks (owner_id, due_date, due_time, description) VALUES
($userid, '$_POST[due_date]','$_POST[due_time]', '$_POST[description]')
```
#### My Tasks
- To display tasks that the user owns but has not assigned yet.
``` SQL
SELECT t.description, t.due_date, t.due_time FROM users o JOIN tasks t on
o.user_id = t.owner_id WHERE t.task_id NOT IN (SELECT task_id FROM
is_picked_for) AND o.user_id = $userid ORDER BY t.due_date")
```
- To display tasks that the user owns and has assigned
``` SQL
SELECT t.description, bd.user_name as bidder, t.due_date, t.due_time FROM
users o JOIN tasks t on o.user_id = t.owner_id JOIN is_picked_for i on
i.task_id = t.task_id JOIN bids b on b.bid_id = i.bid_id JOIN users bd on
b.bidder_id = bd.user_id WHERE o.user_id = $userid ORDER BY t.due_date
```
- To delete a task that the user owns
```SQL
SELECT t.task_id FROM tasks t, users u WHERE t.owner_id = u.user_id AND
t.owner_id = $userid ORDER BY t.due_date LIMIT 1 OFFSET $rownumber
DELETE FROM tasks WHERE task_id = $taskno
```
#### Assertions
- users table
``` SQL
CHECK (user_email LIKE "%@%.%"),
CHECK (user_name NOT LIKE "%[^A-Z0-9*]%" AND LEN(user_name)>=5),
CHECK (user_password LIKE "%[0-9]%" AND user_password LIKE "%[A-Z]%"
AND user_password LIKE "%[!@#$%a^&*()-_+=.,;:`~]%" AND
LEN(user_password)>=8);
```
We check that the email provided is in a proper email format. We also check that the
username only consists of lowercase alphabet and that the username is of at least length 5.
We also check that the password the user creates contains at least one number, uppercase
letter and special character, as per secure password standard, as well as check that the
password is of at least length 8.
- tasks table
``` SQL 
CHECK (due_date >= GetDate());
```
We check that the due date of the created task is not earlier than today’s date.
- bids table
``` SQL 
CHECK (amount < 100000);
```
We check that the amount submitted for the bid is not too extravagant.

#### Screenshots
- View all tasks available to user to bid for
![alt text](https://github.com/ttehseen/OnlineTaskManagement/blob/master/screenshots/1.png)
- View all tasks created by the user 
![alt text](https://github.com/ttehseen/OnlineTaskManagement/blob/master/screenshots/1.png)

