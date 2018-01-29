<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
	// 登录页面
	public function create()
	{
		return view('sessions.create');
	}

	// 登录操作
	public function store(Request $request)
	{
		$credentials = $this->validate($request, [
			'email' => 'required|email|max:255',
			'password' => 'required'
		]);

		if (Auth::attempt($credentials, $request->has('remeber'))) {
			session()->flash('success', '欢迎回来！');
			return redirect()->route('users.show', [Auth::user()]);
		} else {
			session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
			return redirect()->back();
		}

		return;
	}

	// 退出登录
	public function destroy()
	{
		Auth::logout();
		session()->flash('success', '退出成功');
		return redirect('login');
	}
}
