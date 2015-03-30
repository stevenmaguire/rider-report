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
        $now = Carbon::now();
        $then = $now->copy()->addSeconds($this->average_time);

        return $this->getDiff($then, $now);
    }

    public function getFirstTrip($format = null)
    {
        $date = Carbon::createFromTimeStamp($this->first_trip);

        return $this->formatDate($date, $format);
    }

    public function getLastTrip($format = null)
    {
        $date = Carbon::createFromTimeStamp($this->last_trip);

        return $this->formatDate($date, $format);
    }

    public function getTimeSpan()
    {
        return $this->getDiff($this->getFirstTrip(), $this->getLastTrip());
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
        $now = Carbon::now();
        $then = $now->copy()->addSeconds($this->total_time);

        return $this->getDiff($then, $now);
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

    private function formatDate(Carbon $date, $format = null)
    {
        if ($format) {
            return $date->format($format);
        }

        return $date;
    }

    private function getDiff(Carbon $start, Carbon $end = null)
    {
        if (is_null($end)) {
            $end = Carbon::now();
        }

        return $end->diffForHumans($start, true);
    }
}
