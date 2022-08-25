<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ShortUrlController;


class PushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:pushNotification';
    private $pushNotification;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Send a Daily email to all users who's tiny url expired today";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ShortUrlController $pushNotification)
    {
        parent::__construct();
        $this->pushNotification=$pushNotification;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // return 0;
        $this->pushNotification->sendEmailNotification();
    }
}
