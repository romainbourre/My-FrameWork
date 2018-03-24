<?php

namespace WebPage\DefaultWebPage;

use System\Controller;
use System\Http\Response;

/**
 * Class DefaultWebPage
 * Default web page of framework
 * @package WebPage\DefaultWebPage
 * @author Romain Bourré
 */
class DefaultWebPage extends Controller {

    /**
     * @return Response
     */
    public function indexAction() {

        $content = $this->render('DefaultView');

        return new Response($content);

    }

}