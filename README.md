
# Campus Jobs Project

A Laravel-based web application for managing timesheets, hour requests, and scheduling for visa-holding students. The application supports three user roles—**Admin**, **Recruiter**, and **Student**—and includes interactive calendar integration, audit logging for administrative overrides, and export functionality for reporting.

---

## Table of Contents

- [Using Campus Jobs](#using-campus-jobs)
- [Cloning the Project](#cloning-the-project)


## Using Campus Jobs

Campus Jobs streamlines the management of work hours for visa-holding students:

- **Admin:**  
  Review and override timesheets and hour requests, view audit logs, export reports, and access system notifications.

- **Recruiter:**  
  Assign jobs and submit hour requests via an interactive calendar interface.

- **Student:**  
  View assigned jobs, pending timesheets/hour requests, upcoming shifts, history, and notifications.

---

## Cloning the Project

Clone the project files to your XAMPP `htdocs` folder:

```
git clone https://github.com/ibzm/Campus-jobs.git && cd campus-jobs/
npm install
composer install
composer require phpoffice/phpspreadsheet

```
how to start the database
```
php artisan migrate
php artisan db:seed
```
Running the server
```
php artisan serve
npm run dev
```

Cache Issues:
If configurations seem outdated, run:

```
Copy
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

User Roles and Usage
Admin
Dashboard:
View all timesheets and hour requests along with summary statistics.

## Email Notifications:
If you want to receive email notifications, configure your mail settings in your .env.

```
dotenv

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=from@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Database Notifications:
Laravel automatically stores notifications in the notifications table when using the database channel. Ensure that the notifications migration is run (Laravel provides a migration for this if you use php artisan notifications:table and then migrate).


## Features:

Update timesheet statuses (with an optional override message).

Update hour request statuses, adjust requested hours, and provide a reason. Changes to approved hour requests adjust the student's remaining hours.

Export:
Export both timesheets and hour requests as Excel files.

Audit Logs:
Review a history of all administrative changes.

Notifications:
Access system notifications.

Recruiter
Dashboard:
Manage job assignments and submit hour requests.

Calendar Interface:
Use an interactive calendar (powered by FullCalendar) to request hours for students via a modal form.

Export:
Optionally export hour request data.

Student
Dashboard:
View assigned jobs, pending timesheets, pending hour requests, history, upcoming shifts, and notifications.

Processing:
Approve or reject submitted timesheets and hour requests.
