# email-simulation
## Local setup
1- git clone 

2- Run `composer install`

3- Give the input file in `/app/Services/SimulateEmail.php` Set the value of `INPUT_DOCX_FILE_PATH`

4- Run `php artisan serve`

5- Access the url in browser `http://127.0.0.1:8000/generate-pdf`

6- It will read and generate the PDF file in `storage/private` folder

Done
