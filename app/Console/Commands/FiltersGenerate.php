<?php

namespace App\Console\Commands;

use App\Services\Shop\FiltersGeneratorService;
use App\Services\Shop\GoodsStorage;
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
     * @var GoodsStorage
     */
    protected $goodsStorage;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GoodsStorage $goodsStorage)
    {
        parent::__construct();
        $this->goodsStorage = $goodsStorage;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $FilterGenerateService = new FiltersGeneratorService($this->goodsStorage);
        $FilterGenerateService->generateForSection(2);
    }
}
