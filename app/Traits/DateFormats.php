<?php
namespace App\Traits;

use Illuminate\Support\Carbon;

trait DateFormats
{
    public function getCreatedAtForHumansAttribute()
    {
        return $this->created_at->diffForHumans();
    }


    public function getUpdatedAtForHumansAttribute()
    {
        return $this->updated_at->diffForHumans();
    }


    public function getCreatedAtForHumansCarbonAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }


    public function getUpdatedAtForHumansCarbonAttribute()
    {
        Carbon::setLocale('uk');
        return Carbon::parse($this->updated_at)->diffForHumans();
    }


    public function getCreatedAtFormatterAttribute()
    {
        $formatter = new \IntlDateFormatter('uk_UK', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL);
        $formatter->setPattern('d MMM y');

        return $formatter->format(new \DateTime($this->created_at));
    }


    public function getUpdatedAtFormatterAttribute()
    {
        $formatter = new \IntlDateFormatter('uk_UK', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL);
        $formatter->setPattern('d MMM y');

        return $formatter->format(new \DateTime($this->updated_at));
    }

}
