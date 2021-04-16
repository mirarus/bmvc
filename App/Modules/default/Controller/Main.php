<?php

namespace App\Controller;
use BMVC\Core\{View, Controller, Model, MError, Request};
use BMVC\Libs\{Hash, Csrf};

class Main
{

	public function index()
	{

		// pr(Model::import('default/Main')->settings());

		MError::print("Example");

		View::load("default@index", [
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

		ob_start();
		echo "<br>";
		echo "Page Load Time: " . BMVC_LOAD . " Sn";; 
		echo "<br>";
		echo "Memory Usage: " . round(memory_get_usage() / 1024, 2) . " Kb";
		echo "<br>";
		$benchmark_area = ob_get_contents();
		ob_clean();
		MError::color("info")::print("Benchmark", $benchmark_area);
	}
}