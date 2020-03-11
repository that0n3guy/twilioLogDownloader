<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Twilio\Rest\Client as twilioClient;
use App\Exports\SmsLogsExport;


class DownloadLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:downloadlogs {logType} {after} {--B|before=}';

    protected $client;

    protected $beforeOption;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download ALL of the twilio logs into an excel file.  You\'re required to specify the logType (sms is supported) and the "after" date (in Y-m-d format like so: php artisan twilio:downloadlogs sms 2019-01-29) and optionally 
                            the "before" date like so: php artisan twilio:downloadlogs sms 2019-01-29 --before 2020-03-29 ... These 2 dates create a date range.  If "before" date is not set, today is used. ';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $sid = env('TWILIO_SID', "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");
        $token = env('TWILIO_TOKEN', "your_auth_token");
        $this->client = new twilioClient($sid, $token);
        parent::__construct();
    }

    /**
     * Set the date to today unless overwritten.
     */
    public function setBeforeOption ($before) {
        //@todo add validation to make sure that $this->before is after $this->after
        $this->beforeOption = date('Y-m-d'); // set the date today
        if ($before){
            $this->beforeOption = $before;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setBeforeOption($this->option('before'));

        if ($this->argument('logType') == 'sms'){
            $this->output->title('Export Started');

            Excel::store(new SmsLogsExport($this->client,  $this->output, $this->argument('after'), $this->beforeOption), 'twilioLogs'. date('Y-m-d H_i_s') .'.xlsx');
            // another way of doing it: (new LogsExport($this->client, $this->output, $this->argument('after'), $this->beforeOption))->store('twilioLogs'. date('Y-m-d H_i_s') .'.xlsx');
            $this->output->success('Export successful');
        } else {
            $this->error('Only sms log type is supported so far.');

        }
       


    }
}
