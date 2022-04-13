<?php

namespace Bakgul\Evaluator\Tests;

use Bakgul\Kernel\Helpers\Path;
use Bakgul\Kernel\Helpers\Text;
use Bakgul\Kernel\Tests\Services\TestDataService;
use Bakgul\Kernel\Tests\TestCase;
use Bakgul\FileContent\Tasks\CompleteFolders;
use Illuminate\Filesystem\Filesystem;

class EvaluatorTestMethods extends TestCase
{
    public $requests;
    
    public function __construct()
    {
        $this->requests = [
            'file' => TestDataService::createFileCommandBase(),
            'resource' => TestDataService::createResourceCommandBase(),
            'relation' => TestDataService::createRelationCommandBase(),
            'package' => TestDataService::createPackageCommandBase(),
        ];

        parent::__construct();
    }

    protected function makeFakePackage(string $path = '')
    {
        $this->emptyBase();

        mkdir(base_path(Text::prepend($path) . 'src'));
        file_put_contents(Path::base(array_filter([$path, 'src', 'TestingServiceProvider.php'])), '');
    }

    protected function emptyBase()
    {
        (new Filesystem)->deleteDirectories(base_path());
    }

    protected function createModels(array $packages)
    {
        (new Filesystem)->deleteDirectories(base_path());

        foreach ($packages as $package) {
            $path = base_path($package[0] . DIRECTORY_SEPARATOR . "Models");

            CompleteFolders::_($path, false);

            file_put_contents($path . DIRECTORY_SEPARATOR . "{$package[1]}.php", '');
        }
    }
}