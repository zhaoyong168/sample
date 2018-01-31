<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use Auth;
class User extends Authenticatable
{
	use Notifiable;

	/**
	 * 在过滤用户提交的字段，只有包含在该属性中的字段才能够被正常更新
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * 当我们需要对用户密码或其它敏感信息在用户实例通过数组或 JSON 显示时进行隐藏，则可使用 hidden 属性
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	/**
	 * 由于一个用户拥有多条微博，因此在用户模型中我们使用了微博动态的复数形式
	 * @access    public
	 * @author    zhaoyong
	 * @copyright ${DATE}
	 * @param
	 * @return
	 */
	public function statuses()
	{
		return  $this->hasMany(Status::class);
	}

	public static function boot()
	{
		parent::boot();
		static::creating(function ($user) {
			$user->activation_token = str_random(30);
		});
	}

	public function gravatar($size = '100')
	{
		$hash = md5(strtolower(trim($this->attributes['email'])));
		return "http://www.gravatar.com/avatar/$hash?s=$size";
	}

	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ResetPassword($token));
	}

	public function feed()
	{
		$user_ids = Auth::user()->followings->pluck('id')->toArray();
		array_push($user_ids, Auth::user()->id);
		return Status::whereIn('user_id', $user_ids)
			->with('user')
			->orderBy('created_at', 'desc');
	}

	public function followers()
	{
		return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
	}

	public function followings()
	{
		return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
	}

	public function follow($user_ids)
	{
		if (!is_array($user_ids)) {
			$user_ids = compact('user_ids');
		}
		$this->followings()->sync($user_ids, false);
	}

	public function unfollow($user_ids)
	{
		if (!is_array($user_ids)) {
			$user_ids = compact('user_ids');
		}
		$this->followings()->detach($user_ids);
	}

	public function isFollowing($user_id)
	{
		return $this->followings->contains($user_id);
	}
}
