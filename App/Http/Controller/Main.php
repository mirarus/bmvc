<?php

namespace App\Http\Controller;
use BMVC\Core\{View, Controller, Model};
use BMVC\Libs\{Lib, MError, Hash, Request, Csrf, Lang, Log, Session, Benchmark};
use Exception;

class Main
{

	public function index()
	{

		if (Request::getRequestMethod() == "POST") {
			throw new Exception('BMVC');
		}

	//	Log::error(["test", "bmvc"]);

	//
	//		pr(Session::get());
	//	
		pr(Controller::import('main')->tex());

		
		ob_start();
		$_REQUEST ? pr('Request:') . pr($_REQUEST) : null;
		$_GET ? pr('<hr>GET:') . pr($_GET) : null;
		$_POST ? pr('<hr>POST:') . pr($_POST) : null;
		$Request = ob_get_contents();
		ob_clean();
		Lib::MError()::color("warning")::print("Request - " . Request::getRequestMethod(), $Request);

		pr(Lang::___("error"));

		pr(Model::import('main')->index());

		MError::print("Example");

		View::load("test@index", [
			'title' => 'BMVC'
		]);

		echo "<br>";

		#
		MError::color("danger")::print("Password Hash", Hash::make("BMVC"));
		#
		#
		ob_start();
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

		MError::color("info")::print("Benchmark", Benchmark::memory(true));
	}

	function tex()
	{
		return "Current Controller [Main::tex]";
	}
}