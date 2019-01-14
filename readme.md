
# Mini Aspire API

Mini Aspire API provides several tasks such as:

- Register new user & Login
- Create, get, update a loan
- Get list of loans
- Create repayment for a loan

## Installation

Follow Laravel installation [here](https://laravel.com/docs/5.7/installation).

Set up database by running this syntax in command prompt.
```bash
php artisan migrate
```

Test unit can be run all or by filter.
```bash
./vendor/bin/phpunit
./vendor/bin/phpunit --filter LoginTest
./vendor/bin/phpunit --filter LoanTest
```
## Tables

This API uses 3 tables to operate, Users Table, Loan Table and Repayments Table
### Users Table

Column Name | Data Type | Comment
--------------| ------------| --------------
id | increment |
name | string | 
email | string, unique |
email_verfied_at | timestamp, nullable|
password | string|
type | string | ex : Common, Admin

### Loans Table

Column Name | Data Type | Comment
--------------| ------------| --------------
id | increment |
user_id | integer| one-to-many with User
amount| double(8,2) | ex : 30000
duration | integer | ex : 1,2,6
repayment_freq | string | ex : Monthly
interest_rate | double(4,2) | ex : 2.5
arr_fee | double(4,2) | ex : 3
status | string | ex: Pending, Accepted, Completed, Rejected, Cancelled 

### Repayments Table

Column Name | Data Type | Comment
--------------| ------------| --------------
id | increment |
user_id | integer | one-to-many with User
loan_id | integer | one-to-many with Loan
amount | double(8,2) | ex : 30000

## API Functions

No | URL | Type |  Parameters
-----| ------------| -- |---------
1 | http://homestead.test/api/register | POST | name: John <br> email: john@example.com <br> password: john
2 | http://homestead.test/api/login | POST | email: john@example.com <br> password: john
3 | http://homestead.test/api/loans | GET
4 | http://homestead.test/api/loans/{loan} | GET | 
5 | http://homestead.test/api/loans | POST | amount: 30000 <br> duration: 3 <br> repayment_freq: Monthly <br> interest_rate:2 <br> arr_fee:1
6| http://homestead.test/api/loans/{loan} | PUT | status: Accepted <br> interest_rate: 2.5
7| http://homestead.test/api/repay/{loan}| POST | amount: 10000

## Assumption

- User must login to create loan, get loan, create repayment
- Default loan status = Pending
- User able to create repayment if loan status = Accepted
-  After loan fully repaid, function will set loan status = Completed
- User must repay : amount + (interest_rate/100 * duration * amount)

