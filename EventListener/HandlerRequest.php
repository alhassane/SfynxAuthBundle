<?php
/**
 * This file is part of the <Auth> project.
 *
 * @category   EventListener
 * @package    Handler
 * @subpackage Request
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       https://github.com/pigroupe/cmf-sfynx/blob/master/web/COPYING.txt
 * @since      2014-07-18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sfynx\ToolBundle\Util\PiFileManager;

/**
 * Custom request handler.
 * Register the mobile/desktop format.
 *
 * @category   EventListener
 * @package    Handler
 * @subpackage Request
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       https://github.com/pigroupe/cmf-sfynx/blob/master/web/COPYING.txt
 * @since      2014-07-18
 */
class HandlerRequest
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    
    /**
     * @var \Sfynx\CmfBundle\Lib\Browscap
     */
    protected $browscap;    
    
    /**
     * @var \Sfynx\CmfBundle\Lib\MobileDetect
     */
    protected $mobiledetect;    
    
    /**
     * Constructor.
     *
     * @param ContainerInterface $container The service container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container; 
    }    

    /**
     * Invoked to modify the controller that should be executed.
     *
     * @param GetResponseEvent $event The event
     * 
     * @access public
     * @return void
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */    
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            // ne rien faire si ce n'est pas la requête principale
            return;
        }        
        // Set request
        $this->request = $event->getRequest($event);
        // Sets parameter template values.
        $this->setParams();
        //
        if ($this->request->cookies->has('sfynx-layout')) {
            $this->layout = $this->request->cookies->get('sfynx-layout');
        } else {
            $this->layout = $this->container->getParameter('sfynx.auth.theme.layout.front.pc') . $this->init_pc_layout;
        }
        if ($this->request->cookies->has('sfynx-screen')) {
            $this->screen = $this->request->cookies->get('sfynx-screen');
        } else {
            $this->screen = "layout";
        }
        if ($this->is_browser_authorized 
                && !$this->request->cookies->has('sfynx-layout')
                && $this->container->has("sfynx.browser.lib.mobiledetect") 
                && $this->container->has("sfynx.browser.lib.browscap")
        ) {
            // we get the browser
            PiFileManager::mkdirr($this->browscap_cache_dir, 0777);
            if ($this->request->attributes->has('sfynx-browser')) {
                $this->browser = $this->request->attributes->get('sfynx-browser');
            } else {
                $this->browser = $this->container->get("sfynx.browser.lib.browscap")->getBrowser();
            }
            if ($this->request->attributes->has('sfynx-mobiledetect')) {
            	$this->mobiledetect = $this->request->attributes->get('sfynx-mobiledetect');
            } else {
            	$this->mobiledetect = $this->container->get("sfynx.browser.lib.mobiledetect");
            }
            //
            $this->request->attributes->set('sfynx-browser', $this->browser);
            $this->request->attributes->set('sfynx-mobiledetect', $this->mobiledetect);
            //
            if ($this->browser->isMobileDevice) {
                if (!$this->mobiledetect->isTablet()) {
                    $this->screen = "layout-poor";
                } elseif ($this->mobiledetect->isTablet()) {
                    $this->screen = "layout-medium";
                } else {
                    $this->screen = 'layout-medium';
                }
                $this->layout = $this->container->getParameter('sfynx.auth.theme.layout.front.mobile') . $this->init_mobile_layout.'\\' . $this->screen . '.html.twig';
                $this->request->setRequestFormat('mobile');
            }
        }        
        // we add sfynx-layout and sfynx-screen info in the request
        $this->request->attributes->set('sfynx-layout', $this->layout);
        $this->request->attributes->set('sfynx-screen', $this->screen);
    }     
    
    /**
     * Sets parameter template values.
     *
     * @access protected
     * @return void
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function setParams()
    {
    	$this->init_pc_layout         = $this->container->getParameter('sfynx.auth.layout.init.pc.template');
    	$this->init_mobile_layout     = $this->container->getParameter('sfynx.auth.layout.init.mobile.template');
    	$this->is_browser_authorized  = $this->container->getParameter("sfynx.auth.browser.switch_layout_mobile_authorized");
    	//    	
    	if ($this->container->has("sfynx.browser.lib.mobiledetect") 
                && $this->container->hasParameter("sfynx.browser.browscap.cache_dir")
        ) {
    	    $this->browscap_cache_dir  = $this->container->getParameter("sfynx.browser.browscap.cache_dir");
    	} else {
    	    $this->browscap_cache_dir  = null;
    	}
    }   
}
