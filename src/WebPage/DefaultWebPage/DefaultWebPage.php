<?php

namespace WebPage\DefaultWebPage;

use System\Controller;
use System\Http\Request;
use System\Http\Response;

/**
 * Class DefaultWebPage
 * Default web page of framework
 * @package WebPage\DefaultWebPage
 * @author Romain Bourré
 */
class DefaultWebPage extends Controller {

    public function indexAction(Request $request) {

        return new Response($this->render('DefaultView'));

    }

}