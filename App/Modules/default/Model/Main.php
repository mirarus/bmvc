<?php

namespace App\Model;
use Model;

class Main
{

	public function settings()
	{
		return Model::DB()->from('settings')->all();
	}
}