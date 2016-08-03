======

nashOJ

======

### Setting up nashOJ on a server.
Clone or Download zip and store it in your public html directory.

### Requirements

- PHP 7.0
- Mysql 2.0
- Java 1.7
- Python 3.0
- gcc 5.3.1
- Html >= 3.0
- css 3.0

### Steps -

- Unpack the zip file and paste the contents in your web directory.

- Open your browser and open “/install.php” and enter you Mysql
credentials.

- You will be asked to enter admin password.

- The website is up at this point but there is need to compile the java source
files.

- open “codejudge-compiler ” and browse through the classes.

- Goto – languages directory and compiler all the java files there.

- Go to parent directory of languages and then compile that.

- Finally run this command in the terminal - “Java -cp
/var/www/html/codejudge-compiler/src"
codejudge.compiler.CodejudgeCompiler”
To start the backend java server which will wait for requests.

This project is forked from https://github.com/sankha93/codejudge
