<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** *   Replaces '_' with '-' in URL segment
 * @category CI core extension
 * @access public
 * @copyright (c) 2012, Rohitashva Singh
 * @version 1.0
 * @author Rohitashva Singh <rohitashvarathore@gmail.com>
 * 6 November 2012
 */

class MY_Router extends CI_Router
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function set_class($class) 
    {
        parent::set_class($this->_repl($class));
    }

    public function set_method($method) 
    {
        parent::set_method($this->_repl($method));
    }

    public function _validate_request($segments)
    {
        parent::_validate_request(array('permission','has_permission'));
        
        
        if (isset($segments[0]))
            $segments[0] = $this->_repl($segments[0]);
        if (isset($segments[1]))
            $segments[1] = $this->_repl($segments[1]);

        return parent::_validate_request($segments);
    }

    private function _repl($s)
    {
        return str_replace('-', '_', $s);
    }
}

/* End of file My_Router.php */
/* Location: .application/core/MY_Router.php */