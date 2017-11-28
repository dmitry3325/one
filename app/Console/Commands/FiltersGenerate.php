<?php

namespace App\Console\Commands;

use App\Services\Shop\FiltersGeneratorService;
use Illuminate\Console\Command;

class FiltersGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filters:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $FilterGenerateService = new FiltersGeneratorService();
        $FilterGenerateService->generateForSection(2);
    }
}
