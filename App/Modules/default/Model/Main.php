<?php

namespace App\Model;
use BMVC\Core\Model;

class Main
{

	public function settings()
	{
		return Model::DB()->from('settings')->all();
	}
}