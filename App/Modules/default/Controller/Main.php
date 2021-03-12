<?php

namespace App\Controller;
use System\{View, Controller, Model, Lang, MERROR, Get};

class Main
{

	public function index()
	{
		MERROR::print('Error Content');

		View::load("default@index");
	}
}