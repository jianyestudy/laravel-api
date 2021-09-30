<?php
/**
 * User: Edward Yu
 * Date: 2021/9/9

 */

namespace QCS\LaravelApi\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    //过白
    protected $guarded = [];

    //格式化时间戳
    protected function  serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
