# Legal and Real email thread messaging pdf

This branch contains the implementation of real time email messaging in a thread and then printing of the thread as pdf.

Follow these Steps:


##Local setup

1- git clone

2- Run composer install

3- Give the input file in `email-simulation/app/Console/Commands/SendLegalEmails.php` Set the value of `INPUT_DOCX_FILE_PATH'

4- Run php artisan serve

5- Access the url in browser `http://127.0.0.1:8000/auth/gmail` (Send me email address to add as tester in developer app of Gmail).

6- After successfull athentication , Run the Command  `php artisan legalpdf:send-thread`

7- Now , Search the Thread from Already opened browser form. 

8- Click on Generate PDF .  It will read the thread and generate the pdf.

9- Done


