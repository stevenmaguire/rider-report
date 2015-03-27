<?php namespace App;

use Carbon\Carbon;

class Report
{
    private $total_count = 0;

    private $total_time = 0;

    private $total_distance = 0;

    private $average_time = 0;

    private $average_distance = 0;

    private $first_trip = null;

    private $last_trip = null;

    public function addUberTrip($trip)
    {
        $this->total_count++;
        $this->addTime($trip->end_time - $trip->start_time);
        $this->addDistance($trip->distance);
        $this->addDate($trip->start_time);
    }

    public function getAverageDistance()
    {
        return round($this->average_distance, 1);
    }

    public function getAverageTime()
    {
        return $this->humanReadableTime($this->average_time);
    }

    public function getFirstTrip()
    {
        return Carbon::createFromTimeStamp($this->first_trip);
    }

    public function getLastTrip()
    {
        return Carbon::createFromTimeStamp($this->last_trip);
    }

    public function getTimeSpan()
    {
        return $this->getLastTrip()->diffForHumans($this->getFirstTrip(), true);
    }

    public function getTripCount()
    {
        return $this->total_count;
    }

    public function getTotalDistance()
    {
        return round($this->total_distance, 1);
    }

    public function getTotalTime()
    {
        return $this->humanReadableTime($this->total_time);
    }

    private function addDate($date)
    {
        if (is_null($this->first_trip) && is_null($this->last_trip)) {
            $this->first_trip = $date;
            $this->last_trip = $date;
        }

        if ($date > $this->last_trip) {
            $this->last_trip = $date;
        }

        if ($date < $this->first_trip) {
            $this->first_trip = $date;
        }
    }

    private function addDistance($distance)
    {
        $this->total_distance = $this->total_distance + $distance;
        $this->average_distance = $this->total_distance / $this->total_count;
    }

    private function addTime($time)
    {
        $this->total_time = $this->total_time + $time;
        $this->average_time = $this->total_time / $this->total_count;
    }

    private function humanReadableTime($time)
    {
        $s = $time%60;
        $m = floor(($time%3600)/60);
        $h = floor(($time%86400)/3600);
        $d = floor(($time%2592000)/86400);
        $M = floor($time/2592000);

        return "$M months, $d days, $h hours, $m minutes, $s seconds";
    }
}
