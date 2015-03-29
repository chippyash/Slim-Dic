<?php
/*
 * Chippyash Slim DIC integration
 * 
 * A slim MVC Controller
 * 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2014, UK
 */
namespace Slimdic\Controller;

use Slim\Slim;

/**
 * Base abstract controller
 */
abstract class AbstractController
{
    /**
     *
     * @var \Slim\Slim
     */
    protected $app;
    
    /**
     * DI Container
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $dic;
    
    /**
     * @var \Slim\Http\Request
     */
    protected $request;
    
    /**
     * @var \Slim\Http\Response
     */
    protected $response;
    
    /**
     * Arbitrary Controller Configuration
     * 
     * @var array
     */
    protected $config = [];
    
    /**
     * Constructor
     * 
     * @param Slim $app
     */
    public function __construct(Slim $app, array $config = [])
    {
        $this->app = $app;
        $this->dic = $app->dic;
        $this->request = $app->request();
        $this->response = $app->response();
        $this->config = $config;
    }
    
    /**
     * Route request to an action on this controller
     * 
     * @param string $actionName Name of action (minus 'Action' suffix)
     * @param array $params Arbitrary parameters to be sent to the action
     * @throws \Exception
     */
    public function route($actionName, array $params = [])
    {
        $aName = "{$actionName}Action";
        if (!method_exists($this, $aName)) {
            throw new \Exception('Invalid action name: ' . $actionName);
        }
        $this->$aName($params);
    }
    
    /**
     * @proxy app->render
     * 
     * @param  string $template The name of the template passed into the view's render() method
     * @param  array  $data     Associative array of data made available to the view
     * @param  int    $status   The HTTP response status code to use (optional)
     * @param  string $controller The controller name that acts as root for the template - default = current controller
     */
    protected function render($template, $data = array(), $status = null, $controller = null)
    {
        if (!is_null($controller)) {
            $tpl = "{$controller}/{$template}";
        } else {
            $ctrl = $this->controllerName();
            $tpl = "{$ctrl}/{$template}";
        }
        $this->app->render($tpl, $data, $status);
    }

    /**
     * Name of this controller
     * @return string
     */
    protected function controllerName()
    {
        $ctrlClassParts = explode('\\', get_class($this));
        return strtolower(str_replace('Controller', '', array_pop($ctrlClassParts)));
    }
}
