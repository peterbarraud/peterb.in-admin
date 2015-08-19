<?php

		require_once 'dataobjectserver/application.php';
		$app = Application::getinstance();
		$user = $app->GetObjectById('appuser',2);
		$user->username = 'floop';
		$user->Publish();
		echo $user;

?>
