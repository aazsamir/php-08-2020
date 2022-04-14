<?php

namespace App\Library;

use External\Foo\Movies\MovieService;

class FooTitles extends SystemTitles
{
    protected function getTitles()
    {
        $system = new MovieService();
        $raw = null;
        try{
            $raw = $system->getTitles();
        } catch (\Exception $e){
            return;
        }
        return $raw;
    }
}
