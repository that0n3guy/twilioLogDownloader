This allows you to download twilio logs to an excel file.  This uses a laravel artisan command with progress bar.

You're required to specify the logType (sms is only supported at the moment) and the "after" date (in Y-m-d format).   

You can optionally specify a "before" date as well. These 2 dates create a date range.  If "before" date is not set, todays date is used. 

### Examples:
After and before dates basically allow you to specify a date range.  For example, if you want to download all logs AFTER July 5, 2019.  You would do:
`php artisan twilio:downloadlogs sms 2019-07-05`

If you wanted to download only the month of July in 2019, you would do:
`php artisan twilio:downloadlogs sms 2019-07-01 --before 2019-08-01`

## Steps to get this going

* Clone the repo
* install composer packages: `composer install`
* set 2 env variables:
```
TWILIO_SID=
TWILIO_TOKEN=
```
* run the command (see examples above)


## warning
### The twilio client tends to dislike Windows because of: 
https://support.twilio.com/hc/en-us/articles/235279367-Twilio-PHP-helper-library-SSL-certificate-problem-on-Windows

Solution: Use ubuntu on windows (WSL) just for running the commands.

### unset database env vars
This doens't use a database, so laravel will complain if you have any database stuff set in your .env file.  See the example .env file. 

### pulls to memory, then dumps to excel
If you have a super large dataset, this could fail because of memory issues (I think... I'm not for sure on this).   I've not tested that.   I have 16gb memory on my local and have easily pulled ~80k rows pretty easily.

## Some useful links
https://support.twilio.com/hc/en-us/articles/223183588-Exporting-SMS-and-Call-Logs
https://www.twilio.com/docs/voice/tutorials/how-to-retrieve-call-logs-php