<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
	protected $fillable = ['content'];
	/**
	 * 指明一条微博属于一个用户
	 * @access    public
	 * @author    zhaoyong
	 * @copyright 2018-01-30
	 * @param
	 * @return
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
