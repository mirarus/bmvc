<?php

namespace App\Http\Model;
use BMVC\Core\Model;

class Main
{

	public function index()
	{
		return "Current Model [Main::index]";
	  //return Model::DB()->from('settings')->all();
	}
}