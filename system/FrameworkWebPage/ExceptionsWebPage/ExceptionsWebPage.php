<?php

namespace System\FrameworkWebPage\ExceptionsWebPage;

use System\Controller;
use System\Http\Response;

class ExceptionsWebPage extends Controller {

    public function indexAction(\Exception $e) {

        return new Response($this->render('exceptions-view', compact('e')));

    }

}