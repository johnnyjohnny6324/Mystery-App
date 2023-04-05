<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Home extends BaseController
{
	public function welcome()
	{
		helper('html');
		$viewData = [];

			// Check for flash errors
			if (session('error')) {
				$viewData['error'] = session('error');
				$viewData['errorDetail'] = session('errorDetail');
			}
			

			// Check for logged on user
			if (session('userName'))
			{
				$viewData['userName'] = session('userName');
				$viewData['userEmail'] = session('userEmail');
				$viewData['userTimeZone'] = session('userTimeZone');
			}


		echo link_tag('css/app.css');
		//echo view('layout', $viewData);
		echo view('welcome', $viewData);
	}
}
