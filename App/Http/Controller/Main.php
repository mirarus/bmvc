<?php

namespace App\Controller;
use BMVC\Core\{View, Controller, Model};
use BMVC\Libs\{Lib, MError, Hash, Request, Csrf, Lang, Log, Session, Benchmark};

class Main
{

	public function index()
	{

	//	Log::error(["test", "bmvc"]);

	//
	//		pr(Session::get());
	//	
		pr(Controller::import('main')->tex());

		
		ob_start();
		Request::request() ? pr('Request:') . pr(Request::request()) : null;
		Request::get() ? pr('<hr>GET:') . pr(Request::get()) : null;
		Request::post() ? pr('<hr>POST:') . pr(Request::post()) : null;
		$Request = ob_get_contents();
		ob_clean();
		Lib::MError()::color("warning")::print("Request", $Request);

		pr(Lang::___("error"));

		pr(Model::import('main')->index());

		MError::print("Example");

		View::load("test@index", [
			'title' => 'BMVC'
		]);

		echo "<br>";

		#
		ob_start();
		echo Hash::make("ass");
		$pasword_hash_area = ob_get_contents();
		ob_clean();
		MError::color("danger")::print("Password Hash", $pasword_hash_area);
		#
		#
		ob_start();
		pr(Request::post());

		if (Csrf::verify()) {
			echo "Result: Pass";
		} else {
			echo "Result: Fail";
		} ?>
		<br><br>
		<form action="" method="post">
			<?php echo Csrf::input(); ?>
			<input type="submit">
		</form>
		<?php
		$csrf_area = ob_get_contents();
		ob_clean();
		MError::color("success")::print("CSRF", $csrf_area);
		#

		MError::color("info")::print("Benchmark", "Memory Usage: " . Benchmark::memory());
	}

	function tex()
	{
		return "Current Controller [Main::tex]";
	}
}