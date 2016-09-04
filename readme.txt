 Web Services By Sowan





TNB AS Web Services

August 22, 2016
 

In order to run the AS WEB SERVICE Please do the following changes:

In this section we will define the path for WSDL path this done by change the IP for WSDL Path from file Called parm.php

  <YOUR-IP>/RemoteServices/config/parm.php
$GLOBALS['WSDL']="<YOUR–IP>/RemoteServices/services.php?wsdl";

How to use AS WEB SERVICE to create, suspend, reset
In this section we have the following action in file called users.php
•	create ,create new user
•	suspend, suspend user
•	resetPwd , reset password
Note: Each action will take 2 parameter one for username and second is the type of action we will execute.
Create User: from the web browser call this URL with following parameter to create new user with auto generated password
  <YOUR-IP>/RemoteServices/users.php?username=USERNAME&action=create
Suspend User: from the web browser call this URL with following parameter to suspend user
  <YOUR-IP>/RemoteServices/users.php?username=USERNAME&action=suspend
resetPwd User: from the web browser call this URL with following parameter to reset login password for the user
  <YOUR-IP>/RemoteServices/users.php?username=USERNAME&action= resetPwd
How to use AS WEB SERVICE to authenticate users.
We have file called login.php that used to check credential for accounts and check if username and passowrd is correct and If is the first time login and if account is locked or not and return proper message for every case.
The login.php take 2 parameter:
Username
Password
Example:
  <YOUR-IP>/RemoteServices/login.php?username= USERNAME &password=PASSWORD

How to use AS WEB SERVICE to change user password.
We have file called changepwd.php that used to change password for accounts it check if username and old password is correct then will accept the new password and save it to the database.
The login.php take 3 parameter:
Username
Password
Newpassword
 Example:
  <YOUR-IP>/RemoteServices/ changepwd.php?username=USERNAME&password=PASSWORD&newpassword=NEWPASSWORD

How to use AS WEB SERVICE to forget user password.
We have file called forgetpwd.php that used to request new password for user this action will reset the username if account is locked and generate new password and send SMS with new password to the user mobile.
The login.php one parameter:
Username
Example:
<pre>
  <YOUR-IP>/RemoteServices/forgetpwd.php?username=USERNAME
<pre>
How to use AS WEB SERVICE for Batch Users.
We have file called users.txt contains TCIB ID And mobile, the current path for the file in directory called /RemoteServices/crontab
  <YOUR-IP>/RemoteServices/crontab/users.txt

Example:
Username1,05990000001
Username2,05990000002
Username3,05990000003

