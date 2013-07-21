<?php

/**
 * this class is intended to wrap an object of another class when you
 * need to override few methods dynamically and you cannot inherit
 * your class from required one. Here's a solution. Inherit a class
 * from this and declare in it methods which you want to hook with
 * prefix "hookm_" and protected access level.
 * 
 */
class HookWrap {
  private $host;
  private $hookMethods, $hookSet, $hookGet, $rcOfhost;
  private $calledMethod = null;
  private $argsOfCalledMethod = null;
  protected function getHost() { return $this->host; }
  public function __construct($host) {
    $this->host = $host;
    $this->hookMethods = array();
    $this->hookSet = array();
    $this->hookGet  = array();
    $this->rcOfhost = new ReflectionClass($host);
    $rc = new ReflectionClass($this);
    foreach ($rc->getMethods(ReflectionMethod::IS_PROTECTED) as $method) {
      if (preg_match('/^hookm_([A-z0-9_]+)$/', $method->name, $matches))
        $this->hookMethods[$matches[1]] = $method;
      elseif (preg_match('/^hookg_([A-z0-9_]+)$/', $method->name, $matches))
        $this->hookGet[$matches[1]] = $method;
      elseif (preg_match('/^hooks_([A-z0-9_]+)$/', $method->name, $matches))
        $this->hookSet[$matches[1]] = $method;                       
      // else usual function 
    }    
  }
  private function freeResource__Call($m) {
    $m->setAccessible(false);
    $this->calledMethod = null;
    $this->argsOfCalledMethod = null;    
  }
  public function __call($name,$args) {
    return $this->callHook($name,$args,$this->hookMethods, true);
  }
  private function callHook($name,$args,$hookTable,$isMethod) {
    if (isset($hookTable[$name])) {
      if ($isMethod) {
        $this->calledMethod = $name;
        $this->argsOfCalledMethod = $args;
      }
      $m = $hookTable[$name];
      // all hook method in your subclass is always protected because
      // because ... I want that. O! let say host object has method with same name as this class.
      // And you cannot change name in both places.
      // But I agree that direct method call is more faster than my solution.
      $m->setAccessible(true);
      try {
        $v = $m->invokeArgs($this,$args);
        $this->freeResource__Call($m);
        return $v;
      } catch (Exception $e) {
        $this->freeResource_Call($m);
        throw $e;
      }
    }
    // it can only call public method
    return call_user_func_array(array($this->host, $name), $args);    
  }
  /**
   * the method releases a capability of hooking access to object
   * variables. Define in your subclass 'hooks_varName' method to
   * hook access to an object variable with name 'varName'. 
   */
  public function __get($name) {
    if (isset($this->hookGet[$name]))
      return $this->callHook($name, array(), $this->hookGet, false);
    return $this->host->$name;
  }
  public function __set($name,$value) {
    if (isset($this->hookSet[$name]))
      return $this->callHook($name, array($value), $this->hookSet, false);
    return $this->host->$name = $value;
  }
  /**
   * the method is an analogue of parent::method.
   * It allows to call overriden method from its hook.
   */
  protected function callBaseMethodArgs($args) {
    // method of host object is always public
    if ($this->calledMethod === null)
      throw new Exception('callBaseMethodArgs was called from not a hook');
    $m = null;
    try {
      $m = $this->rcOfhost->getMethod($this->calledMethod);
    } catch (Exception $e) {
      throw new Exception('host object hasn\'t go method \''
                          . $this->calledMethod . '\'');
    }
    if ($m->isPrivate() or $m->isProtected())
      throw new Exception("Method '" . $m->class . '::'
                          . $m->name . "' is not public");
    return $m->invokeArgs($this->host, $args);
  }
  /**
   * see callBaseMethodArgs
   * @param any all will be passed to an overriden method
   * @return mixed it returns that an overriden method returns
   */
  protected function callBaseMethod() {
    return $this->callBaseMethodArgs(func_get_args());
  }
  /**
   * @return array 
   */
  protected function getArgsOfCalledMethod() {
    return $this->argsOfCalledMethod;
  }
}
?>