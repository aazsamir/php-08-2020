<?php

namespace App\Library;

use External\Bar\Movies\MovieService;

class BarTitles extends SystemTitles
{
    protected function getTitles()
    {
        $titles = [];
        try {
            $system = new MovieService();
            $raw = $system->getTitles();
            if (isset($raw['titles']) && is_iterable($raw['titles'])) {
                foreach ($raw['titles'] as $data) {
                    if (isset($data['title'])) {
                        $titles[] = $data['title'];
                    }
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        return $titles;
    }
}
