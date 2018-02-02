<?php

namespace App\Console\Commands;

use App\Models\Shop\Sections;
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
    protected $signature = 'filters:generate {--section=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sections = $this->option('section');

        $goodsStorage = new GoodsStorage();
        $FilterGenerateService = new FiltersGeneratorService($goodsStorage);
        if (count($sections)) {
            foreach ($sections as $section_id) {
                $FilterGenerateService->generateForSection($section_id);
//                $FilterGenerateService->saveFiltersSchema($section_id);
//                $FilterGenerateService->saveGoodsByFilter($section_id);
            }
        } else {
            $sections = Sections::all();
            foreach($sections as $section){
                $FilterGenerateService->generateForSection($section->id);
//                $FilterGenerateService->saveFiltersSchema($section->id);
//                $FilterGenerateService->saveGoodsByFilter($section->id);

            }
        }
    }
}
