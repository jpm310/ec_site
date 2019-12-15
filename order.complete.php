<?php
namespace ec;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [' cache ' => Bootstrap::CACHE_DIR]);

$context['ses'] = $_SESSION;

$template = $twig->loadTemplate('order.complete.html.twig');
$template->display($context);