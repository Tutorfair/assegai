<?php

namespace assegai;

class ErrorController extends Controller
{
    /**
     * A very light view, just a glimpse of a template...
     */
    function glimpse($template, array $vars = array())
    {
        $vars = (object)$vars;
        ob_start();
        include($template);
        return ob_get_clean();
    }

    function notFoundHandler()
    {
        $e = $this->request->getException();

        if(isset($_SERVER['APPLICATION_ENV'])
        && $_SERVER['APPLICATION_ENV'] == 'development') {
            if(php_sapi_name() != 'cli') {
                $server = $this->server;
                return $this->glimpse('templates/notfoundview.phtml', array(
                    'exception' => $e,
                ));
            }
            else {
                echo "URL handler not found:\n";
                printf("Prefix: %s\n", $this->server->getPrefix());
                printf("Request: %s\n", $this->server->getRoute());
                printf("Routes:\n");
                foreach($e->getRoutes() as $route => $target) {
                    printf("\t%s => %s[%s]\n", $route, $target->getApp(), $target->getCall());
                }
                echo "\n\n";
            }
        } else {
            return new Response('Not found!', 404);
        }
    }

    protected function printTrace(\Exception $error)
    {
        $trace = $error->getTrace();
        $formatted_trace = array();
        $traceSize = count($trace);
        for($i = 0; $i < ($traceSize); $i++) {
            $line = '';
            if(true || strpos($trace[$i]['class'], 'assegai\\') === false) {
                $line = "$i - ";
                if(isset($trace[$i]['class'])) {
                    $line.= "at " . $trace[$i]['class'] . "::";
                }
                if(isset($trace[$i]['function'])) {
                    $line.= $trace[$i]['function'] . "() ";
                }

                $line .= sprintf("in %s on line %s",
                    (isset($trace[$i]['file']) ? $trace[$i]['file'] : '(no file)'),
                    (isset($trace[$i]['line']) ? $trace[$i]['line'] : '(no line)')
                );
            }
            $formatted_trace[] = $line;
        }

        return implode(PHP_EOL, $formatted_trace);
    }

    function errorHandler()
    {
        $e = $this->request->getException();

        if(isset($_SERVER['APPLICATION_ENV'])
            && ($_SERVER['APPLICATION_ENV'] == 'development' || $_SERVER['APPLICATION_ENV'] == 'docker-dev'))
        {
            if(php_sapi_name() != 'cli') {
                return $this->glimpse('templates/errorview.phtml', array(
                    'exception' => $e,
                ));
            }
            else {
                echo "An error occured while running your code:\n";
                printf("%s\n", $e->getMessage());
                printf("%s\n\n", $this->printTrace($e));
            }
        } else {
            return new Response(sprintf("Server error %d<br>", $e->getCode()), 500);
        }
    }
}
