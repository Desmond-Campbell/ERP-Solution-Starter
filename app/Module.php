<?php

namespace App;

use App\Model;

class Module extends Model
{
  
  protected $table = 'module';
  protected $fillable = [ 'name', 'slug' ];

  public static $audit_class = "module";

}
