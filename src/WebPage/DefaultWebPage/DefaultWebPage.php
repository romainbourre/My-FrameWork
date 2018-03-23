<?php

namespace WebPage\DefaultWebPage;

use System\Controller;
use System\Http\Response;

/**
 * Class DefaultWebPage
 * Default web page of framework
 * @package WebPage\DefaultWebPage
 */
class DefaultWebPage extends Controller {

    public function indexAction() {

        $content = $this->render('DefaultView');

        return new Response($content);

    }

}