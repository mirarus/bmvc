<?php

namespace App\Model;
use System\Model;

class Main
{

	public function settings()
	{
		return Model::DB()->from('settings')->all();
	}
}