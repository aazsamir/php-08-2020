<?php

namespace App\Library;

use External\Baz\Movies\MovieService;

class BazTitles extends SystemTitles
{
    protected function getTitles()
    {
        $titles = [];
        try {
            $system = new MovieService();
            $raw = $system->getTitles();
            if (isset($raw['titles']) && is_iterable($raw['titles'])) {
                $titles = $raw['titles'];
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $titles;
    }
}
