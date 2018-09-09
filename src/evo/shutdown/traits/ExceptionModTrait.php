<?php
namespace evo\shutdown\traits;

trait ExceptionModTrait {
       
    /**
     * checks if a vaiable is throwable
     * 
     * @param \Exception $e - @todo \Throwable after PHP7
     * @return bool
     */
    public function isTrowable($e){
        if (!is_a($e, \Exception::class) && !is_a($e, '\\Throwable', false)) {
            //PHP7 compatibility
            return false;
        }
        return true;
    }
    
    /**
     * Get stack trace suitable to for "testing" environment self::ENV_TESTING
     *
     * @param \Exception $e - @todo \Throwable after PHP7
     * @return array
     */
    public function getTrace($e){
        if(!$this->isTrowable($e)) return [];
        
        switch ($this->environment){
            case self::ENV_DEVELOPMENT:
                return $e->getTrace();
            case self::ENV_TESTING:
                $trace = $e->getTrace();
                array_walk($trace, function(&$call, $key) use (&$trace){
                    if(!isset($call['args'])) return;
                    $trace[$key]['args'] =  array_map(function($arg){
                        return ucfirst(gettype($arg));
                    },$call['args']);
                        
                });
                print_r($trace);
                return $trace;
            case self::ENV_PRODUCTION:
            default:
                return [];
                
        }
        
    }
    
    /**
     * Get printable stack trace suitable to for "testing" environment self::ENV_TESTING
     * 
     * Omits all .php from files and only basic argument info, to hide things like passwords
     *
     * @param \Exception $e - @todo \Throwable after PHP7
     * @return string
     */
    public function getTraceAsString($e){
        if(!$this->isTrowable($e)) return '';
        
        $trace = "";
        
        foreach ($e->getTrace() as $key => $value) {
            $file = isset($value['file']) ? preg_replace('/\.php$/', '', $value['file']): 'Unknown';
            $line = isset($value['line']) ?  $value['line'] : 'Unknown';
            $function = isset($value['function']) ?  $value['function'] : '';
            $class = isset($value['class']) ?  $value['class'] : '';
            $type = isset($value['type']) ?  $value['type'] : '';

            $args = isset($value['args']) ? array_map(function($arg){
                $type = gettype($arg);
                switch ($type){
                    case 'boolean':
                        return 'Boolean('.($arg ? 'true' : 'false').')';
                    case 'integer':
                        return 'Int('.$arg.')';
                    case 'double':
                        if (strlen($arg) == 1 || (strlen($arg) == 2 && $float < 0)) {
                            $float = number_format($arg, 1);
                        }
                        return 'Float('.$float.')';
                    case 'string':
                        return 'String('.strlen($arg).')';
                    case 'resource':
                    case 'resource (closed)':
                        return trim('Resource id #'.intval($arg).' '.get_resource_type($arg));
                    case 'NULL':
                        return 'Null';
                    case 'array':
                        return 'Array('.count($arg).')';
                    case 'object':
                        return 'Object('.get_class($arg).')';
                    case 'unknown type':
                    default:
                        return 'Undefined('.strlen($arg).')';
                }
                echo ucfirst(gettype($arg))."\n";
                return ucfirst(gettype($arg));
            },$value['args']) : '';

            $classFuntion = $class . $type . $function;

            if (empty($args)) {
                $args = '()';
            } else {
                $args = '('.implode(',',$args).')';
            }

            $trace .= "#{$key} {$file}({$line}): {$classFuntion}{$args}".PHP_EOL;
       }
            
       $trace .= '#'.(1+count($trace)).' {main}';
       
       return $trace;
    }
    
}


