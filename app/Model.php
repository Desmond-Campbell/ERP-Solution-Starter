<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Config, Setting;

class Model extends Eloquent
{
    /**
   * Display timestamps in user's timezone
   */
  protected function asDateTime($value)
  {
      
    if ($value instanceof Carbon) {
      return $value;
    }

    $tz = Config::get( 'settings.timezone_hours', '0' );

    $value = parent::asDateTime($value);

    return $value->addHours($tz);
  
  }

}