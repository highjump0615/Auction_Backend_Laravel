<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

class TimeoutGiveupCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timeout:giveup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check timeout for determine give up';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dateNow = new DateTime();
        $strDate = $dateNow->format('Y-m-d H:i:s');

        $this->info("Giveup Cron is working: " . $strDate);
    }
}
